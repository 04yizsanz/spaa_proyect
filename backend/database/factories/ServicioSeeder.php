<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Catálogo fijo de servicios. Usa firstOrCreate para no duplicar
 * si el seeder se ejecuta más de una vez.
 */
class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            ['Corte de cabello dama',      45,  35000, 'Corte con lavado y secado.'],
            ['Corte de cabello caballero', 30,  20000, 'Corte clásico o moderno.'],
            ['Tinte completo',            120, 150000, 'Aplicación de color en todo el cabello.'],
            ['Mechas balayage',           180, 250000, 'Iluminación degradada a mano alzada.'],
            ['Keratina',                  150, 220000, 'Alisado y nutrición del cabello.'],
            ['Peinado para evento',        60,  60000, 'Ondas, recogidos o semirecogidos.'],
            ['Manicure tradicional',       45,  25000, 'Limpieza, limado y esmaltado.'],
            ['Manicure semipermanente',    60,  45000, 'Esmaltado de larga duración.'],
            ['Pedicure spa',               60,  40000, 'Exfoliación, hidratación y esmaltado.'],
            ['Limpieza facial profunda',   60,  90000, 'Extracción, exfoliación y mascarilla.'],
            ['Masaje relajante',           60, 110000, 'Masaje corporal con aceites esenciales.'],
            ['Depilación con cera piernas',45,  50000, 'Depilación completa de piernas.'],
        ];

        foreach ($servicios as [$nombre, $duracion, $precio, $descripcion]) {
            Servicio::firstOrCreate(
                ['nombre' => $nombre],
                [
                    'duracion_min' => $duracion,
                    'precio'       => $precio,
                    'descripcion'  => $descripcion,
                    'activo'       => true,
                ]
            );
        }
    }
}
