<?php

namespace Database\Seeders;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Para cada producto que todavía no tiene movimientos
 * (los productos fijos del DatabaseSeeder ya traen los suyos):
 *  1. Registra una entrada inicial (compra al proveedor).
 *  2. Registra entre 3 y 6 movimientos posteriores en orden cronológico.
 *  3. Nunca permite una salida mayor al stock disponible.
 *  4. Actualiza producto.cantidad con el stock resultante,
 *     para que el inventario quede coherente con los movimientos.
 */
class MovimientoInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $productosConMovimientos = MovimientoInventario::pluck('producto_id')->unique();

        Producto::whereNotIn('producto_id', $productosConMovimientos)->get()->each(function (Producto $producto) {
            $fecha = Carbon::parse($producto->fecha_registro)->setTime(9, 0);
            $stock = fake()->numberBetween(20, 80);

            MovimientoInventario::create([
                'tipo'        => 'entrada',
                'cantidad'    => $stock,
                'fecha_hora'  => $fecha,
                'motivo'      => 'Compra inicial al proveedor',
                'producto_id' => $producto->producto_id,
            ]);

            foreach (range(1, fake()->numberBetween(3, 6)) as $i) {
                $fecha = $fecha->copy()
                    ->addDays(fake()->numberBetween(2, 10))
                    ->setTime(fake()->numberBetween(8, 18), fake()->randomElement([0, 15, 30, 45]));

                if ($stock > 5 && fake()->boolean(65)) {
                    $tipo     = 'salida';
                    $cantidad = fake()->numberBetween(1, min(10, $stock));
                    $motivo   = fake()->randomElement(['Uso en servicio', 'Venta a cliente', 'Producto vencido', 'Producto dañado']);
                    $stock   -= $cantidad;
                } else {
                    $tipo     = 'entrada';
                    $cantidad = fake()->numberBetween(10, 40);
                    $motivo   = 'Reposición de stock';
                    $stock   += $cantidad;
                }

                MovimientoInventario::create([
                    'tipo'        => $tipo,
                    'cantidad'    => $cantidad,
                    'fecha_hora'  => $fecha,
                    'motivo'      => $motivo,
                    'producto_id' => $producto->producto_id,
                ]);
            }

            $producto->update(['cantidad' => $stock]);
        });
    }
}
