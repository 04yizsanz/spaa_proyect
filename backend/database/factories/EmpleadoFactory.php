<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
{
    protected $model = Empleado::class;

    public function definition(): array
    {
        return [
            'nombre'             => fake()->firstName(),
            'apellido'           => fake()->lastName(),
            'documento'          => fake()->unique()->numerify('##########'),
            'correo'             => fake()->unique()->safeEmail(),
            'telefono'           => fake()->numerify('3#########'),
            // Valores exactos del enum de la migración
            'rol'                => fake()->randomElement(['estilista', 'Estetisista', 'recepcionista', 'admin']),
            'salario'            => fake()->randomElement([1423500, 1800000, 2200000, 2600000, 3100000, 3800000]),
            'fecha_contratacion' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'disponibilidad'     => fake()->boolean(85),
        ];
    }

    /**
     * Fija el rol del empleado: estilista, Estetisista, recepcionista o admin.
     */
    public function rol(string $rol): static
    {
        return $this->state(fn () => ['rol' => $rol]);
    }
}
