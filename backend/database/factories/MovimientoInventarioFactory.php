<?php

namespace Database\Factories;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimientoInventario>
 */
class MovimientoInventarioFactory extends Factory
{
    protected $model = MovimientoInventario::class;

    public function definition(): array
    {
        $tipo = fake()->randomElement(['entrada', 'salida']);

        return [
            'tipo'        => $tipo,
            'cantidad'    => fake()->numberBetween(1, 20),
            'fecha_hora'  => fake()->dateTimeBetween('-2 months', 'now'),
            'motivo'      => $tipo === 'entrada' ? 'Reposición de stock' : 'Uso en servicio',
            'producto_id' => Producto::factory(),
        ];
    }
}
