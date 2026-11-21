<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Factura;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Recorre las pantallas web con cada rol y verifica:
 *  - que cada pantalla carga (200) para quien tiene permiso,
 *  - que se niega (403/404) a quien no lo tiene,
 *  - y los flujos principales de cada módulo (crear, aprobar, facturar…).
 *
 * Ejecutar: php artisan test --filter=PantallasWebTest
 */
class PantallasWebTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $recepcionista;
    private Usuario $estilista;
    private Usuario $cliente;
    private Usuario $otroCliente;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin         = $this->crearUsuario('Administrador', 'admin@prueba.com');
        $this->recepcionista = Usuario::where('email', 'laura.recepcion@example.com')->firstOrFail();
        $this->estilista     = Usuario::where('email', 'andres.estilista@example.com')->firstOrFail();
        $this->cliente       = $this->crearUsuario('Cliente', 'cliente@prueba.com');
        $this->otroCliente   = $this->crearUsuario('Cliente', 'otro@prueba.com');

        Cliente::create(['usuario_id' => $this->cliente->usuario_id]);
        Cliente::create(['usuario_id' => $this->otroCliente->usuario_id]);
    }

    private function crearUsuario(string $rol, string $email): Usuario
    {
        return Usuario::create([
            'rol_id'    => Rol::where('nombre', $rol)->value('rol_id'),
            'nombre'    => $rol,
            'apellido'  => 'Prueba',
            'documento' => (string) random_int(10000000, 99999999),
            'email'     => $email,
            'password'  => 'Clave1234',
            'estado'    => true,
        ]);
    }

    /** Todas las pantallas GET con un registro de ejemplo de cada módulo. */
    private function pantallas(): array
    {
        $cliente   = Cliente::firstOrFail()->cliente_id;
        $servicio  = Servicio::firstOrFail()->servicio_id;
        $empleado  = Empleado::firstOrFail()->empleado_id;
        $proveedor = Proveedor::firstOrFail()->proveedor_id;
        $producto  = Producto::firstOrFail()->producto_id;
        $pago      = Pago::firstOrFail()->pago_id;
        $factura   = Factura::firstOrFail()->factura_id;
        $cita      = Cita::firstOrFail()->citas_id;

        $movimiento = MovimientoInventario::create([
            'tipo' => 'entrada', 'cantidad' => 3, 'fecha_hora' => now(), 'motivo' => 'Compra', 'producto_id' => $producto,
        ])->movimiento_id;

        return [
            'home' => '/',
            'citas.index' => '/citas', 'citas.create' => '/citas/nueva', 'citas.edit' => "/citas/$cita/editar",
            'clientes.index' => '/clientes', 'clientes.create' => '/clientes/nuevo',
            'clientes.show' => "/clientes/$cliente", 'clientes.edit' => "/clientes/$cliente/editar",
            'servicios.index' => '/servicios', 'servicios.create' => '/servicios/nuevo',
            'servicios.show' => "/servicios/$servicio", 'servicios.edit' => "/servicios/$servicio/editar",
            'empleados.index' => '/empleados', 'empleados.create' => '/empleados/nuevo',
            'empleados.show' => "/empleados/$empleado", 'empleados.edit' => "/empleados/$empleado/editar",
            'pagos.index' => '/pagos', 'pagos.create' => '/pagos/nuevo', 'pagos.show' => "/pagos/$pago",
            'facturas.index' => '/facturas', 'facturas.create' => '/facturas/nuevo',
            'facturas.show' => "/facturas/$factura", 'facturas.edit' => "/facturas/$factura/editar",
            'proveedores.index' => '/proveedores', 'proveedores.create' => '/proveedores/nuevo',
            'proveedores.show' => "/proveedores/$proveedor", 'proveedores.edit' => "/proveedores/$proveedor/editar",
            'productos.index' => '/productos', 'productos.create' => '/productos/nuevo',
            'productos.show' => "/productos/$producto", 'productos.edit' => "/productos/$producto/editar",
            'movimientos.index' => '/movimientos', 'movimientos.create' => '/movimientos/nuevo',
            'movimientos.show' => "/movimientos/$movimiento", 'movimientos.edit' => "/movimientos/$movimiento/editar",
            'usuarios.index' => '/usuarios', 'usuarios.create' => '/usuarios/nuevo',
            'usuarios.edit' => '/usuarios/' . $this->recepcionista->usuario_id . '/editar',
        ];
    }

    public function test_el_administrador_puede_abrir_todas_las_pantallas(): void
    {
        $this->actingAs($this->admin);

        foreach ($this->pantallas() as $nombre => $url) {
            $respuesta = $this->get($url);
            $this->assertSame(200, $respuesta->status(), "La pantalla $nombre ($url) respondió {$respuesta->status()}");
        }
    }

    public function test_la_recepcionista_gestiona_clientes_y_citas_pero_no_caja_inventario_ni_usuarios(): void
    {
        $this->actingAs($this->recepcionista);
        $p = $this->pantallas();

        foreach (['home', 'citas.index', 'citas.create', 'citas.edit', 'clientes.index', 'clientes.create',
                  'clientes.show', 'clientes.edit', 'servicios.index', 'servicios.show', 'empleados.index'] as $nombre) {
            $this->get($p[$nombre])->assertOk();
        }

        foreach (['servicios.create', 'empleados.create', 'pagos.index', 'facturas.index', 'proveedores.index',
                  'productos.index', 'movimientos.index', 'usuarios.index'] as $nombre) {
            $this->get($p[$nombre])->assertForbidden();
        }
    }

    public function test_la_estilista_solo_consulta_la_agenda(): void
    {
        $this->actingAs($this->estilista);
        $cita = Cita::firstOrFail();

        $this->get('/citas')->assertOk();
        $this->get('/clientes')->assertForbidden();

        // Antes podía editar cualquier cita: ahora la pantalla la devuelve a la agenda…
        $this->get("/citas/{$cita->citas_id}/editar")->assertRedirect('/citas');

        // …y el servidor rechaza el cambio aunque se envíe el formulario a mano.
        $this->put("/citas/{$cita->citas_id}", [
            'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00', 'estado' => 'completada',
            'cliente_id' => $cita->cliente_id, 'empleado_id' => $cita->empleado_id, 'servicio_id' => $cita->servicio_id,
        ])->assertForbidden();
    }

    public function test_el_cliente_ve_solo_lo_suyo(): void
    {
        $this->actingAs($this->cliente);
        $propio = $this->cliente->cliente;
        $ajeno  = $this->otroCliente->cliente;

        $this->get('/')->assertOk()->assertSee('Tus próximas citas');
        foreach (['/citas', '/citas/nueva', '/servicios', '/pagos', '/facturas', "/clientes/{$propio->cliente_id}"] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get("/clientes/{$ajeno->cliente_id}")->assertNotFound();
        foreach (['/clientes', '/productos', '/usuarios', '/pagos/nuevo', '/facturas/nuevo', '/servicios/nuevo'] as $url) {
            $this->get($url)->assertForbidden();
        }

        // El menú no ofrece módulos internos.
        $this->get('/')->assertDontSee('Proveedores')->assertDontSee('>Usuarios<', false);
    }

    public function test_el_cliente_agenda_siempre_a_su_nombre_y_como_pendiente(): void
    {
        $this->actingAs($this->cliente);
        $empleado = Empleado::where('disponibilidad', true)->firstOrFail();

        $this->post('/citas', [
            'fecha' => now()->addDays(2)->toDateString(), 'hora' => '11:00',
            'estado' => 'confirmada',                                   // intento de saltarse la confirmación
            'cliente_id' => $this->otroCliente->cliente->cliente_id,    // intento de agendar a nombre de otro
            'empleado_id' => $empleado->empleado_id,
            'servicio_id' => Servicio::where('activo', true)->value('servicio_id'),
        ])->assertRedirect('/citas');

        $cita = Cita::latest('citas_id')->firstOrFail();
        $this->assertSame($this->cliente->cliente->cliente_id, $cita->cliente_id);
        $this->assertSame('pendiente', $cita->estado);

        $this->patch("/citas/{$cita->citas_id}/cancelar")->assertRedirect();
        $this->assertSame('cancelada', $cita->fresh()->estado);
    }

    public function test_flujo_de_inventario(): void
    {
        $this->actingAs($this->admin);

        $this->post('/proveedores', ['nombre' => 'Belleza SAS', 'registro_tributario' => '900111222-3', 'email' => 'ventas@belleza.com'])
            ->assertRedirect();
        $proveedor = Proveedor::where('registro_tributario', '900111222-3')->firstOrFail();

        $this->post('/productos', [
            'nombre' => 'Tinte rubio', 'cantidad' => 10, 'precio' => 25000,
            'fecha_registro' => now()->toDateString(), 'proveedor_id' => $proveedor->proveedor_id,
        ])->assertRedirect();
        $producto = Producto::where('nombre', 'Tinte rubio')->firstOrFail();

        // El formulario envía la fecha como datetime-local (AAAA-MM-DDTHH:MM).
        $this->post('/movimientos', [
            'producto_id' => $producto->producto_id, 'tipo' => 'salida', 'cantidad' => 2,
            'fecha_hora' => now()->format('Y-m-d\TH:i'), 'motivo' => 'Uso en servicio',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('movimientoinventario', ['producto_id' => $producto->producto_id, 'tipo' => 'salida']);

        // Un proveedor con productos no se elimina.
        $this->delete("/proveedores/{$proveedor->proveedor_id}")->assertSessionHas('error');
    }

    public function test_flujo_de_caja_pago_y_factura(): void
    {
        $this->actingAs($this->admin);
        $cita = Cita::firstOrFail();

        $this->post('/pagos', [
            'codigo_cita' => $cita->codigo_cita, 'monto' => 45000, 'metodo' => 'efectivo',
            'fecha_hora' => now()->format('Y-m-d\TH:i'), 'estado' => 'aprobado',
        ])->assertRedirect();

        $pago = Pago::latest('pago_id')->firstOrFail();
        $this->assertSame('pendiente', $pago->estado, 'Un pago nuevo siempre nace pendiente.');

        $this->patch("/pagos/{$pago->pago_id}/estado", ['estado' => 'aprobado'])->assertRedirect();
        $this->assertSame('aprobado', $pago->fresh()->estado);

        // Un pago aprobado ya no se edita.
        $this->get("/pagos/{$pago->pago_id}/editar")->assertRedirect("/pagos/{$pago->pago_id}");

        $this->post('/facturas', [
            'cliente_id' => $this->cliente->cliente->cliente_id,
            'fecha_hora' => now()->format('Y-m-d\TH:i'), 'impuestos' => 1000,
        ])->assertRedirect();
        $factura  = Factura::latest('factura_id')->firstOrFail();
        $servicio = Servicio::where('activo', true)->firstOrFail();

        $this->post("/facturas/{$factura->factura_id}/servicios", ['servicio_id' => $servicio->servicio_id, 'cantidad' => 2])
            ->assertRedirect();

        $factura->refresh();
        $this->assertEquals($servicio->precio * 2, (float) $factura->subtotal);
        $this->assertEquals($servicio->precio * 2 + 1000, (float) $factura->total);

        // El cliente ve su factura; el otro cliente no.
        $this->actingAs($this->cliente)->get("/facturas/{$factura->factura_id}")->assertOk();
        $this->actingAs($this->otroCliente)->get("/facturas/{$factura->factura_id}")->assertNotFound();
    }

    public function test_el_administrador_crea_personal_y_no_puede_quitarse_su_rol(): void
    {
        $this->actingAs($this->admin);
        $rolEstilista = Rol::where('nombre', 'Estilista')->value('rol_id');

        $this->post('/usuarios', [
            'rol_id' => $rolEstilista, 'nombre' => 'Nueva', 'apellido' => 'Estilista', 'documento' => '55555555',
            'email' => 'nueva@prueba.com', 'password' => 'Clave1234', 'estado' => '1',
        ])->assertRedirect('/usuarios');
        $this->assertSame($rolEstilista, Usuario::where('email', 'nueva@prueba.com')->value('rol_id'));

        // Ascender a un cliente a recepcionista.
        $rolRecepcion = Rol::where('nombre', 'Recepcionista')->value('rol_id');
        $this->put("/usuarios/{$this->cliente->usuario_id}", [
            'rol_id' => $rolRecepcion, 'nombre' => 'Cliente', 'apellido' => 'Prueba', 'documento' => $this->cliente->documento,
            'email' => $this->cliente->email, 'estado' => '1',
        ])->assertRedirect('/usuarios');
        $this->assertSame($rolRecepcion, $this->cliente->fresh()->rol_id);

        // El administrador no puede desactivarse a sí mismo.
        $this->put("/usuarios/{$this->admin->usuario_id}", [
            'rol_id' => $this->admin->rol_id, 'nombre' => 'Administrador', 'apellido' => 'Prueba',
            'documento' => $this->admin->documento, 'email' => $this->admin->email, 'estado' => '0',
        ])->assertSessionHas('error');
        $this->assertTrue((bool) $this->admin->fresh()->estado);

        // Nadie más puede entrar a la gestión de usuarios.
        $this->actingAs($this->recepcionista)->get('/usuarios')->assertForbidden();
    }

    public function test_el_registro_publico_ignora_un_rol_enviado_a_mano(): void
    {
        $this->post('/register', [
            'nombre' => 'Intrusa', 'apellido' => 'Prueba', 'documento' => '77777777', 'email' => 'intrusa@prueba.com',
            'password' => 'Clave1234', 'password_confirmation' => 'Clave1234',
            'rol_id' => Rol::where('nombre', 'Administrador')->value('rol_id'),
        ])->assertRedirect('/');

        $this->assertSame('Cliente', Usuario::where('email', 'intrusa@prueba.com')->firstOrFail()->rol->nombre);
    }
}
