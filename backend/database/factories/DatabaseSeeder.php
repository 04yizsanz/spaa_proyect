<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * El orden respeta las llaves foráneas:
     * primero las tablas independientes y luego las que dependen de ellas.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            ClienteSeeder::class,              // usuarios + clientes
            EmpleadoSeeder::class,
            ServicioSeeder::class,
            CitaSeeder::class,                 // necesita clientes, empleados y servicios
            PagoSeeder::class,                 // necesita citas
            FacturaSeeder::class,              // necesita citas y servicios
            ProveedorSeeder::class,
            ProductoSeeder::class,             // necesita proveedores
            MovimientoInventarioSeeder::class, // necesita productos
        ]);
    }
}
