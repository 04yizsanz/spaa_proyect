<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Genera usuarios de prueba con rol Cliente.
 * Contraseña de todos los usuarios generados: password
 *
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'rol_id' => fn () => Rol::firstOrCreate(
                ['nombre' => 'Cliente'],
                ['descripcion' => 'Cliente del spa', 'estado' => true]
            )->rol_id,
            'nombre'         => fake()->firstName(),
            'apellido'       => fake()->lastName(),
            'documento'      => fake()->unique()->numerify('##########'),
            'email'          => fake()->unique()->safeEmail(),
            'telefono'       => fake()->numerify('3#########'),
            'password'       => static::$password ??= Hash::make('password'),
            'estado'         => true,
            'remember_token' => Str::random(10),
        ];
    }
}
