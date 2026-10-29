<?php

/**
 * ARCHIVO: routes/api.php
 *
 * PROPÓSITO: Define todas las rutas de la API RESTful del sistema Asha Spa.
 * Este archivo es cargado automáticamente por el framework Laravel a través
 * del método `withRouting()` definido en bootstrap/app.php, bajo el prefijo
 * de URL '/api' y el middleware 'api'.
 *
 * Cada ruta asocia un método HTTP y una URI con un método específico de un
 * controlador. Los controladores referenciados delegan su lógica a la capa
 * de servicios (Services), que a su vez utiliza repositorios (Repositories)
 * para interactuar con la base de datos mediante los modelos Eloquent.
 *
 * ARQUITECTURA: El proyecto implementa el patrón Repository-Service-Controller:
 *   - Controlador: recibe la petición HTTP y delega al servicio.
 *   - Servicio: contiene la lógica de negocio.
 *   - Repositorio: abstrae las operaciones sobre la base de datos.
 *
 * FORMATO DE RESPUESTA: Todos los endpoints retornan respuestas en formato
 * JSON, producidas mediante `response()->json()` en cada controlador.
 *
 * AUTENTICACIÓN: En la versión actual del proyecto, las rutas API no
 * aplican middleware de autenticación explícito en este archivo de rutas.
 */

// Importa la fachada Route del framework Laravel.
// Una fachada (Facade) es una interfaz estática que provee acceso al
// sistema de enrutamiento del contenedor de servicios de Laravel.
use Illuminate\Support\Facades\Route;

// Importación de los controladores API de cada módulo del sistema.
// Cada controlador hereda de App\Http\Controllers\Controller y recibe
// una instancia de su servicio correspondiente mediante inyección de
// dependencias en el constructor.
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

// Route::name('api.'): agrupa todas las rutas bajo el prefijo de nombre 'api.'.
// Esto permite referenciar las rutas por nombre en el código, por ejemplo:
// route('api.empleados.index'). El método group() recibe una función
// anónima que define el conjunto de rutas que pertenecen a este grupo.
Route::name('api.')->group(function () {

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: EMPLEADOS
    // Tabla: empleados | Modelo: App\Models\Empleado
    // ─────────────────────────────────────────────────────────────────────────

    // Route::apiResource() registra automáticamente las 5 rutas RESTful
    // estándar para el recurso indicado, sin incluir las rutas de formularios
    // HTML (create y edit) que son exclusivas de interfaces web:
    //   GET    /api/empleados           → EmpleadoController@index   (listar)
    //   POST   /api/empleados           → EmpleadoController@store   (crear)
    //   GET    /api/empleados/{empleado}→ EmpleadoController@show    (ver uno)
    //   PUT/PATCH /api/empleados/{id}   → EmpleadoController@update  (actualizar)
    //   DELETE /api/empleados/{id}      → EmpleadoController@destroy (eliminar)
    Route::apiResource('empleados', EmpleadoController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: SERVICIOS
    // Tabla: servicios | Modelo: App\Models\Servicio
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'servicios'.
    Route::apiResource('servicios', ServicioController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: CITAS
    // Tabla: citas | Modelo: App\Models\Cita
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'citas'.
    Route::apiResource('citas', CitaController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: FACTURAS
    // Tabla: facturas | Modelo: App\Models\Factura
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'facturas'.
    Route::apiResource('facturas', FacturaController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: PAGOS
    // Tabla: pagos | Modelo: App\Models\Pago
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'pagos'.
    Route::apiResource('pagos', PagoController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: FACTURA_SERVICIO (tabla pivote con clave primaria compuesta)
    // Tabla: factura_servicio | Modelo: App\Models\FacturaServicio
    //
    // Este módulo no utiliza Route::apiResource() porque la tabla posee una
    // clave primaria compuesta por dos columnas (factura_id + servicio_id),
    // lo que impide usar el parámetro de ruta simple {id} que apiResource
    // genera por defecto. Por ello se definen las rutas de forma explícita,
    // usando dos segmentos de ruta para identificar unívocamente un registro.
    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/factura-servicios → FacturaServicioController@index
    // Retorna todos los registros de la tabla factura_servicio.
    Route::get('factura-servicios', [FacturaServicioController::class, 'index']);

    // POST /api/factura-servicios → FacturaServicioController@store
    // Crea un nuevo registro en la tabla factura_servicio.
    Route::post('factura-servicios', [FacturaServicioController::class, 'store']);

    // GET /api/factura-servicios/{factura_id}/{servicio_id} → @show
    // Retorna el registro cuya clave primaria compuesta coincide con los
    // dos segmentos de ruta proporcionados.
    Route::get('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'show']);

    // PUT /api/factura-servicios/{factura_id}/{servicio_id} → @update
    // Actualiza completamente un registro identificado por su clave compuesta.
    // El método PUT reemplaza todos los campos del recurso.
    Route::put('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update']);

    // PATCH /api/factura-servicios/{factura_id}/{servicio_id} → @update
    // Actualiza parcialmente un registro. Comparte el método del controlador
    // con la ruta PUT, por lo que ambos verbos ejecutan el mismo método.
    Route::patch('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'update']);

    // DELETE /api/factura-servicios/{factura_id}/{servicio_id} → @destroy
    // Elimina el registro identificado por su clave primaria compuesta.
    Route::delete('factura-servicios/{factura_id}/{servicio_id}', [FacturaServicioController::class, 'destroy']);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: ROLES
    // Tabla: roles | Modelo: App\Models\Rol
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'roles'.
    Route::apiResource('roles', RolController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: USUARIOS
    // Tabla: usuarios | Modelo: App\Models\Usuario
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'usuarios'.
    // Incluye además los métodos adicionales definidos en UsuarioController:
    // porRol, porEstatus y porNombre, que deben registrarse por separado
    // si se requiere exponerlos (no están incluidos en este apiResource).
    Route::apiResource('usuarios', UsuarioController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: CLIENTES
    // Tabla: clientes | Modelo: App\Models\Cliente
    // ─────────────────────────────────────────────────────────────────────────

    // Registra las rutas CRUD estándar para el recurso 'clientes'.
    Route::apiResource('clientes', ClienteController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: PROVEEDORES
    // Tabla: proveedor | Modelo: App\Models\Proveedor
    //
    // Las rutas de búsqueda personalizada se registran ANTES del apiResource
    // para evitar que el parámetro genérico {proveedor} del apiResource capture
    // las rutas de búsqueda y las trate como si fueran identificadores numéricos.
    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/proveedores/contacto/{contacto} → ProveedorController@getByContacto
    // Retorna los proveedores cuyo campo 'contacto' coincide con el valor
    // del segmento de ruta {contacto}.
    Route::get('proveedores/contacto/{contacto}', [ProveedorController::class, 'getByContacto']);

    // GET /api/proveedores/email/{email} → ProveedorController@getByEmail
    // Retorna el proveedor cuyo campo 'email' coincide con el valor proporcionado.
    Route::get('proveedores/email/{email}', [ProveedorController::class, 'getByEmail']);

    // GET /api/proveedores/registro-tributario/{registro_tributario} → @getByRegistroTributario
    // Retorna el proveedor identificado por su número de registro tributario.
    Route::get('proveedores/registro-tributario/{registro_tributario}', [ProveedorController::class, 'getByRegistroTributario']);

    // Registra las rutas CRUD estándar para el recurso 'proveedores'.
    Route::apiResource('proveedores', ProveedorController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: PRODUCTO
    // Tabla: producto | Modelo: App\Models\Producto
    //
    // Al igual que proveedores, las rutas personalizadas se registran antes
    // del apiResource para garantizar la resolución correcta de las rutas.
    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/producto/nombre/{nombre} → ProductoController@getByNombre
    // Retorna los productos cuyo campo 'nombre' coincide con el valor indicado.
    Route::get('producto/nombre/{nombre}', [ProductoController::class, 'getByNombre']);

    // GET /api/producto/fecha-registro/{fecha_registro} → @getByFechaRegistro
    // Retorna los productos registrados en la fecha indicada.
    Route::get('producto/fecha-registro/{fecha_registro}', [ProductoController::class, 'getByFechaRegistro']);

    // GET /api/producto/proveedor/{proveedor_id} → ProductoController@getByProveedor
    // Retorna todos los productos asociados al proveedor identificado
    // por el valor del segmento {proveedor_id}.
    Route::get('producto/proveedor/{proveedor_id}', [ProductoController::class, 'getByProveedor']);

    // Registra las rutas CRUD estándar para el recurso 'producto'.
    Route::apiResource('producto', ProductoController::class);

    // ─────────────────────────────────────────────────────────────────────────
    // MÓDULO: MOVIMIENTO DE INVENTARIO
    // Tabla: movimientoinventario | Modelo: App\Models\MovimientoInventario
    // ─────────────────────────────────────────────────────────────────────────

    // GET /api/movimientoinventario/fecha-hora/{fecha_hora} → @getByFechaHora
    // Retorna los movimientos de inventario registrados en la fecha y hora indicadas.
    Route::get('movimientoinventario/fecha-hora/{fecha_hora}', [MovimientoInventarioController::class, 'getByFechaHora']);

    // GET /api/movimientoinventario/producto/{producto_id} → @getByProducto
    // Retorna todos los movimientos de inventario asociados al producto
    // identificado por el segmento {producto_id}.
    Route::get('movimientoinventario/producto/{producto_id}', [MovimientoInventarioController::class, 'getByProducto']);

    // Registra las rutas CRUD estándar para el recurso 'movimientoinventario'.
    Route::apiResource('movimientoinventario', MovimientoInventarioController::class);

});
