<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Total = Subtotal + Impuestos (IVA 19 %).
 *
 * @extends Factory<Factura>
 */
class FacturaFactory extends Factory
{
    protected $model = Factura::class;

    public function definition(): array
    {
        $subtotal  = fake()->numberBetween(30, 400) * 1000;
        $impuestos = round($subtotal * 0.19, 2);

        return [
            'fecha_hora' => fake()->dateTimeBetween('-2 months', 'now'),
            'subtotal'   => $subtotal,
            'impuestos'  => $impuestos,
            'total'      => $subtotal + $impuestos,
            'pdf_url'    => null,
            'cliente_id' => Cliente::factory(),
        ];
    }
}
