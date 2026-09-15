<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | ROLES
            |--------------------------------------------------------------------------
            */

            $administradorId = DB::table('roles')->insertGetId([
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador general del sistema.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recepcionistaId = DB::table('roles')->insertGetId([
                'nombre' => 'Recepcionista',
                'descripcion' => 'Gestiona clientes y citas.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $estilistaId = DB::table('roles')->insertGetId([
                'nombre' => 'Estilista',
                'descripcion' => 'Realiza servicios de estilismo.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $esteticistaId = DB::table('roles')->insertGetId([
                'nombre' => 'Esteticista',
                'descripcion' => 'Realiza servicios de estética.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | USUARIOS
            |--------------------------------------------------------------------------
            */

            $administradorUsuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $administradorId,
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'documento' => '1001001001',
                'email' => 'carlos.admin@example.com',
                'telefono' => '3001001001',
                'password' => Hash::make('12345678'),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recepcionistaUsuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $recepcionistaId,
                'nombre' => 'Laura',
                'apellido' => 'Gómez',
                'documento' => '1001001002',
                'email' => 'laura.recepcion@example.com',
                'telefono' => '3001001002',
                'password' => Hash::make('12345678'),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $estilistaUsuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $estilistaId,
                'nombre' => 'Andrés',
                'apellido' => 'Martínez',
                'documento' => '1001001003',
                'email' => 'andres.estilista@example.com',
                'telefono' => '3001001003',
                'password' => Hash::make('12345678'),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $esteticistaUsuarioId = DB::table('usuarios')->insertGetId([
                'rol_id' => $esteticistaId,
                'nombre' => 'Sofía',
                'apellido' => 'López',
                'documento' => '1001001004',
                'email' => 'sofia.esteticista@example.com',
                'telefono' => '3001001004',
                'password' => Hash::make('12345678'),
                'estado' => true,
                'created_at' => now6(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | CLIENTES
            |--------------------------------------------------------------------------
            */

            $cliente1Id = DB::table('clientes')->insertGetId([
                'usuario_id' => $recepcionistaUsuarioId,
                'fecha_nacimiento' => '1995-05-15',
                'preferencias' => 'Prefiere cortes clásicos y horarios en la tarde.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cliente2Id = DB::table('clientes')->insertGetId([
                'usuario_id' => $estilistaUsuarioId,
                'fecha_nacimiento' => '1990-08-20',
                'preferencias' => 'Prefiere servicios de corte y barba.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cliente3Id = DB::table('clientes')->insertGetId([
                'usuario_id' => $esteticistaUsuarioId,
                'fecha_nacimiento' => '1998-02-10',
                'preferencias' => 'Prefiere servicios de manicure y tratamientos.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | EMPLEADOS
            |--------------------------------------------------------------------------
            */

            $empleadoEstilistaId = DB::table('empleados')->insertGetId([
                'nombre' => 'Andrés',
                'apellido' => 'Martínez',
                'documento' => '1010000001',
                'correo' => 'andres.estilista@salon.com',
                'telefono' => '3101000001',
                'rol' => 'estilista',
                'salario' => 1800000,
                'fecha_contratacion' => '2024-01-15',
                'disponibilidad' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $empleadoEsteticistaId = DB::table('empleados')->insertGetId([
                'nombre' => 'Sofía',
                'apellido' => 'López',
                'documento' => '1010000002',
                'correo' => 'sofia.esteticista@salon.com',
                'telefono' => '3101000002',
                'rol' => 'Estetisista',
                'salario' => 1900000,
                'fecha_contratacion' => '2024-03-10',
                'disponibilidad' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $empleadoEstilista2Id = DB::table('empleados')->insertGetId([
                'nombre' => 'Mariana',
                'apellido' => 'Torres',
                'documento' => '1010000003',
                'correo' => 'mariana.estilista@salon.com',
                'telefono' => '3101000003',
                'rol' => 'estilista',
                'salario' => 1750000,
                'fecha_contratacion' => '2024-06-01',
                'disponibilidad' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $empleadoRecepcionistaId = DB::table('empleados')->insertGetId([
                'nombre' => 'Paula',
                'apellido' => 'Ramírez',
                'documento' => '1010000004',
                'correo' => 'paula.recepcion@salon.com',
                'telefono' => '3101000004',
                'rol' => 'recepcionista',
                'salario' => 1600000,
                'fecha_contratacion' => '2024-02-20',
                'disponibilidad' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | SERVICIOS
            |--------------------------------------------------------------------------
            */

            $corteId = DB::table('servicios')->insertGetId([
                'nombre' => 'Corte de cabello',
                'duracion_min' => 45,
                'precio' => 25000,
                'descripcion' => 'Corte de cabello tradicional.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $corteBarbaId = DB::table('servicios')->insertGetId([
                'nombre' => 'Corte y barba',
                'duracion_min' => 60,
                'precio' => 40000,
                'descripcion' => 'Corte de cabello y arreglo de barba.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $manicureId = DB::table('servicios')->insertGetId([
                'nombre' => 'Manicure',
                'duracion_min' => 45,
                'precio' => 30000,
                'descripcion' => 'Servicio profesional de manicure.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $pedicureId = DB::table('servicios')->insertGetId([
                'nombre' => 'Pedicure',
                'duracion_min' => 60,
                'precio' => 40000,
                'descripcion' => 'Servicio profesional de pedicure.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tratamientoId = DB::table('servicios')->insertGetId([
                'nombre' => 'Tratamiento capilar',
                'duracion_min' => 90,
                'precio' => 70000,
                'descripcion' => 'Tratamiento especializado para el cabello.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | CITAS
            |--------------------------------------------------------------------------
            */

            $cita1Id = DB::table('citas')->insertGetId([
                'codigo_cita' => 100001,
                'fecha' => now()->addDays(1)->format('Y-m-d'),
                'hora' => '09:00:00',
                'estado' => 'confirmada',
                'cliente_id' => $cliente1Id,
                'empleado_id' => $empleadoEstilistaId,
                'servicio_id' => $corteId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cita2Id = DB::table('citas')->insertGetId([
                'codigo_cita' => 100002,
                'fecha' => now()->addDays(2)->format('Y-m-d'),
                'hora' => '10:30:00',
                'estado' => 'pendiente',
                'cliente_id' => $cliente2Id,
                'empleado_id' => $empleadoEstilistaId,
                'servicio_id' => $corteBarbaId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cita3Id = DB::table('citas')->insertGetId([
                'codigo_cita' => 100003,
                'fecha' => now()->addDays(3)->format('Y-m-d'),
                'hora' => '14:00:00',
                'estado' => 'confirmada',
                'cliente_id' => $cliente3Id,
                'empleado_id' => $empleadoEsteticistaId,
                'servicio_id' => $manicureId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cita4Id = DB::table('citas')->insertGetId([
                'codigo_cita' => 100004,
                'fecha' => now()->subDays(2)->format('Y-m-d'),
                'hora' => '16:00:00',
                'estado' => 'completada',
                'cliente_id' => $cliente1Id,
                'empleado_id' => $empleadoEstilista2Id,
                'servicio_id' => $tratamientoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | PAGOS
            |--------------------------------------------------------------------------
            */

            DB::table('pagos')->insert([
                [
                    'monto' => 70000,
                    'metodo' => 'efectivo',
                    'fecha_hora' => now()->subDays(2),
                    'estado' => 'aprobado',
                    'codigo_cita' => 100004,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'monto' => 25000,
                    'metodo' => 'tarjeta',
                    'fecha_hora' => now(),
                    'estado' => 'aprobado',
                    'codigo_cita' => 100001,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'monto' => 30000,
                    'metodo' => 'transferencia',
                    'fecha_hora' => now(),
                    'estado' => 'pendiente',
                    'codigo_cita' => 100003,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);


            /*
            |--------------------------------------------------------------------------
            | FACTURAS
            |--------------------------------------------------------------------------
            */

            $factura1Id = DB::table('facturas')->insertGetId([
                'fecha_hora' => now()->subDays(2),
                'subtotal' => 70000,
                'impuestos' => 13300,
                'total' => 83300,
                'pdf_url' => null,
                'cliente_id' => $cliente1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $factura2Id = DB::table('facturas')->insertGetId([
                'fecha_hora' => now(),
                'subtotal' => 25000,
                'impuestos' => 4750,
                'total' => 29750,
                'pdf_url' => null,
                'cliente_id' => $cliente1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | FACTURA SERVICIO
            |--------------------------------------------------------------------------
            */

            DB::table('factura_servicio')->insert([
                [
                    'factura_id' => $factura1Id,
                    'servicio_id' => $tratamientoId,
                    'cantidad' => 1,
                    'precio_unitario' => 70000,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'factura_id' => $factura2Id,
                    'servicio_id' => $corteId,
                    'cantidad' => 1,
                    'precio_unitario' => 25000,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);


            /*
            |--------------------------------------------------------------------------
            | PROVEEDORES
            |--------------------------------------------------------------------------
            */

            $proveedor1Id = DB::table('proveedor')->insertGetId([
                'nombre' => 'Distribuciones Belleza S.A.S.',
                'contacto' => 'Juan Pérez',
                'email' => 'ventas@distribucionesbelleza.com',
                'registro_tributario' => '900100100-1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $proveedor2Id = DB::table('proveedor')->insertGetId([
                'nombre' => 'Productos Profesionales S.A.S.',
                'contacto' => 'María González',
                'email' => 'contacto@productosprofesionales.com',
                'registro_tributario' => '900200200-2',
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | PRODUCTOS
            |--------------------------------------------------------------------------
            */

            $shampooId = DB::table('producto')->insertGetId([
                'nombre' => 'Shampoo Profesional',
                'cantidad' => 50,
                'precio' => 35000,
                'fecha_registro' => now()->format('Y-m-d'),
                'proveedor_id' => $proveedor1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $acondicionadorId = DB::table('producto')->insertGetId([
                'nombre' => 'Acondicionador Profesional',
                'cantidad' => 40,
                'precio' => 38000,
                'fecha_registro' => now()->format('Y-m-d'),
                'proveedor_id' => $proveedor1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $esmalteId = DB::table('producto')->insertGetId([
                'nombre' => 'Esmalte Profesional',
                'cantidad' => 100,
                'precio' => 15000,
                'fecha_registro' => now()->format('Y-m-d'),
                'proveedor_id' => $proveedor2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $mascarillaId = DB::table('producto')->insertGetId([
                'nombre' => 'Mascarilla Capilar',
                'cantidad' => 30,
                'precio' => 45000,
                'fecha_registro' => now()->format('Y-m-d'),
                'proveedor_id' => $proveedor2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | MOVIMIENTOS DE INVENTARIO
            |--------------------------------------------------------------------------
            */

            DB::table('movimientoinventario')->insert([
                [
                    'tipo' => 'entrada',
                    'cantidad' => 50,
                    'fecha_hora' => now()->subDays(10),
                    'motivo' => 'Compra inicial',
                    'producto_id' => $shampooId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tipo' => 'salida',
                    'cantidad' => 5,
                    'fecha_hora' => now()->subDays(5),
                    'motivo' => 'Consumo',
                    'producto_id' => $shampooId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tipo' => 'entrada',
                    'cantidad' => 40,
                    'fecha_hora' => now()->subDays(8),
                    'motivo' => 'Compra',
                    'producto_id' => $acondicionadorId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tipo' => 'salida',
                    'cantidad' => 10,
                    'fecha_hora' => now()->subDays(3),
                    'motivo' => 'Consumo',
                    'producto_id' => $esmalteId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        });
    }
}
