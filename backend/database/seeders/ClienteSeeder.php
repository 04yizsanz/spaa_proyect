<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Crea un cliente fijo para pruebas de login con rol Cliente
 * y 25 clientes aleatorios, cada uno con su usuario.
 * Contraseña de todos: 12345678
 */
class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Cliente fijo para probar el login con rol Cliente
        $usuario = Usuario::factory()->create([
            'nombre'    => 'Valentina',
            'apellido'  => 'Castro',
            'documento' => '1001001005',
            'email'     => 'valentina.cliente@example.com',
            'telefono'  => '3001001005',
        ]);

        Cliente::factory()->create([
            'usuario_id'       => $usuario->usuario_id,
            'fecha_nacimiento' => '2000-07-12',
            'preferencias'     => 'Prefiere citas los sábados en la mañana.',
        ]);

        // Clientes aleatorios
        Cliente::factory()->count(25)->create();
    }
}
