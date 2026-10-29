<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo único de roles. Es idempotente: se puede ejecutar varias veces
 * sin duplicar registros.
 */
class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Administrador general del sistema.'],
            ['nombre' => 'Recepcionista', 'descripcion' => 'Gestiona clientes y citas.'],
            ['nombre' => 'Estilista',     'descripcion' => 'Realiza servicios de estilismo.'],
            ['nombre' => 'Esteticista',   'descripcion' => 'Realiza servicios de estética.'],
            ['nombre' => 'Cliente',       'descripcion' => 'Cliente registrado que puede gestionar sus citas.'],
        ];

        foreach ($roles as $rol) {
            $existe = DB::table('roles')->where('nombre', $rol['nombre'])->exists();

            if ($existe) {
                continue; // no pisamos lo que ya existe
            }

            DB::table('roles')->insert($rol + [
                'estado'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
