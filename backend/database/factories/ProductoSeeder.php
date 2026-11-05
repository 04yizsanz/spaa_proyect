<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

/**
 * Crea 20 productos repartidos entre los proveedores existentes.
 */
class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        Producto::factory()
            ->count(20)
            ->recycle(Proveedor::all())
            ->create();
    }
}
