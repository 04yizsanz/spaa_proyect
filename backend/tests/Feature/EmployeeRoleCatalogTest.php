<?php

namespace Tests\Feature;

use Database\Seeders\CitaCatalogoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EmployeeRoleCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_includes_an_administrator_and_a_manicurist(): void
    {
        $this->seed(CitaCatalogoSeeder::class);

        $this->assertDatabaseHas('empleados', [
            'nombre' => 'Daniel',
            'apellido' => 'Vargas',
            'rol' => 'administrador',
        ]);
        $this->assertDatabaseHas('empleados', [
            'nombre' => 'Valentina',
            'apellido' => 'Morales',
            'rol' => 'manicurista',
        ]);

        $rolesValidos = DB::table('empleados')
            ->whereIn('rol', ['administrador', 'manicurista'])
            ->count();

        $this->assertSame(2, $rolesValidos);
    }
}
