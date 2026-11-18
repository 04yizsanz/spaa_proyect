<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CitaWebController;
use App\Http\Controllers\Web\ClienteWebController;
use App\Http\Controllers\Web\EmpleadoWebController;
use App\Http\Controllers\Web\FacturaWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MovimientoInventarioWebController;
use App\Http\Controllers\Web\PagoWebController;
use App\Http\Controllers\Web\ProductoWebController;
use App\Http\Controllers\Web\ProveedorWebController;
use App\Http\Controllers\Web\ServicioWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use App\Models\Cita;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas web — Asha Spa
|--------------------------------------------------------------------------
|
| Estas rutas devuelven vistas Blade (las de api.php devuelven JSON).
|
| Seguridad (se aplica en el servidor, no en las vistas):
|  - 'auth' + 'cuenta.activa': sesión iniciada con usuario y rol activos.
|  - Cada controlador web autoriza con las Policies (Gate::authorize).
|  - Ocultar enlaces en el menú es solo una ayuda visual.
|
| Route::resource crea las 7 rutas típicas de un módulo:
|   index (lista) · create (/nuevo) · store · show · edit (/editar) · update · destroy
| Los verbos en español se configuran en AppServiceProvider::boot().
|
*/

// ── Autenticación (solo para invitados) ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:registro');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Rutas protegidas ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'cuenta.activa'])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Citas (cada cita se autoriza en el controlador con CitaPolicy)
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/',            [CitaWebController::class, 'index'])->name('index')->can('viewAny', Cita::class);
        Route::get('/nueva',       [CitaWebController::class, 'create'])->name('create')->can('create', Cita::class);
        Route::post('/',           [CitaWebController::class, 'store'])->name('store')->can('create', Cita::class);
        Route::get('/{id}/editar', [CitaWebController::class, 'edit'])->name('edit')->whereNumber('id');
        Route::put('/{id}',        [CitaWebController::class, 'update'])->name('update')->whereNumber('id');
        Route::patch('/{id}/cancelar', [CitaWebController::class, 'cancelar'])->name('cancelar')->whereNumber('id');
    });

    // Clientes, servicios y personal
    Route::resource('clientes', ClienteWebController::class)->whereNumber('cliente');
    Route::resource('servicios', ServicioWebController::class)->whereNumber('servicio');
    Route::resource('empleados', EmpleadoWebController::class)->whereNumber('empleado');

    // Caja: pagos y facturas. Los registros financieros NO se eliminan.
    Route::resource('pagos', PagoWebController::class)->except('destroy')->whereNumber('pago');
    Route::patch('pagos/{pago}/estado', [PagoWebController::class, 'cambiarEstado'])
        ->name('pagos.estado')->whereNumber('pago');

    Route::resource('facturas', FacturaWebController::class)->except('destroy')->whereNumber('factura');
    Route::post('facturas/{factura}/servicios', [FacturaWebController::class, 'agregarServicio'])
        ->name('facturas.servicios.store')->whereNumber('factura');
    Route::put('facturas/{factura}/servicios/{servicio}', [FacturaWebController::class, 'actualizarServicio'])
        ->name('facturas.servicios.update')->whereNumber(['factura', 'servicio']);

    // Inventario
    Route::resource('proveedores', ProveedorWebController::class)->whereNumber('proveedore');
    Route::resource('productos', ProductoWebController::class)->whereNumber('producto');
    Route::resource('movimientos', MovimientoInventarioWebController::class)->whereNumber('movimiento');

    // Gestión de usuarios: solo con el permiso 'sistema.administrar' (Administrador).
    // Las cuentas no se eliminan: se desactivan desde "Editar".
    Route::resource('usuarios', UsuarioWebController::class)
        ->except(['show', 'destroy'])
        ->whereNumber('usuario')
        ->middleware('can:administrar-sistema');
});
