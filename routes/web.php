<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CitaWebController;
use App\Http\Controllers\Web\ClienteWebController;
use App\Http\Controllers\Web\ServicioWebController;
use App\Http\Controllers\Web\EmpleadoWebController;

/*
|--------------------------------------------------------------------------
| Rutas web — Asha Spa
|--------------------------------------------------------------------------
|
| Estas rutas renderizan vistas Blade. Son distintas de las rutas API
| que ya existen en api.php (o que el equipo puede agregar en el futuro).
|
*/

// ── Autenticación (solo para invitados) ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Rutas protegidas (requieren sesión activa) ────────────────────────────────
Route::middleware('auth')->group(function () {

    // Página de inicio
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Módulo de Citas
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/',             [CitaWebController::class, 'index'])->name('index');
        Route::get('/nueva',        [CitaWebController::class, 'create'])->name('create');
        Route::post('/',            [CitaWebController::class, 'store'])->name('store');
        Route::get('/{id}/editar',  [CitaWebController::class, 'edit'])->name('edit');
        Route::put('/{id}',         [CitaWebController::class, 'update'])->name('update');
    });

    // Módulo de Clientes (listado)
    Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index');

    // Módulo de Servicios (listado)
    Route::get('/servicios', [ServicioWebController::class, 'index'])->name('servicios.index');

    // Módulo de Empleados (listado)
    Route::get('/empleados', [EmpleadoWebController::class, 'index'])->name('empleados.index');

});
