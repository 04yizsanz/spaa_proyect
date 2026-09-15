<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;




// Empleados
Route::apiResource('empleados', EmpleadoController::class);

// Servicios
Route::apiResource('servicios', ServicioController::class);

// Citas
Route::apiResource('citas', CitaController::class);

// Proveedor
Route::apiResource('proveedor', ProveedorController::class);

// Producto
Route::apiResource('producto', ProductoController::class);

// MovimientoInventario
Route::apiResource('movimientoinventario', MovimientoInventarioController::class);