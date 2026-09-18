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

// Facturas de servicios
Route::apiResource('factura-servicios', FacturaServicioController::class);

// Rol
Route::apiResource('roles', RolController::class);

// Usuario
Route::apiResource('usuarios', UsuarioController::class);

// Cliente
Route::apiResource('clientes', ClienteController::class);



