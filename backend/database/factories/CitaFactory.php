<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * El estado de la cita depende de la fecha:
 * citas pasadas -> completada o cancelada; citas futuras -> pendiente o confirmada.
 *
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    protected $model = Cita::class;

    public function definition(): array
    {
        $fecha  = fake()->dateTimeBetween('-2 months', '+1 month');
        $pasada = $fecha < now()->startOfDay();

        return [
            'codigo_cita' => fake()->unique()->numberBetween(100000, 999999),
            'fecha'       => $fecha->format('Y-m-d'),
            'hora'        => fake()->randomElement([
                '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00',
                '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00',
            ]),
            'estado'      => $pasada
                ? fake()->randomElement(['completada', 'completada', 'completada', 'cancelada'])
                : fake()->randomElement(['pendiente', 'confirmada']),
            'cliente_id'  => Cliente::factory(),
            'empleado_id' => Empleado::factory(),
            'servicio_id' => Servicio::factory(),
        ];
    }
}
