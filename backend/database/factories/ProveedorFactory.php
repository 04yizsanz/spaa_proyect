<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proveedor>
 */
class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        return [
            'nombre'              => fake()->company(),
            'contacto'            => fake()->name(),
            'email'               => fake()->unique()->companyEmail(),
            // Formato tipo NIT: 9 dígitos + dígito de verificación
            'registro_tributario' => fake()->unique()->numerify('#########-#'),
        ];
    }
}
