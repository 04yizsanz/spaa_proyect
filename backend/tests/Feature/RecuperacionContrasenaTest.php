<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use App\Notifications\RestablecerContrasenaNotification;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * "Olvidé mi contraseña": envío del enlace, cambio de contraseña y casos de seguridad.
 *
 * Ejecutar: php artisan test --filter=RecuperacionContrasenaTest
 */
class RecuperacionContrasenaTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);

        $this->usuario = Usuario::create([
            'rol_id'    => Rol::where('nombre', 'Cliente')->value('rol_id'),
            'nombre'    => 'Dolly',
            'apellido'  => 'Prueba',
            'documento' => '12345678',
            'email'     => 'dolly@prueba.com',
            'password'  => Hash::make('Antigua123'),
            'estado'    => true,
        ]);
    }

    /** Pide el enlace y devuelve el token que viajó en el correo. */
    private function pedirEnlace(string $email = 'dolly@prueba.com'): ?string
    {
        $token = null;

        $this->post('/olvide-contrasena', ['email' => $email]);

        Notification::assertSentTo($this->usuario, RestablecerContrasenaNotification::class,
            function (RestablecerContrasenaNotification $n) use (&$token) {
                $token = $n->token;

                return true;
            });

        return $token;
    }

    public function test_el_login_muestra_el_enlace_y_las_pantallas_cargan(): void
    {
        $this->get('/login')->assertOk()->assertSee('¿Olvidaste tu contraseña?');
        $this->get('/olvide-contrasena')->assertOk()->assertSee('¿Olvidaste tu contraseña?');
        $this->get('/restablecer-contrasena/token-de-prueba?email=dolly@prueba.com')
            ->assertOk()->assertSee('Crea tu nueva contraseña');
    }

    public function test_flujo_completo_cambia_la_contrasena_y_permite_entrar(): void
    {
        Notification::fake();
        $token = $this->pedirEnlace();

        $this->post('/restablecer-contrasena', [
            'token' => $token, 'email' => 'dolly@prueba.com',
            'password' => 'Nueva12345', 'password_confirmation' => 'Nueva12345',
        ])->assertRedirect('/login')->assertSessionHas('success');

        $this->assertTrue(Hash::check('Nueva12345', $this->usuario->fresh()->password));

        // Entra con la nueva contraseña…
        $this->post('/login', ['email' => 'dolly@prueba.com', 'password' => 'Nueva12345'])->assertRedirect('/');
        $this->assertAuthenticatedAs($this->usuario->fresh());

        // …y el enlace ya no sirve una segunda vez.
        $this->post('/logout');
        $this->post('/restablecer-contrasena', [
            'token' => $token, 'email' => 'dolly@prueba.com',
            'password' => 'Otra123456', 'password_confirmation' => 'Otra123456',
        ])->assertSessionHas('error');
        $this->assertTrue(Hash::check('Nueva12345', $this->usuario->fresh()->password));
    }

    public function test_no_revela_si_un_correo_esta_registrado(): void
    {
        Notification::fake();

        $existe   = $this->post('/olvide-contrasena', ['email' => 'dolly@prueba.com']);
        $noExiste = $this->post('/olvide-contrasena', ['email' => 'nadie@prueba.com']);

        // Misma respuesta en ambos casos.
        $existe->assertSessionHas('enlace_enviado', true);
        $noExiste->assertSessionHas('enlace_enviado', true);
        Notification::assertSentTimes(RestablecerContrasenaNotification::class, 1);
    }

    public function test_una_cuenta_inactiva_no_recibe_enlace(): void
    {
        Notification::fake();
        $this->usuario->update(['estado' => false]);

        $this->post('/olvide-contrasena', ['email' => 'dolly@prueba.com'])->assertSessionHas('enlace_enviado');

        Notification::assertNothingSent();
    }

    public function test_un_token_falso_o_vencido_no_cambia_la_contrasena(): void
    {
        Notification::fake();
        $token = $this->pedirEnlace();

        // Token inventado.
        $this->post('/restablecer-contrasena', [
            'token' => 'inventado', 'email' => 'dolly@prueba.com',
            'password' => 'Nueva12345', 'password_confirmation' => 'Nueva12345',
        ])->assertSessionHas('error');

        // Token real pero vencido (más de 60 minutos después).
        $this->travel(61)->minutes();
        $this->post('/restablecer-contrasena', [
            'token' => $token, 'email' => 'dolly@prueba.com',
            'password' => 'Nueva12345', 'password_confirmation' => 'Nueva12345',
        ])->assertSessionHas('error');

        $this->assertTrue(Hash::check('Antigua123', $this->usuario->fresh()->password));
    }

    public function test_la_nueva_contrasena_debe_cumplir_las_reglas(): void
    {
        Notification::fake();
        $token = $this->pedirEnlace();

        $this->post('/restablecer-contrasena', [
            'token' => $token, 'email' => 'dolly@prueba.com',
            'password' => 'debil', 'password_confirmation' => 'debil',
        ])->assertSessionHasErrors('password');

        $this->post('/restablecer-contrasena', [
            'token' => $token, 'email' => 'dolly@prueba.com',
            'password' => 'Nueva12345', 'password_confirmation' => 'Distinta123',
        ])->assertSessionHasErrors('password');
    }

    public function test_el_token_se_guarda_cifrado(): void
    {
        Notification::fake();
        $token = $this->pedirEnlace();

        $guardado = \DB::table('password_reset_tokens')->where('email', 'dolly@prueba.com')->value('token');

        $this->assertNotSame($token, $guardado);
        $this->assertTrue(Hash::check($token, $guardado));
    }

    public function test_limita_las_solicitudes_seguidas(): void
    {
        Notification::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/olvide-contrasena', ['email' => 'dolly@prueba.com']);
        }
        $this->post('/olvide-contrasena', ['email' => 'dolly@prueba.com'])->assertStatus(429);

        // Aunque se pida 5 veces, solo se envía un correo (60 s entre envíos).
        Notification::assertSentTimes(RestablecerContrasenaNotification::class, 1);
    }
}
