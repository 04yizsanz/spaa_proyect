<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Factura;
use App\Models\FacturaServicio;
use App\Models\Servicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Una factura por cada cita completada, con su detalle en factura_servicio.
 * En el 30 % de los casos se agrega un servicio adicional.
 * Subtotal = suma de líneas, Impuestos = 19 %, Total = Subtotal + Impuestos.
 *
 * Solo procesa las citas generadas por CitaFactory (codigo_cita >= 200000).
 * Las citas fijas del DatabaseSeeder (1000xx) ya tienen sus facturas.
 */
class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = Servicio::where('activo', true)->get();

        Cita::with('servicio')
            ->where('estado', 'completada')
            ->where('codigo_cita', '>=', 200000)
            ->get()
            ->each(function (Cita $cita) use ($servicios) {
                $lineas = collect([
                    ['servicio' => $cita->servicio, 'cantidad' => 1],
                ]);

                if (fake()->boolean(30)) {
                    $extra = $servicios->where('servicio_id', '!=', $cita->servicio_id)->random();
                    $lineas->push(['servicio' => $extra, 'cantidad' => 1]);
                }

                $subtotal  = $lineas->sum(fn ($l) => $l['servicio']->precio * $l['cantidad']);
                $impuestos = round($subtotal * 0.19, 2);

                $factura = Factura::create([
                    'fecha_hora' => Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora)
                        ->addMinutes($cita->servicio->duracion_min),
                    'subtotal'   => $subtotal,
                    'impuestos'  => $impuestos,
                    'total'      => $subtotal + $impuestos,
                    'pdf_url'    => null,
                    'cliente_id' => $cita->cliente_id,
                ]);

                foreach ($lineas as $linea) {
                    FacturaServicio::create([
                        'factura_id'      => $factura->factura_id,
                        'servicio_id'     => $linea['servicio']->servicio_id,
                        'cantidad'        => $linea['cantidad'],
                        'precio_unitario' => $linea['servicio']->precio,
                    ]);
                }
            });
    }
}
