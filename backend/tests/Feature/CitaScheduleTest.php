<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CitaScheduleTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $now = now();
        $rolId = DB::table('roles')->insertGetId([
            'nombre' => 'Recepcionista',
            'descripcion' => 'Usuario de prueba.',
            'estado' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->usuario = Usuario::create([
            'rol_id' => $rolId,
            'nombre' => 'Prueba',
            'apellido' => 'Usuario',
            'documento' => '123456789',
            'email' => 'prueba@example.com',
            'password' => 'password',
            'estado' => true,
        ]);

        $this->actingAs($this->usuario);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_create_form_sets_today_as_the_earliest_date_and_business_hours(): void
    {
        $this->get(route('citas.create'))
            ->assertOk()
            ->assertSee('min="' . now()->toDateString() . '"', false)
            ->assertSee('min="09:00"', false)
            ->assertSee('max="21:00"', false);
    }

    public function test_past_dates_are_rejected(): void
    {
        $this->from(route('citas.create'))
            ->post(route('citas.store'), [
                'codigo_cita' => 999001,
                'fecha' => now()->subDay()->toDateString(),
                'hora' => '10:00',
            ])
            ->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('fecha');
    }

    public function test_appointments_outside_business_hours_are_rejected(): void
    {
        $this->from(route('citas.create'))
            ->post(route('citas.store'), [
                'codigo_cita' => 999002,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '08:59',
            ])
            ->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('hora');
    }

    public function test_appointments_after_closing_time_are_rejected(): void
    {
        $this->from(route('citas.create'))
            ->post(route('citas.store'), [
                'codigo_cita' => 999004,
                'fecha' => now()->addDay()->toDateString(),
                'hora' => '21:01',
            ])
            ->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('hora');
    }

    public function test_past_times_today_are_rejected(): void
    {
        Carbon::setTestNow(Carbon::parse('2030-04-25 12:00:00', config('app.timezone')));

        $this->from(route('citas.create'))
            ->post(route('citas.store'), [
                'codigo_cita' => 999003,
                'fecha' => now()->toDateString(),
                'hora' => '11:59',
            ])
            ->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('hora');
    }
}
