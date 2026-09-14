<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;

// Ruta creada por Laravel
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ==================== ROLES ====================

Route::get('/roles/activos', [RolController::class, 'activos']);
Route::get('/roles/buscar', [RolController::class, 'buscarPorNombre']);

Route::get('/roles', [RolController::class, 'index']);
Route::post('/roles', [RolController::class, 'store']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);
Route::patch('/roles/{id}/estado', [RolController::class, 'actualizarEstado']);


// ==================== USUARIOS ====================

Route::get('/usuarios/rol/{idRol}', [UsuarioController::class, 'buscarPorRol']);
Route::get('/usuarios/estado/{status}', [UsuarioController::class, 'buscarPorEstado']);
Route::get('/usuarios/buscar', [UsuarioController::class, 'buscarPorNombre']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);


// ==================== CLIENTES ====================

Route::get('/clientes/buscar/nombre', [ClienteController::class, 'buscarPorNombre']);
Route::get('/clientes/buscar/apellido', [ClienteController::class, 'buscarPorApellido']);
Route::get('/clientes/buscar/documento', [ClienteController::class, 'buscarPorDocumento']);

Route::get('/clientes', [ClienteController::class, 'index']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::get('/clientes/{id}', [ClienteController::class, 'show']);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
