<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Servicios adicionales a los 5 fijos del DatabaseSeeder
 * (Corte de cabello, Corte y barba, Manicure, Pedicure, Tratamiento capilar).
 * Usa firstOrCreate para no duplicar si se ejecuta más de una vez.
 */
class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            ['Tinte completo',              120, 150000, 'Aplicación de color en todo el cabello.'],
            ['Mechas balayage',             180, 250000, 'Iluminación degradada a mano alzada.'],
            ['Keratina',                    150, 220000, 'Alisado y nutrición del cabello.'],
            ['Peinado para evento',          60,  60000, 'Ondas, recogidos o semirecogidos.'],
            ['Manicure semipermanente',      60,  45000, 'Esmaltado de larga duración.'],
            ['Limpieza facial profunda',     60,  90000, 'Extracción, exfoliación y mascarilla.'],
            ['Masaje relajante',             60, 110000, 'Masaje corporal con aceites esenciales.'],
            ['Depilación con cera piernas',  45,  50000, 'Depilación completa de piernas.'],
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
