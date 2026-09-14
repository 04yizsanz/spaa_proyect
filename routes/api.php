<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CitaController;


// Empleados
Route::apiResource('empleados', EmpleadoController::class);

// Servicios
Route::apiResource('servicios', ServicioController::class);

// Citas
Route::apiResource('citas', CitaController::class);