<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Cada cliente se crea junto con su usuario (relación 1:1 con usuarios).
 *
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'usuario_id'       => Usuario::factory(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'preferencias'     => fake()->randomElement([
                'Prefiere citas en la mañana',
                'Piel sensible, evitar productos con fragancia',
                'Cabello teñido, usar productos sin sulfatos',
                'Prefiere atención con la misma estilista',
                'Alérgica al látex',
                'Le gustan los tonos cobrizos',
                null,
            ]),
        ];
    }
}
