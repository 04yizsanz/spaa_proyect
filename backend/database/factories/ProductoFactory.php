<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * La cantidad arranca en 0: el stock real lo calcula
 * MovimientoInventarioSeeder a partir de las entradas y salidas.
 *
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre'         => fake()->randomElement([
                'Shampoo sin sulfatos 500 ml', 'Acondicionador hidratante 500 ml',
                'Tinte permanente rubio cenizo', 'Tinte permanente castaño oscuro',
                'Oxidante 20 vol 1 L', 'Tratamiento de keratina 1 L',
                'Esmalte semipermanente rojo', 'Removedor de esmalte 500 ml',
                'Cera depilatoria 400 g', 'Aceite de masaje de almendras 1 L',
                'Mascarilla facial de arcilla', 'Crema exfoliante corporal',
                'Toallas desechables x100', 'Guantes de nitrilo x100',
            ]),
            'cantidad'       => 0,
            'precio'         => fake()->numberBetween(8, 120) * 1000,
            'fecha_registro' => fake()->dateTimeBetween('-6 months', '-2 months')->format('Y-m-d'),
            'proveedor_id'   => Proveedor::factory(),
        ];
    }
}
