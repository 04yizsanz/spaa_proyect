<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\EnsureAdministrator;
use App\Http\Controllers\ApiAuthController;
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
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Factura;
use App\Models\FacturaServicio;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Servicio;

/*
|--------------------------------------------------------------------------
| Rutas API — Asha Spa
|--------------------------------------------------------------------------
|
| Todas requieren un token Bearer de Sanctum (POST /api/login) y una cuenta
| habilitada (usuario y rol activos), salvo el propio login.
|
| Autorización (denegación por defecto, config/permisos.php):
|  - Permisos de clase (listar, crear) → middleware 'can' en la ruta.
|  - Permisos sobre un registro concreto → Policies en los controladores;
|    un registro que el usuario no puede ver responde 404.
|
*/

// Autenticación (pública, con límite de intentos)
Route::post('login', [ApiAuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('api.login');

Route::middleware(['auth:sanctum', 'cuenta.activa'])->name('api.')->group(function () {

    Route::post('logout', [ApiAuthController::class, 'logout'])->name('logout');
    Route::get('me', [ApiAuthController::class, 'me'])->name('me');

    // Empleados (sin reglas de propiedad: todo se autoriza en la ruta)
    Route::apiResource('empleados', EmpleadoController::class)
        ->where(['empleado' => '[0-9]+'])
        ->middlewareFor(['index', 'show'], 'can:viewAny,'.Empleado::class)
        ->middlewareFor('store', 'can:create,'.Empleado::class)
        ->middlewareFor('update', 'can:update,'.Empleado::class)
        ->middlewareFor('destroy', 'can:delete,'.Empleado::class);

    // Servicios
    Route::apiResource('servicios', ServicioController::class)
        ->where(['servicio' => '[0-9]+'])
        ->middlewareFor(['index', 'show'], 'can:viewAny,'.Servicio::class)
        ->middlewareFor('store', 'can:create,'.Servicio::class)
        ->middlewareFor('update', 'can:update,'.Servicio::class)
        ->middlewareFor('destroy', 'can:delete,'.Servicio::class);

    // Citas (show/update/destroy se autorizan por cita en el controlador)
    Route::apiResource('citas', CitaController::class)
        ->where(['cita' => '[0-9]+'])
        ->middlewareFor('index', 'can:viewAny,'.Cita::class)
        ->middlewareFor('store', 'can:create,'.Cita::class);

    // Facturas (no se eliminan)
    Route::apiResource('facturas', FacturaController::class)
        ->where(['factura' => '[0-9]+'])
        ->middlewareFor('index', 'can:viewAny,'.Factura::class)
        ->middlewareFor('store', 'can:create,'.Factura::class);

    // Pagos (no se eliminan)
    Route::apiResource('pagos', PagoController::class)
        ->where(['pago' => '[0-9]+'])
        ->middlewareFor('index', 'can:viewAny,'.Pago::class)
        ->middlewareFor('store', 'can:create,'.Pago::class);

    // Facturas de servicios (PK compuesta: factura_id + servicio_id; no se eliminan)
    Route::get('factura-servicios', [FacturaServicioController::class, 'index'])
        ->can('viewAny', FacturaServicio::class);
    Route::post('factura-servicios', [FacturaServicioController::class, 'store'])
        ->can('create', FacturaServicio::class);
    Route::get('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'show'])
        ->whereNumber(['factura_id', 'servicio_id']);
    Route::put('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update'])
        ->whereNumber(['factura_id', 'servicio_id']);
    Route::patch('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update'])
        ->whereNumber(['factura_id', 'servicio_id']);
    Route::delete('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'destroy'])
        ->whereNumber(['factura_id', 'servicio_id']);

    // Administración del sistema (permiso 'sistema.administrar')
    Route::middleware(EnsureAdministrator::class)->group(function () {
        Route::apiResource('roles', RolController::class)->where(['role' => '[0-9]+']);
        Route::apiResource('usuarios', UsuarioController::class)->where(['usuario' => '[0-9]+']);
    });

    // Cliente (un Cliente solo consulta su propio perfil; el listado exige 'clientes.ver')
    Route::apiResource('clientes', ClienteController::class)
        ->where(['cliente' => '[0-9]+'])
        ->middlewareFor('index', 'can:viewAny,'.Cliente::class)
        ->middlewareFor('store', 'can:create,'.Cliente::class);

    // Proveedor - rutas personalizadas (antes del apiResource)
    Route::get('proveedores/contacto/{contacto}', [ProveedorController::class, 'getByContacto'])
        ->can('viewAny', Proveedor::class);
    Route::get('proveedores/email/{email}', [ProveedorController::class, 'getByEmail'])
        ->can('viewAny', Proveedor::class);
    Route::get('proveedores/registro-tributario/{registro_tributario}', [ProveedorController::class, 'getByRegistroTributario'])
        ->can('viewAny', Proveedor::class);

    // Proveedor - CRUD estándar
    Route::apiResource('proveedores', ProveedorController::class)
        ->where(['proveedore' => '[0-9]+'])
        ->middlewareFor(['index', 'show'], 'can:viewAny,'.Proveedor::class)
        ->middlewareFor('store', 'can:create,'.Proveedor::class)
        ->middlewareFor('update', 'can:update,'.Proveedor::class)
        ->middlewareFor('destroy', 'can:delete,'.Proveedor::class);

    // Producto - rutas personalizadas (antes del apiResource)
    Route::get('producto/nombre/{nombre}', [ProductoController::class, 'getByNombre'])
        ->can('viewAny', Producto::class);
    Route::get('producto/fecha-registro/{fecha_registro}', [ProductoController::class, 'getByFechaRegistro'])
        ->can('viewAny', Producto::class);
    Route::get('producto/proveedor/{proveedor_id}', [ProductoController::class, 'getByProveedor'])
        ->whereNumber('proveedor_id')
        ->can('viewAny', Producto::class);

    // Producto - CRUD estándar
    Route::apiResource('producto', ProductoController::class)
        ->where(['producto' => '[0-9]+'])
        ->middlewareFor(['index', 'show'], 'can:viewAny,'.Producto::class)
        ->middlewareFor('store', 'can:create,'.Producto::class)
        ->middlewareFor('update', 'can:update,'.Producto::class)
        ->middlewareFor('destroy', 'can:delete,'.Producto::class);

    // MovimientoInventario - rutas personalizadas (antes del apiResource)
    Route::get('movimientoinventario/fecha-hora/{fecha_hora}', [MovimientoInventarioController::class, 'getByFechaHora'])
        ->can('viewAny', MovimientoInventario::class);
    Route::get('movimientoinventario/producto/{producto_id}', [MovimientoInventarioController::class, 'getByProducto'])
        ->whereNumber('producto_id')
        ->can('viewAny', MovimientoInventario::class);

    // MovimientoInventario - CRUD estándar
    Route::apiResource('movimientoinventario', MovimientoInventarioController::class)
        ->where(['movimientoinventario' => '[0-9]+'])
        ->middlewareFor(['index', 'show'], 'can:viewAny,'.MovimientoInventario::class)
        ->middlewareFor('store', 'can:create,'.MovimientoInventario::class)
        ->middlewareFor('update', 'can:update,'.MovimientoInventario::class)
        ->middlewareFor('destroy', 'can:delete,'.MovimientoInventario::class);

});
