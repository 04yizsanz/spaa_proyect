<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\FacturaServicioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MovimientoInventarioController;





// Empleados
Route::apiResource('empleados', EmpleadoController::class);

// Servicios
Route::apiResource('servicios', ServicioController::class);

// Citas
Route::apiResource('citas', CitaController::class);

// Facturas
Route::apiResource('facturas', FacturaController::class);

// Pagos
Route::apiResource('pagos', PagoController::class);

// Facturas de servicios (PK compuesta: factura_id + servicio_id)
Route::get('factura-servicios', [FacturaServicioController::class, 'index']);
Route::post('factura-servicios', [FacturaServicioController::class, 'store']);
Route::get('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'show']);
Route::put('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update']);
Route::patch('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update']);
Route::delete('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'destroy']);

// Rol
Route::apiResource('roles', RolController::class);

// Usuario
Route::apiResource('usuarios', UsuarioController::class);

// Cliente
Route::apiResource('clientes', ClienteController::class);

// Proveedor - rutas personalizadas (antes del apiResource)
Route::get('proveedores/contacto/{contacto}', [ProveedorController::class, 'getByContacto']);
Route::get('proveedores/email/{email}', [ProveedorController::class, 'getByEmail']);
Route::get('proveedores/registro-tributario/{registro_tributario}', [ProveedorController::class, 'getByRegistroTributario']);

// Proveedor - CRUD estándar
Route::apiResource('proveedores', ProveedorController::class);

// Producto - rutas personalizadas (antes del apiResource)
Route::get('producto/nombre/{nombre}', [ProductoController::class, 'getByNombre']);
Route::get('producto/fecha-registro/{fecha_registro}', [ProductoController::class, 'getByFechaRegistro']);
Route::get('producto/proveedor/{proveedor_id}', [ProductoController::class, 'getByProveedor']);

// Producto - CRUD estándar
Route::apiResource('producto', ProductoController::class);

// MovimientoInventario - rutas personalizadas (antes del apiResource)
Route::get('movimientoinventario/fecha-hora/{fecha_hora}', [MovimientoInventarioController::class, 'getByFechaHora']);
Route::get('movimientoinventario/producto/{producto_id}', [MovimientoInventarioController::class, 'getByProducto']);

// MovimientoInventario - CRUD estándar
Route::apiResource('movimientoinventario', MovimientoInventarioController::class);



