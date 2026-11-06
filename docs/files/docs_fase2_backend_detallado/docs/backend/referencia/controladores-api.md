# Referencia: controladores de la API

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Rutas relativas a la raíz del repositorio. Los fragmentos se copiaron del código sin modificaciones. Nada de lo descrito se ejecutó: los códigos HTTP y los resultados corresponden a la lectura del código. Volver a verificar si cambia la rama `feat/dolly2`.

Este documento completa la [capa 3 de `backend/capas.md`](../capas.md#7-capa-3--controlador). `CitaController`, `ProductoController` (salvo sus búsquedas) y `ApiAuthController` están explicados allí.

## Contenido

1. [Patrones comunes](#1-patrones-comunes)
2. [`Controller` (clase base)](#2-controller-clase-base)
3. [`ClienteController`](#3-clientecontroller)
4. [`EmpleadoController`](#4-empleadocontroller)
5. [`ServicioController`](#5-serviciocontroller)
6. [`FacturaController`](#6-facturacontroller)
7. [`PagoController`](#7-pagocontroller)
8. [`FacturaServicioController`](#8-facturaserviciocontroller)
9. [`RolController`](#9-rolcontroller)
10. [`UsuarioController`](#10-usuariocontroller)
11. [`ProveedorController`](#11-proveedorcontroller)
12. [`ProductoController`: constructor, `index` y búsquedas](#12-productocontroller-constructor-index-y-búsquedas)
13. [`MovimientoInventarioController`](#13-movimientoinventariocontroller)
14. [`CitaController`: métodos sin ruta](#14-citacontroller-métodos-sin-ruta)

---

## 1. Patrones comunes

Los controladores de la API siguen tres patrones. Reconocerlos evita repetir la misma explicación en cada método.

| Patrón | Controladores | Cómo responde | Dónde se autoriza |
| --- | --- | --- | --- |
| **A. Recurso por permiso** | Empleado, Servicio, Rol, Usuario | JSON del registro o un mensaje; 404 si el servicio devuelve `null` o `false` | Solo en la ruta (`can` o `EnsureAdministrator`) |
| **B. Recurso por registro** | Cliente, Factura, Pago, FacturaServicio (y Cita) | Igual que A, pero primero busca el registro **visible** para el usuario (404 si no lo es) y luego llama a `Gate::authorize(...)` | Ruta para `index` y `store`; controlador para `show`, `update`, `destroy` |
| **C. Respuesta envuelta** | Proveedor, Producto, MovimientoInventario | `{ "success": "...", "data": ... }`; 404 con `{ "not_found": "Not found", "message": "..." }` | Solo en la ruta |

En todos, el constructor recibe el servicio correspondiente por **inyección de dependencias** y lo guarda en una propiedad (`protected` en A y B; `private` en C). Ningún controlador de la API accede directamente a modelos ni a repositorios.

**Parámetro de la URL.** `apiResource` nombra el segmento en singular (`{cliente}`, `{empleado}`, `{proveedore}`), pero los métodos lo reciben con otro nombre (`int $id`, `int $cliente`, `int $factura_id`). Laravel entrega los valores de la URL a los parámetros que no son clases según su posición, por eso el número llega igual.

---

## 2. `Controller` (clase base)

```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
```

Fuente: `backend/app/Http/Controllers/Controller.php` L1–8.

| Aspecto | Detalle |
| --- | --- |
| Qué es | Clase **abstracta** (no se puede instanciar) y vacía. Todos los controladores, de la API y de la web, la extienden (`extends Controller`). |
| Por qué existe | Es la plantilla de Laravel 12: ofrece un punto común donde agregar comportamiento compartido. En Asha Spa no tiene contenido; el comentario `//` marca el cuerpo vacío. |
| Métodos | Ninguno. |

---

## 3. `ClienteController`

**Archivo:** `backend/app/Http/Controllers/ClienteController.php` · **Patrón:** B · **Rutas:** `backend/routes/api.php` L109–113.

**Responsabilidad.** Atender las peticiones sobre fichas de cliente. Una ficha (`clientes`) solo guarda fecha de nacimiento y preferencias; nombre, documento y correo están en el usuario asociado.

```php
class ClienteController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->clienteService->listarPara($request->user()));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $cliente = $this->clienteService->buscarVisible($id, $request->user());

        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Gate::authorize('view', $cliente);

        return response()->json($cliente);
    }
```

Fuente: `backend/app/Http/Controllers/ClienteController.php` L16–38.

| Método | Nombre y propósito | Parámetros | Qué hace | Devuelve | Errores |
| --- | --- | --- | --- | --- | --- |
| Constructor (L18–20) | Recibe el servicio | `ClienteService $clienteService` | Lo guarda en `$this->clienteService` | — | — |
| `index` (L22–25) | Listar clientes | `Request $request` | `listarPara($request->user())` → `ClienteRepository::visiblesPara` → `Cliente::scopeVisiblePara` | 200 con la colección | La ruta exige `clientes.ver` (`ClientePolicy::viewAny`); sin él, 403. Por eso un Cliente no llega a la rama `clientes.propio` del alcance desde esta ruta |
| `show` (L27–38) | Consultar una ficha | `Request $request`, `int $id` (`cliente_id`) | Busca la ficha visible; si no hay, 404; luego `Gate::authorize('view', $cliente)` | 200 con la ficha | 404 `Cliente no encontrado`. Un Cliente sí puede ver su propia ficha por esta ruta |

```php
    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = $this->clienteService->create($request->validated());

        return response()->json($cliente, 201);
    }

    public function update(UpdateClienteRequest $request, int $cliente): JsonResponse
    {
    $registro = $this->clienteService->buscarVisible($cliente, $request->user());

    if (! $registro) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    Gate::authorize('update', $registro);

    $updated = $this->clienteService->update($cliente, $request->validated());

    if (! $updated) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    return response()->json(['message' => 'Cliente actualizado correctamente']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $registro = $this->clienteService->buscarVisible($id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Gate::authorize('delete', $registro);

        $deleted = $this->clienteService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/ClienteController.php` L40–83.

| Método | Propósito | Parámetros | Qué hace cada bloque | Devuelve | Errores |
| --- | --- | --- | --- | --- | --- |
| `store` (L40–45) | Crear una ficha para un usuario existente | `StoreClienteRequest $request` | Pasa los datos validados a `ClienteService::create` → `BaseRepository::create` | 201 con la ficha | 403 sin `clientes.gestionar` (ruta); 422 si el usuario no existe o ya tiene ficha |
| `update` (L47–64) | Editar una ficha | `UpdateClienteRequest $request`, `int $cliente` (`cliente_id`) | `$registro`: ficha visible (404 si no); `Gate::authorize('update', $registro)` (`ClientePolicy::update` → `clientes.gestionar`); `$updated`: resultado de `ClienteService::update`, que comprueba existencia y llama a `BaseRepository::update` | 200 `Cliente actualizado correctamente` | 404; 403 “No tienes permiso para modificar clientes.”; 422 (recordar que `usuario_id` es **obligatorio** al editar, ver `referencia/requests.md`) |
| `destroy` (L66–83) | Eliminar una ficha | `Request $request`, `int $id` | Ficha visible; `Gate::authorize('delete', ...)`; `$deleted`: resultado de `ClienteService::delete` | 200 `Cliente eliminado correctamente` | 404; 403. Borrado lógico (`Cliente` usa `SoftDeletes`) |

A diferencia de `CitaController`, aquí el guardado sí pasa por el repositorio (`ClienteService::update` → `BaseRepository::update`). Los métodos `porNombre`, `porApellido` y `porDocumento` (L85–104) **no tienen ruta**: llaman a las búsquedas de `ClienteService` sin filtro de visibilidad ni autorización; no participan en el funcionamiento normal.

---

## 4. `EmpleadoController`

**Archivo:** `backend/app/Http/Controllers/EmpleadoController.php` · **Patrón:** A · **Rutas:** `api.php` L56–61.

**Responsabilidad.** Atender las peticiones sobre fichas de empleado y **ocultar los datos sensibles** según los permisos del usuario.

```php
    public function index(Request $request): JsonResponse
    {
        $empleados = $this->empleadoService->getAll();

        return response()->json($empleados->makeHidden($this->camposOcultos($request->user())));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $empleado = $this->empleadoService->getById($id);

        if (! $empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json($empleado->makeHidden($this->camposOcultos($request->user())));
    }

    /**
     * Campos que el usuario no puede ver según sus permisos.
     *
     * @return array<int, string>
     */
    private function camposOcultos(Usuario $usuario): array
    {
        $ocultos = [];

        if (! $usuario->tienePermiso('empleados.ver_contacto')) {
            array_push($ocultos, 'documento', 'correo', 'telefono');
        }

        if (! $usuario->tienePermiso('empleados.ver_salario')) {
            $ocultos[] = 'salario';
        }

        return $ocultos;
    }
```

Fuente: `backend/app/Http/Controllers/EmpleadoController.php` L24–60.

| Método | Propósito | Parámetros | Variables y bloques | Devuelve | Errores |
| --- | --- | --- | --- | --- | --- |
| `index` (L24–29) | Listar empleados | `Request $request` | `$empleados`: colección de `EmpleadoService::getAll()`. `makeHidden([...])` oculta, **en la respuesta**, los atributos que devuelve `camposOcultos` | 200 con la colección | 403 sin `empleados.ver` (ruta) |
| `show` (L31–40) | Consultar uno | `Request $request`, `int $id` | `$empleado`: `?Empleado`; 404 si no existe; luego `makeHidden` | 200 | 404 `Empleado no encontrado` |
| `camposOcultos` (L47–60, privado) | Calcular qué columnas no puede ver el usuario | `Usuario $usuario` | `$ocultos` empieza vacío. Sin `empleados.ver_contacto`, `array_push` agrega `documento`, `correo` y `telefono`. Sin `empleados.ver_salario`, agrega `salario` | `array` de nombres de columna | — |

`makeHidden` no borra datos ni cambia la consulta: solo excluye esos atributos al convertir el objeto a JSON. Resultado por rol según `config/permisos.php`: el Administrador ve todo; el Recepcionista ve contacto pero no salario; Estilista, Esteticista y Cliente no ven contacto ni salario.

```php
    public function store(StoreEmpleadoRequest $request): JsonResponse
    {
        $empleado = $this->empleadoService->create($request->validated());

        return response()->json($empleado, 201);
    }

    public function update(UpdateEmpleadoRequest $request, int $id): JsonResponse
    {
        $updated = $this->empleadoService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json(['message' => 'Empleado actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->empleadoService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/EmpleadoController.php` L62–89.

| Método | Propósito | Qué hace | Devuelve | Errores |
| --- | --- | --- | --- | --- |
| `store` (L62–67) | Crear | `EmpleadoService::create` con datos de `StoreEmpleadoRequest` | 201 con el empleado **completo** (sin `makeHidden`; solo quien tiene `empleados.gestionar` llega aquí) | 403 (ruta); 422 |
| `update` (L69–78) | Editar | `$updated`: `EmpleadoService::update` → `BaseRepository::update` (`null` si no existe) | 200 con mensaje | 404; 403; 422 |
| `destroy` (L80–89) | Eliminar | `$deleted`: `EmpleadoService::delete`, que devuelve `true` si el repositorio no devolvió `null` | 200 con mensaje | 404. Borrado lógico. Las citas del empleado no se modifican (la llave foránea con `onDelete('cascade')` de `citas.empleado_id` solo actuaría ante un borrado físico) |

`disponibles`, `porRol` y `cambiarDisponibilidad` (L91–113) **no tienen ruta**. `cambiarDisponibilidad` lee `request()->boolean('disponibilidad')` (convierte el valor enviado en `true`/`false`) y llama a `EmpleadoRepository::cambiarDisponibilidad`. No participan en el funcionamiento normal.

---

## 5. `ServicioController`

**Archivo:** `backend/app/Http/Controllers/ServicioController.php` · **Patrón:** A · **Rutas:** `api.php` L63–69.

**Responsabilidad.** Gestionar el catálogo de servicios del spa (corte, manicura, etc.).

```php
class ServicioController extends Controller
{
    public function __construct(
        protected ServicioService $servicioService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->servicioService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $servicio = $this->servicioService->getById($id);

        if (! $servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json($servicio);
    }

    public function store(StoreServicioRequest $request): JsonResponse
    {
        $servicio = $this->servicioService->create($request->validated());

        return response()->json($servicio, 201);
    }

    public function update(UpdateServicioRequest $request, int $id): JsonResponse
    {
        $updated = $this->servicioService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(['message' => 'Servicio actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->servicioService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(['message' => 'Servicio eliminado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/ServicioController.php` L10–59.

| Método | Parámetros | Qué hace | Devuelve | Errores |
| --- | --- | --- | --- | --- |
| Constructor | `ServicioService $servicioService` | Guarda el servicio | — | — |
| `index` | — | `getAll()`: **todos** los servicios no borrados, activos e inactivos | 200 | 403 sin `servicios.ver` |
| `show` | `int $id` | `getById`; 404 si `null` | 200 | 404 `Servicio no encontrado` |
| `store` | `StoreServicioRequest $request` | `create` | 201 | 403 sin `servicios.gestionar`; 422 |
| `update` | `UpdateServicioRequest $request`, `int $id` | `update` → `null` si no existe | 200 con mensaje | 404; 403; 422 |
| `destroy` | `int $id` | `delete`; `ServicioService::delete` convierte el resultado a `bool` (su comentario L37 explica que antes un `null` provocaba un error de tipo) | 200 con mensaje | 404. Borrado lógico |

`activos` y `cambiarEstado` (L61–78) **no tienen ruta**.

---

## 6. `FacturaController`

**Archivo:** `backend/app/Http/Controllers/FacturaController.php` · **Patrón:** B · **Rutas:** `api.php` L77–81.

```php
class FacturaController extends Controller
{
    public function __construct(
        protected FacturaService $facturaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->facturaService->listarPara($request->user()), 200);
    }

    public function store(StoreFacturaRequest $request): JsonResponse
    {
        $factura = $this->facturaService->store($request->validated(), $request->user());

        return response()->json($factura, 201);
    }

    public function show(Request $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        Gate::authorize('view', $factura);

        return response()->json($factura, 200);
    }

    public function update(UpdateFacturaRequest $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        Gate::authorize('update', $factura);

        $factura = $this->facturaService->update($factura, $request->validated(), $request->user());

        return response()->json($factura, 200);
    }

    public function destroy(Request $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        // FacturaPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $factura);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
```

Fuente: `backend/app/Http/Controllers/FacturaController.php` L19–77.

| Método | Parámetros | Qué hace | Devuelve | Errores |
| --- | --- | --- | --- | --- |
| Constructor | `FacturaService $facturaService` | Guarda el servicio | — | — |
| `index` | `Request $request` | `listarPara(usuario)` → `Factura::scopeVisiblePara` | 200 | 403 sin `facturas.ver_todas` ni `facturas.propias` |
| `store` | `StoreFacturaRequest $request` | `FacturaService::store(datos, usuario)` (sección 8.3 de `capas.md`) | 201 con la factura | 403 sin `facturas.gestionar`; 422 |
| `show` | `Request $request`, `int $factura_id` | Factura visible (404 si no); `Gate::authorize('view', ...)` | 200 | 404 `Factura no encontrada` |
| `update` | `UpdateFacturaRequest $request`, `int $factura_id` | Factura visible; `Gate::authorize('update', ...)` (`facturas.gestionar`); `FacturaService::update` | 200 con la factura | 404; 403 “No tienes permiso para modificar facturas.”; 422 |
| `destroy` | `Request $request`, `int $factura_id` | Factura visible; `Gate::authorize('delete', ...)` | — | **Siempre** 403 “Los registros financieros no se eliminan.” (o 404 si no es visible) |

**Sobre `destroy`.** `FacturaPolicy::delete` nunca permite, así que `Gate::authorize` lanza la excepción en L74 y la línea L76 (`return response()->json(... 403)`) **no se alcanza nunca**. El comentario de L73 lo anticipa. El resultado para el usuario es el mismo 403, pero con el mensaje de la policy (observación CAP-17).

---

## 7. `PagoController`

**Archivo:** `backend/app/Http/Controllers/PagoController.php` · **Patrón:** B · **Rutas:** `api.php` L83–87.

```php
class PagoController extends Controller
{
    public function __construct(
        protected PagoService $pagoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->pagoService->listarPara($request->user()), 200);
    }

    public function store(StorePagoRequest $request): JsonResponse
    {
        $pago = $this->pagoService->store($request->validated(), $request->user());

        return response()->json($pago, 201);
    }

    public function show(Request $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        Gate::authorize('view', $pago);

        return response()->json($pago, 200);
    }

    public function update(UpdatePagoRequest $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        Gate::authorize('update', $pago);

        $pago = $this->pagoService->update($pago, $request->validated(), $request->user());

        return response()->json($pago, 200);
    }

    public function destroy(Request $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        // PagoPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $pago);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
```

Fuente: `backend/app/Http/Controllers/PagoController.php` L19–77.

Estructura idéntica a `FacturaController`, con `PagoService` y `PagoPolicy`:

| Método | Particularidad | Errores |
| --- | --- | --- |
| `index` | Alcance `Pago::scopeVisiblePara` (por citas del cliente) | 403 sin `pagos.ver_todos` ni `pagos.propios` |
| `store` | `PagoService::store`: registrar con estado distinto de `pendiente` exige `pagos.aprobar` | 403 sin `pagos.registrar` (ruta) o sin `pagos.aprobar` (servicio); 422 |
| `show` | Pago visible + `PagoPolicy::view` | 404 `Pago no encontrado` |
| `update` | `PagoPolicy::update` exige `pagos.registrar` y estado `pendiente`; `PagoService::update` exige `pagos.aprobar` si cambia el estado | 404; 403; **409** si el pago ya no está pendiente; 422 |
| `destroy` | Siempre denegado por `PagoPolicy::delete`; L76 no se alcanza (CAP-17) | 403 o 404 |

---

## 8. `FacturaServicioController`

**Archivo:** `backend/app/Http/Controllers/FacturaServicioController.php` · **Patrón:** B · **Rutas:** `api.php` L89–101.

**Responsabilidad.** Gestionar las líneas del detalle de una factura (qué servicios y cuántas veces). Como la línea se identifica por dos números, sus métodos reciben `$factura_id` y `$servicio_id`.

```php
class FacturaServicioController extends Controller
{
    public function __construct(
        protected FacturaServicioService $facturaServicioService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->facturaServicioService->listarPara($request->user()), 200);
    }

    public function store(StoreFacturaServicioRequest $request): JsonResponse
    {
        try {
            $registro = $this->facturaServicioService->store($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($registro, 201);
    }

    public function show(Request $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        Gate::authorize('view', $registro);

        return response()->json($registro, 200);
    }

    public function update(UpdateFacturaServicioRequest $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        Gate::authorize('update', $registro);

        $registro = $this->facturaServicioService->update($registro, $request->validated(), $request->user());

        return response()->json($registro, 200);
    }

    public function destroy(Request $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        // FacturaServicioPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $registro);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
```

Fuente: `backend/app/Http/Controllers/FacturaServicioController.php` L19–81.

| Método | Parámetros | Qué hace | Devuelve | Errores |
| --- | --- | --- | --- | --- |
| `index` | `Request $request` | `listarPara` → `FacturaServicio::scopeVisiblePara` (líneas de facturas visibles) | 200 | 403 (ruta `can:viewAny`) |
| `store` | `StoreFacturaServicioRequest $request` | `try`: `FacturaServicioService::store` (sección 8.8 de `capas.md`); `catch InvalidArgumentException`: el servicio ya estaba en la factura | 201 con la línea | **409** si la línea ya existe; 403 sin `facturas.gestionar`; 422; si la factura o el servicio no existen pese a la validación, `findOrFail` lanzaría un error de “no encontrado” |
| `show` | `$request`, `int $factura_id`, `int $servicio_id` | `buscarVisible(factura, servicio, usuario)`; 404 si no; `Gate::authorize('view', ...)` | 200 | 404 `Registro no encontrado` |
| `update` | `UpdateFacturaServicioRequest`, ambos identificadores | Línea visible; `Gate::authorize('update', ...)`; `FacturaServicioService::update` (cambia solo la cantidad y recalcula la factura) | 200 con la línea | 404; 403; 422 |
| `destroy` | `$request`, ambos identificadores | Línea visible; `Gate::authorize('delete', ...)`, que siempre deniega; L80 no se alcanza (CAP-17) | — | 403 o 404 |

Estas rutas no tienen `can` en `show`, `update` ni `destroy`; la autorización está en el controlador.

---

## 9. `RolController`

**Archivo:** `backend/app/Http/Controllers/RolController.php` · **Patrón:** A · **Ruta:** `apiResource('roles', ...)` dentro del grupo `EnsureAdministrator` (`api.php` L104–107). Solo el Administrador llega a estos métodos.

```php
class RolController extends Controller
{
    public function __construct(
        protected RolService $rolService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->rolService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $rol = $this->rolService->getById($id);

        if (! $rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json($rol);
    }

    public function store(StoreRolRequest $request): JsonResponse
    {
        $rol = $this->rolService->create($request->validated());

        return response()->json($rol, 201);
    }

    public function update(UpdateRolRequest $request, int $id): JsonResponse
    {
        $updated = $this->rolService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['message' => 'Rol actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->rolService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['message' => 'Rol eliminado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/RolController.php` L11–60.

| Método | Parámetros | Qué hace | Devuelve | Errores y efectos |
| --- | --- | --- | --- | --- |
| `index` | — | `RolService::getAll()` → `BaseRepository::all()` | 200 con todos los roles | — |
| `show` | `int $id` | `getById` → `find` | 200 o 404 `Rol no encontrado` | — |
| `store` | `StoreRolRequest $request` | `create` | 201 | 422. Un rol creado aquí **no tendrá permisos** mientras su nombre no se agregue a `config/permisos.php`: sus usuarios no podrán operar (`Usuario::estaHabilitado`) |
| `update` | `UpdateRolRequest $request`, `int $id` | `update` | 200 con mensaje o 404 | Renombrar un rol (por ejemplo, `Cliente`) lo desconecta de `config/permisos.php` y deshabilita a todos sus usuarios en la siguiente petición. Desactivarlo (`estado = false`) tiene el mismo efecto |
| `destroy` | `int $id` | `delete`. `Rol` no usa `SoftDeletes`: borrado físico | 200 con mensaje o 404 | La llave foránea `usuarios.rol_id` tiene `onDelete('restrict')` (migración `02_usuario_table.php` L17–20): según la migración, la base de datos rechazaría borrar un rol con usuarios. El controlador no captura ese error (no verificado) |

`activos`, `porNombre` y `cambiarEstado` (L62–87) **no tienen ruta**. `cambiarEstado` pasa `request()->input('estado')` a un parámetro tipado `bool` de `RolService::cambiarEstado`; con un valor que PHP no pueda convertir, se produciría un error de tipo (no verificado; sin ruta).

---

## 10. `UsuarioController`

**Archivo:** `backend/app/Http/Controllers/UsuarioController.php` · **Patrón:** A · **Ruta:** grupo `EnsureAdministrator` (`api.php` L106).

```php
class UsuarioController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->usuarioService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $usuario = $this->usuarioService->getById($id);

        if (! $usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = $this->usuarioService->create($request->validated());

        return response()->json($usuario, 201);
    }

    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        $updated = $this->usuarioService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->usuarioService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/UsuarioController.php` L10–59.

| Método | Parámetros | Qué hace | Devuelve | Errores y efectos |
| --- | --- | --- | --- | --- |
| `index` | — | `UsuarioService::getAll()` | 200 con todos los usuarios. `password` y `remember_token` no salen en el JSON porque el modelo los declara en `$hidden` (`Usuario.php` L39–42) | — |
| `show` | `int $id` | `getById` | 200 o 404 `Usuario no encontrado` | — |
| `store` | `StoreUsuarioRequest $request` | `create` → `UsuarioRepository::create` (cifra la contraseña); al guardar, `Usuario::booted` calcula `administrador_unico` | 201 con el usuario | 403 (request y middleware); 422 |
| `update` | `UpdateUsuarioRequest $request`, `int $id` | `update` → `UsuarioRepository::update` (cifra la contraseña si llega; la conserva si llega vacía) | 200 con mensaje o 404 | 422. `UpdateUsuarioRequest` exige todos los campos salvo `telefono` y `password` |
| `destroy` | `int $id` | `delete`. `Usuario` no usa `SoftDeletes`: borrado físico | 200 con mensaje o 404 | Según las migraciones, `clientes.usuario_id` tiene `onDelete('cascade')` (`03_cliente_table.php` L17–21) y `citas.cliente_id` también (`06_cita_table.php` L43–46): borrar un usuario borraría su ficha y sus citas, salvo que existan pagos de esas citas, cuya llave tiene `restrictOnDelete()` (`07_ pago.php` L33–36). Nada de esto se ejecutó ni se captura en el controlador |

`porRol`, `porEstatus` y `porNombre` (L61–74) **no tienen ruta**.

---

## 11. `ProveedorController`

**Archivo:** `backend/app/Http/Controllers/ProveedorController.php` · **Patrón:** C · **Rutas:** `api.php` L115–129.

```php
class ProveedorController extends Controller
{
    public function __construct(private ProveedorService $proveedorService)
    {
    }
//index, es para listar todos los registros 
    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->proveedorService->list()
        ], 200);
    }
//el store es para incertar o crear un nuevo registro 
    public function store(StoreProveedorRequest $datos)
    {
        $registroInsertado = $this->proveedorService->store($datos->validated());
        return response()->json([
            'success' => 'El proveedor se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }
//es para trer un solo registro por medio del id 
    public function show(int $id)
    {

        $dato = $this->proveedorService->show($id);

        if (!$dato) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }


        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $dato
        ], 200);
    }
//el update sirve para eliminar un registro 
    public function update(UpdateProveedorRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->proveedorService->update($id, $datosActualizar->validated());

        if (! $registroActualizado) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }

        return response()->json([
            'success' => 'El proveedor se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }
//destroy sirve para eliminar un registro
    public function destroy(int $id)
    {
        if (! $this->proveedorService->destroy($id)) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }

        return response()->json([
            'success' => 'El proveedor se eliminó correctamente'
        ], 200);
    }
```

Fuente: `backend/app/Http/Controllers/ProveedorController.php` L8–79.

Los métodos CRUD siguen exactamente la misma estructura que `ProductoController` (sección 7.3 y 7.5 de `capas.md`), con `ProveedorService`:

| Método | Parámetros | Delega en | Respuesta |
| --- | --- | --- | --- |
| `index` | — | `list()` | 200 `{success, data}` |
| `store` | `StoreProveedorRequest $datos` | `store` | 201 `{success, data}` |
| `show` | `int $id` | `show` | 200 o 404 `{not_found, message}` |
| `update` | `UpdateProveedorRequest $datosActualizar`, `int $id` | `update` | 200 o 404 |
| `destroy` | `int $id` | `destroy` | 200 o 404. Borrado lógico; los productos del proveedor no se modifican |

**Comentarios del código.** Las líneas L13, L21, L30, L49 y L66 son comentarios de una línea que describen cada método. El de L49 dice “el update sirve para eliminar un registro”, lo cual no corresponde a lo que hace el método, que actualiza (observación CAP-18).

```php
    public function getByContacto(string $contacto)
    {
        return response()->json([
            'success' => 'Proveedores encontrados',
            'data' => $this->proveedorService->getByContacto($contacto)
        ], 200);
    }

    public function getByEmail(string $email)
    {
        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $this->proveedorService->getByEmail($email)
        ], 200);
    }

    public function getByRegistroTributario(string $registro_tributario)
    {
        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $this->proveedorService->getByRegistroTributario($registro_tributario)
        ], 200);
    }
```

Fuente: `backend/app/Http/Controllers/ProveedorController.php` L81–103.

| Método | Ruta | Parámetro | Delega en | Respuesta |
| --- | --- | --- | --- | --- |
| `getByContacto` | `GET /api/proveedores/contacto/{contacto}` | `string $contacto` | `ProveedorService::getByContacto` → `ProveedorRepository::getByContacto` | 200 con `data`: colección o **`null`** si no hay resultados |
| `getByEmail` | `GET /api/proveedores/email/{email}` | `string $email` | `getByEmail` | 200 con `data`: colección o `null` |
| `getByRegistroTributario` | `GET /api/proveedores/registro-tributario/{registro_tributario}` | `string $registro_tributario` | `getByRegistroTributario` → `getByRegistro_tributario` | 200 con `data`: un proveedor o `null` |

Las búsquedas **no responden 404** cuando no encuentran nada: responden 200 con `data: null` (observación CAP-19). Exigen `inventario.ver` en la ruta y no usan request.

---

## 12. `ProductoController`: constructor, `index` y búsquedas

```php
    public function __construct(private ProductoService $productoService) {}

    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->productoService->list()
        ], 200);
    }
```

Fuente: `backend/app/Http/Controllers/ProductoController.php` L10–18.

El constructor usa promoción de propiedades con `private`. `index` responde 200 `{success, data}` con `ProductoService::list()`. Ruta: `GET /api/producto` con `can:viewAny` (`inventario.ver`).

```php
    public function getByNombre(string $nombre)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByNombre($nombre)
        ], 200);
    }

    public function getByFechaRegistro(string $fecha_registro)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByFechaRegistro($fecha_registro)
        ], 200);
    }

    public function getByProveedor(int $proveedor_id)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByProveedor($proveedor_id)
        ], 200);
    }
```

Fuente: `backend/app/Http/Controllers/ProductoController.php` L81–103.

| Método | Ruta | Parámetro | Consulta final (en `ProductoRepository`) | Respuesta |
| --- | --- | --- | --- | --- |
| `getByNombre` | `GET /api/producto/nombre/{nombre}` | `string $nombre` | `where('nombre', ...)`: coincidencia exacta | 200 con colección o `null` |
| `getByFechaRegistro` | `GET /api/producto/fecha-registro/{fecha_registro}` | `string $fecha_registro` | `where('fecha_registro', ...)` | 200 con colección o `null` |
| `getByProveedor` | `GET /api/producto/proveedor/{proveedor_id}` | `int $proveedor_id` (la ruta exige número) | `where('proveedor_id', ...)` | 200 con colección o `null` |

---

## 13. `MovimientoInventarioController`

**Archivo:** `backend/app/Http/Controllers/MovimientoInventarioController.php` · **Patrón:** C · **Rutas:** `api.php` L148–161.

```php
class MovimientoInventarioController extends Controller
{
    public function __construct(private MovimientoInventarioService $movimientoInventarioService) {}

    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->movimientoInventarioService->list()
        ], 200);
    }

    public function store(StoreMovimientoInventarioRequest $datos)
    {
        $registroInsertado = $this->movimientoInventarioService->store($datos->validated());
        return response()->json([
            'success' => 'El movimiento se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }

    public function show(int $id)
    {
        $dato = $this->movimientoInventarioService->show($id);

        if (!$dato) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }


        return response()->json([
            'success' => 'MovimientoInventario encontrado',
            'data' => $dato
        ], 200);
    }	
    

    public function update(UpdateMovimientoInventarioRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->movimientoInventarioService->update($id, $datosActualizar->validated());

        if (! $registroActualizado) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }

        return response()->json([
            'success' => 'El movimiento se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }

    public function destroy(int $id)
    {
        if (! $this->movimientoInventarioService->destroy($id)) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }

        return response()->json([
            'success' => 'El movimiento se eliminó correctamente'
        ], 200);
    }

    public function getByFechaHora(string $fecha_hora)
    {
        return response()->json([
            'success' => 'Movimientos encontrados',
            'data' => $this->movimientoInventarioService->getByFechaHora($fecha_hora)
        ], 200);
    }

    public function getByProducto(int $producto_id)
    {
        return response()->json([
            'success' => 'Movimientos encontrados',
            'data' => $this->movimientoInventarioService->getByProducto($producto_id)
        ], 200);
    }
```

Fuente: `backend/app/Http/Controllers/MovimientoInventarioController.php` L8–93.

| Método | Parámetros | Delega en | Respuesta | Nota |
| --- | --- | --- | --- | --- |
| `index` | — | `list()` | 200 `{success, data}` | — |
| `store` | `StoreMovimientoInventarioRequest $datos` | `store` | 201 | **No modifica `producto.cantidad`** (CAP-09) |
| `show` | `int $id` | `show` | 200 o 404 | — |
| `update` | `UpdateMovimientoInventarioRequest $datosActualizar`, `int $id` | `update` | 200 o 404 | Tampoco ajusta el stock |
| `destroy` | `int $id` | `destroy` | 200 o 404 | Borrado lógico |
| `getByFechaHora` | `string $fecha_hora` | `getByFechaHora` → `getByFecha_Hora` | 200 con colección o `null` | Compara la fecha y hora exactas (`where('fecha_hora', ...)`) |
| `getByProducto` | `int $producto_id` | `getByProducto` | 200 con colección o `null` | — |

Todas exigen `inventario.ver` (lectura) o `inventario.gestionar` (escritura) en la ruta: solo el Administrador.

---

## 14. `CitaController`: métodos sin ruta

```php
    /*
    |--------------------------------------------------------------------------
    | Métodos sin ruta registrada
    |--------------------------------------------------------------------------
    | Se conservan tal como estaban. Si en el futuro se les asigna una ruta,
    | deben filtrar por Cita::visiblePara() y autorizar con CitaPolicy.
    */

    public function porCliente(int $clienteId): JsonResponse
    {
        return response()->json($this->citaService->getByCliente($clienteId));
    }

    public function porEmpleado(int $empleadoId): JsonResponse
    {
        return response()->json($this->citaService->getByEmpleado($empleadoId));
    }

    public function porEstado(string $estado): JsonResponse
    {
        return response()->json($this->citaService->getByEstado($estado));
    }

    public function porRangoFechas(): JsonResponse
    {
        $fechaInicio = request()->query('fecha_inicio');
        $fechaFin = request()->query('fecha_fin');

        return response()->json(
            $this->citaService->getByRangoFechas($fechaInicio, $fechaFin)
        );
    }

    public function cambiarEstado(int $id): JsonResponse
    {
        $cambiado = $this->citaService->cambiarEstado($id, request()->input('estado'));

        if (! $cambiado) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
```

Fuente: `backend/app/Http/Controllers/CitaController.php` L88–130.

| Método | Parámetros | Qué haría | Por qué no participa |
| --- | --- | --- | --- |
| `porCliente` | `int $clienteId` | Devolver las citas de un cliente (`CitaService::getByCliente`) | Sin ruta |
| `porEmpleado` | `int $empleadoId` | Citas de un empleado | Sin ruta |
| `porEstado` | `string $estado` | Citas en un estado | Sin ruta |
| `porRangoFechas` | — (lee `fecha_inicio` y `fecha_fin` de la URL con `request()->query(...)`) | Citas entre dos fechas | Sin ruta. Si faltara un parámetro, se pasaría `null` a un parámetro tipado `string` del servicio (no verificado) |
| `cambiarEstado` | `int $id` (lee `estado` del cuerpo) | Cambiar el estado con `CitaRepository::cambiarEstado` | Sin ruta. No aplica `CitaPolicy` ni disponibilidad (CAP-08) |

El comentario del propio código (L88–94) advierte que, si se les asigna una ruta, deben filtrar por `visiblePara` y autorizar con `CitaPolicy`.
