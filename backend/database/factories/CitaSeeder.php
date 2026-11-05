<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Crea 60 citas reutilizando clientes, empleados y servicios existentes.
 * Solo se asignan estilistas y esteticistas (no recepcionistas ni admin).
 */
class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $clientes      = Cliente::all();
        $profesionales = Empleado::whereIn('rol', ['estilista', 'Estetisista'])->get();
        $servicios     = Servicio::where('activo', true)->get();

        Cita::factory()
            ->count(60)
            ->recycle($clientes)
            ->recycle($profesionales)
            ->recycle($servicios)
            ->create();
    }
}
