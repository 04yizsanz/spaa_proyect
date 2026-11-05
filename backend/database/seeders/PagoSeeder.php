<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Pago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Citas completadas -> pago aprobado.
 * Citas confirmadas -> pago pendiente.
 * Omite las citas que ya tienen pago (las fijas del DatabaseSeeder).
 */
class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $citasConPago = Pago::pluck('codigo_cita');

        Cita::with('servicio')
            ->whereIn('estado', ['completada', 'confirmada'])
            ->whereNotIn('codigo_cita', $citasConPago)
            ->get()
            ->each(function (Cita $cita) {
                Pago::create([
                    'monto'       => $cita->servicio->precio,
                    'metodo'      => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia', 'pse']),
                    'fecha_hora'  => Carbon::parse($cita->fecha->format('Y-m-d') . ' ' . $cita->hora),
                    'estado'      => $cita->estado === 'completada' ? 'aprobado' : 'pendiente',
                    'codigo_cita' => $cita->codigo_cita,
                ]);
            });
    }
}
