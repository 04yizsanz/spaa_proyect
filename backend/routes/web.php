<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CitaWebController;
use App\Http\Controllers\Web\ClienteWebController;
use App\Http\Controllers\Web\ServicioWebController;
use App\Http\Controllers\Web\EmpleadoWebController;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;

/*
|--------------------------------------------------------------------------
| Rutas web — Asha Spa
|--------------------------------------------------------------------------
|
| Estas rutas renderizan vistas Blade. Son distintas de las rutas API
| que ya existen en api.php (o que el equipo puede agregar en el futuro).
|
| La protección se aplica en el servidor: 'auth' + 'cuenta.activa' (usuario
| y rol activos) y 'can' con las Policies de cada módulo. Ocultar enlaces en
| las vistas es solo una ayuda visual.
|
*/

// ── Autenticación (solo para invitados) ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->middleware('throttle:registro');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Rutas protegidas (requieren sesión activa y cuenta habilitada) ───────────
Route::middleware(['auth', 'cuenta.activa'])->group(function () {

    // Página de inicio (muestra solo la información autorizada para el usuario)
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Módulo de Citas (cada cita se autoriza en el controlador con CitaPolicy)
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/',             [CitaWebController::class, 'index'])->name('index')->can('viewAny', Cita::class);
        Route::get('/nueva',        [CitaWebController::class, 'create'])->name('create')->can('create', Cita::class);
        Route::post('/',            [CitaWebController::class, 'store'])->name('store')->can('create', Cita::class);
        Route::get('/{id}/editar',  [CitaWebController::class, 'edit'])->name('edit')->whereNumber('id');
        Route::put('/{id}',         [CitaWebController::class, 'update'])->name('update')->whereNumber('id');
    });

    // Módulo de Clientes (listado: requiere 'clientes.ver')
    Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index')->can('viewAny', Cliente::class);

    // Módulo de Servicios (listado)
    Route::get('/servicios', [ServicioWebController::class, 'index'])->name('servicios.index')->can('viewAny', Servicio::class);

    // Módulo de Empleados (listado)
    Route::get('/empleados', [EmpleadoWebController::class, 'index'])->name('empleados.index')->can('viewAny', Empleado::class);

});
