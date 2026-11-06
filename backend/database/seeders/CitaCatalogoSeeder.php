<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitaCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $empleados = [
            [
                'nombre' => 'Jessica',
                'apellido' => 'Gómez',
                'documento' => '1010000001',
                'correo' => 'jessica.estilista@salon.com',
                'telefono' => '3101000001',
                'rol' => 'estilista',
                'salario' => 1800000,
                'fecha_contratacion' => '2024-01-15',
                'disponibilidad' => true,
            ],
            [
                'nombre' => 'Andrea',
                'apellido' => 'Rojas',
                'documento' => '1010000002',
                'correo' => 'andrea.esteticista@salon.com',
                'telefono' => '3101000002',
                'rol' => 'Estetisista',
                'salario' => 1900000,
                'fecha_contratacion' => '2024-03-10',
                'disponibilidad' => true,
            ],
            [
                'nombre' => 'Sandra',
                'apellido' => 'Pérez',
                'documento' => '1010000003',
                'correo' => 'sandra.estilista@salon.com',
                'telefono' => '3101000003',
                'rol' => 'estilista',
                'salario' => 1750000,
                'fecha_contratacion' => '2024-06-01',
                'disponibilidad' => true,
            ],
            [
                'nombre' => 'Camila',
                'apellido' => 'Torres',
                'documento' => '1010000004',
                'correo' => 'camila.recepcion@salon.com',
                'telefono' => '3101000004',
                'rol' => 'recepcionista',
                'salario' => 1600000,
                'fecha_contratacion' => '2024-02-20',
                'disponibilidad' => true,
            ],
            [
                'nombre' => 'Daniel',
                'apellido' => 'Vargas',
                'documento' => '1010000005',
                'correo' => 'daniel.administrador@salon.com',
                'telefono' => '3101000005',
                'rol' => 'administrador',
                'salario' => 2200000,
                'fecha_contratacion' => '2024-04-15',
                'disponibilidad' => true,
            ],
            [
                'nombre' => 'Valentina',
                'apellido' => 'Morales',
                'documento' => '1010000006',
                'correo' => 'valentina.manicurista@salon.com',
                'telefono' => '3101000006',
                'rol' => 'manicurista',
                'salario' => 1700000,
                'fecha_contratacion' => '2024-05-01',
                'disponibilidad' => true,
            ],
        ];

        foreach ($empleados as $empleado) {
            $existente = DB::table('empleados')
                ->where('documento', $empleado['documento'])
                ->first(['empleado_id']);

            if ($existente) {
                DB::table('empleados')
                    ->where('empleado_id', $existente->empleado_id)
                    ->update($empleado + ['deleted_at' => null, 'updated_at' => $now]);
                continue;
            }

            DB::table('empleados')->insert($empleado + [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $servicios = [
            ['Manicure', 40, 23000, 'Manicure tradicional. Mediana de tres tarifas públicas consultadas en Bogotá: COP $23.000.'],
            ['Pedicure', 60, 32000, 'Pedicure tradicional. Mediana de tres tarifas públicas consultadas en Bogotá: COP $32.000.'],
            ['Masajes', 60, 140000, 'Masaje relajante cuerpo completo; tarifa publicada por un spa de Bogotá.'],
            ['Depilación de cejas', 15, 13000, 'Depilación de cejas con cera. Promedio de dos tarifas públicas consultadas en Bogotá.'],
            ['Depilación corporal', 15, 20000, 'Tarifa base por zona (referencia de axila); el precio cambia según el área.'],
            ['Depilación bikini', 30, 65000, 'Bikini completo; tarifa y duración publicadas por un spa de Bogotá.'],
            ['Depilación de piernas', 40, 65000, 'Pierna completa; tarifa y duración publicadas por un spa de Bogotá.'],
            ['Depilación de otras zonas', 15, 20000, 'Tarifa base por zona pequeña; confirmar el valor según el área a depilar.'],
            ['Corte de cabello', 60, 50000, 'Corte de cabello. Referencia de tarifa pública de peluquerías de Bogotá.'],
            ['Planchado', 30, 33000, 'Precio y duración orientativos; el tiempo y costo cambian según largo y volumen.'],
            ['Alisado', 180, 120000, 'Precio base estimado dentro del rango observado de COP $120.000–$400.000; requiere valoración.'],
            ['Decoloraciones', 180, 360000, 'Referencia publicada para balayage (técnica de aclaración); otras decoloraciones requieren cotización.'],
            ['Tintura', 120, 180000, 'Referencia publicada; coloraciones cortas desde COP $150.000 y cabello medio desde COP $185.000.'],
            ['Corte y barba', 60, 50000, 'Corte de cabello y arreglo de barba. Referencia de tarifa pública de Bogotá.'],
            ['Tratamiento capilar', 90, 90000, 'Precio orientativo; puede variar según diagnóstico, largo y producto utilizado.'],
        ];

        foreach ($servicios as [$nombre, $duracion, $precio, $descripcion]) {
            $existente = DB::table('servicios')
                ->where('nombre', $nombre)
                ->first(['servicio_id']);

            if ($existente) {
                DB::table('servicios')
                    ->where('servicio_id', $existente->servicio_id)
                    ->update([
                        'duracion_min' => $duracion,
                        'precio' => $precio,
                        'descripcion' => $descripcion,
                        'activo' => true,
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
                continue;
            }

            DB::table('servicios')->insert([
                'nombre' => $nombre,
                'duracion_min' => $duracion,
                'precio' => $precio,
                'descripcion' => $descripcion,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
