<?php

namespace Database\Factories;

use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Servicio>
 */
class ServicioFactory extends Factory
{
    protected $model = Servicio::class;

    public function definition(): array
    {
        return [
            'nombre'       => fake()->randomElement([
                'Corte de cabello', 'Tinte completo', 'Manicure', 'Pedicure',
                'Limpieza facial', 'Masaje relajante', 'Keratina', 'Depilación con cera',
            ]),
            'duracion_min' => fake()->randomElement([30, 45, 60, 90, 120]),
            'precio'       => fake()->numberBetween(20, 250) * 1000,
            'descripcion'  => fake()->sentence(10),
            'activo'       => true,
        ];
    }
}
