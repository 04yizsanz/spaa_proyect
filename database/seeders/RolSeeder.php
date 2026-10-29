<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre'      => 'Administrador',
                'descripcion' => 'Acceso completo al sistema.',
                'estado'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Empleado',
                'descripcion' => 'Personal del spa (estilistas, esteticistas, recepcionistas).',
                'estado'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Cliente',
                'descripcion' => 'Cliente registrado que puede gestionar sus citas.',
                'estado'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}
