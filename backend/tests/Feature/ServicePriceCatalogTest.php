<?php

namespace Tests\Feature;

use Database\Seeders\CitaCatalogoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ServicePriceCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_sets_local_reference_prices_and_durations(): void
    {
        $this->seed(CitaCatalogoSeeder::class);

        $expected = [
            'Manicure' => [40, 23000],
            'Pedicure' => [60, 32000],
            'Masajes' => [60, 140000],
            'Depilación bikini' => [30, 65000],
            'Planchado' => [30, 33000],
            'Tintura' => [120, 180000],
        ];

        foreach ($expected as $name => [$duration, $price]) {
            $this->assertDatabaseHas('servicios', [
                'nombre' => $name,
                'duracion_min' => $duration,
                'precio' => $price,
                'activo' => true,
            ]);
        }

        $this->assertSame(
            0,
            DB::table('servicios')->where('precio', 0)->whereNull('deleted_at')->count()
        );
    }

    public function test_running_catalog_seeder_twice_does_not_duplicate_service_records(): void
    {
        $this->seed(CitaCatalogoSeeder::class);
        $this->seed(CitaCatalogoSeeder::class);

        $this->assertSame(
            1,
            DB::table('servicios')->where('nombre', 'Manicure')->whereNull('deleted_at')->count()
        );
    }
}
