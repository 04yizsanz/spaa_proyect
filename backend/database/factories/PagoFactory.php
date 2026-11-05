<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Ojo: la FK de pagos apunta a citas.codigo_cita (no a citas_id),
 * por eso se toma explícitamente el codigo_cita.
 *
 * @extends Factory<Pago>
 */
class PagoFactory extends Factory
{
    protected $model = Pago::class;

    public function definition(): array
    {
        return [
            'monto'       => fake()->numberBetween(20, 250) * 1000,
            'metodo'      => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia', 'pse']),
            'fecha_hora'  => fake()->dateTimeBetween('-2 months', 'now'),
            'estado'      => 'aprobado',
            'codigo_cita' => fn () => Cita::factory()->create()->codigo_cita,
        ];
    }
}
