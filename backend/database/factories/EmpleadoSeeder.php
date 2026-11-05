<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        Empleado::factory()->count(4)->rol('estilista')->create();
        Empleado::factory()->count(3)->rol('Estetisista')->create();
        Empleado::factory()->count(2)->rol('recepcionista')->create();
        Empleado::factory()->count(1)->rol('admin')->create();
    }
}
