<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Pago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Citas completadas -> pago aprobado.
 * Citas confirmadas -> pago pendiente.
 * El monto es el precio del servicio de la cita.
 */
class PagoSeeder extends Seeder
{
    public function run(): void
    {
        Cita::with('servicio')
            ->whereIn('estado', ['completada', 'confirmada'])
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
