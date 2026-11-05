<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

/**
 * Crea 25 clientes, cada uno con su usuario de rol Cliente.
 * Contraseña de todos: password
 */
class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::factory()->count(25)->create();
    }
}
