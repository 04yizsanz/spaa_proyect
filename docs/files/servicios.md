# Referencia: servicios

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Nada se ejecutó.

Este documento completa la [capa 4 de `backend/capas.md`](../capas.md#8-capa-4--servicio). Allí están explicados en detalle `CitaService` (métodos con reglas), `FacturaService`, `FacturaServicioService::store`, `PagoService::store` y `update`, y `ProductoService`.

## Por qué existen servicios sin reglas propias

Siete servicios (`ClienteService`, `EmpleadoService`, `ServicioService`, `RolService`, `UsuarioService`, `ProveedorService`, `MovimientoInventarioService`) solo **delegan** en su repositorio. Existen por tres motivos que se desprenden de la estructura del código:

1. **Uniformidad:** todos los controladores de la API hablan con un servicio, nunca con un repositorio. Así, el controlador no cambia si algún día se agrega una regla.
2. **Desacoplamiento:** el servicio recibe una **interfaz**; el controlador no sabe cómo se guardan los datos.
3. **Punto de extensión:** por ejemplo, la regla pendiente de actualizar el stock al registrar un movimiento (CAP-09) tendría su lugar natural en `MovimientoInventarioService::store`.

Cuando un método “comprueba existencia” (`getById` o `find` antes de `update`/`delete`), es redundante con `BaseRepository`, que ya devuelve `null` si no encuentra el registro; no cambia el resultado.

---

## 1. `ClienteService`

```php
class ClienteService
{
    public function __construct(
        protected ClienteInterface $clienteRepository
    ) {}

    /**
     * Clientes que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor)
    {
        return $this->clienteRepository->visiblesPara($actor);
    }

    /**
     * Cliente por id solo si el usuario puede verlo; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor)
    {
        return $this->clienteRepository->buscarVisible($id, $actor);
    }

    public function getAll()
    {
        return $this->clienteRepository->all();
    }

    public function getById(int $id)
    {
        return $this->clienteRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->clienteRepository->create($data);
    }

    public function update(int $id, array $data)
{
    $cliente = $this->clienteRepository->getById($id);

    if (! $cliente) {
        return null;
    }

    return $this->clienteRepository->update($data, $id);
}

    public function delete(int $id)
    {
        $cliente = $this->clienteRepository->find($id);

        if (! $cliente) {
            return null;
        }

        return $this->clienteRepository->delete($id);
    }

    public function getByNombre(string $nombre)
    {
        return $this->clienteRepository->getByName($nombre);
    }

    public function getByApellido(string $apellido)
    {
        return $this->clienteRepository->getByLastname($apellido);
    }

    public function getByDocumento(string $documento)
    {
        return $this->clienteRepository->getByDocument($documento);
    }
}
```

Fuente: `backend/app/Services/ClienteService.php` L8–81.

| Método | Parámetros | Delega en | Devuelve | Uso real |
| --- | --- | --- | --- | --- |
| Constructor | `ClienteInterface $clienteRepository` (`protected`) | — | — | Contenedor |
| `listarPara` | `Usuario $actor` | `visiblesPara($actor)` | Colección visible | `ClienteController::index` |
| `buscarVisible` | `int $id`, `Usuario $actor` | `buscarVisible` | `?Cliente` | `ClienteController::show`, `update`, `destroy` |
| `getAll` | — | `all()` (alias de `BaseRepository`, no declarado en la interfaz, CAP-03) | Colección | **Ningún código lo llama** |
| `getById` | `int $id` | `find($id)` | `?Cliente` | Ningún código lo llama |
| `create` | `array $data` | `create` | `Cliente` | `ClienteController::store` |
| `update` | `int $id`, `array $data` | `getById` (si `null`, devuelve `null`) y `update($data, $id)` | `?Cliente` | `ClienteController::update` |
| `delete` | `int $id` | `find` y `delete` | `null` o `bool` | `ClienteController::destroy` |
| `getByNombre`, `getByApellido`, `getByDocumento` | `string` | `getByName`, `getByLastname`, `getByDocument` | Colección / `?Cliente` | Métodos sin ruta de `ClienteController` |

`listarPara` y `buscarVisible` no declaran tipo de retorno, a diferencia de los de `CitaService`.

## 2. `EmpleadoService`

```php
class EmpleadoService
{
    public function __construct(
        protected EmpleadoInterface $empleadoRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->empleadoRepository->getAll();
    }

    public function getById(int $id): ?Empleado
    {
        return $this->empleadoRepository->getById($id);
    }

    public function create(array $data): Empleado
    {
        return $this->empleadoRepository->create($data);
    }

    public function update(int $id, array $data)
    {
    return $this->empleadoRepository->update($data, $id);
    }

    public function delete(int $id): bool
    {
    $registro = $this->empleadoRepository->delete($id);

    return $registro !== null;
    }

    public function getDisponibles(): Collection
    {
        return $this->empleadoRepository->getDisponibles();
    }

    public function getByRol(string $rol): Collection
    {
        return $this->empleadoRepository->getByRol($rol);
    }

    public function getByDocumento(string $documento): ?Empleado
    {
        return $this->empleadoRepository->getByDocumento($documento);
    }

    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool
    {
        return $this->empleadoRepository->cambiarDisponibilidad($id, $disponibilidad);
    }
}
```

Fuente: `backend/app/Services/EmpleadoService.php` L9–61.

| Método | Delega en | Devuelve | Uso real |
| --- | --- | --- | --- |
| `getAll()` | `getAll()` | `Collection` | `EmpleadoController::index` |
| `getById(int $id)` | `getById` | `?Empleado` | `EmpleadoController::show` |
| `create(array $data)` | `create` | `Empleado` | `EmpleadoController::store` |
| `update(int $id, array $data)` | `update($data, $id)` | `?Empleado` (sin tipo declarado) | `EmpleadoController::update` |
| `delete(int $id): bool` | `delete($id)`; `$registro !== null` convierte “no encontrado” (`null`) en `false` y cualquier otro resultado en `true` | `bool` | `EmpleadoController::destroy` |
| `getDisponibles()`, `getByRol(string $rol)`, `getByDocumento(string $documento)`, `cambiarDisponibilidad(int $id, bool $disponibilidad)` | Métodos del mismo nombre en `EmpleadoRepository` | `Collection`, `?Empleado`, `bool` | Sin ruta (o sin ningún llamador, en `getByDocumento`) |

## 3. `ServicioService`

```php
class ServicioService
{
    public function __construct(
        protected ServicioInterface $servicioRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->servicioRepository->getAll();
    }

    public function getById(int $id): ?Servicio
    {
        return $this->servicioRepository->getById($id);
    }

    public function create(array $data): Servicio
    {
        return $this->servicioRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->servicioRepository->update($data,$id);
    }

    public function delete(int $id): bool
    {
        // El repositorio devuelve null si el servicio no existe (antes provocaba un TypeError → 500).
        return (bool) $this->servicioRepository->delete($id);
    }

    public function getActivos(): Collection
    {
        return $this->servicioRepository->getActivos();
    }

    public function getByNombre(string $nombre): ?Servicio
    {
        return $this->servicioRepository->getByNombre($nombre);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        return $this->servicioRepository->cambiarEstado($id, $activo);
    }
}
```

Fuente: `backend/app/Services/ServicioService.php` L9–55.

Misma estructura que `EmpleadoService`. `delete` convierte el resultado con `(bool)`; su comentario (L37) explica que antes un `null` producía un error de tipo (500). `getActivos`, `getByNombre` y `cambiarEstado` no tienen ruta que los alcance (`getByNombre` no lo llama ningún código).

## 4. `RolService`

```php
class RolService
{
    public function __construct(
        protected RolInterface $rolRepository
    ) {}

    public function getAll()
    {
        return $this->rolRepository->all();
    }

    public function getById(int $id)
    {
        return $this->rolRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->rolRepository->create($data);
    }

   public function update(int $id, array $data)
    {
    $rol = $this->rolRepository->getById($id);

    if (! $rol) {
        return null;
    }

    return $this->rolRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $rol = $this->rolRepository->find($id);

        if (! $rol) {
            return null;
        }

        return $this->rolRepository->delete($id);
    }

    public function getActivos()
    {
        return $this->rolRepository->findActivos();
    }

    public function getByNombre(string $nombre)
    {
        $rol = $this->rolRepository->getByName($nombre);

        if (! $rol) {
            throw new InvalidArgumentException("No existe un rol con el nombre '{$nombre}'.");
        }

        return $rol;
    }

    public function cambiarEstado(int $id, bool $estado)
    {
        return $this->rolRepository->updateEstado($id, $estado);
    }
}
```

Fuente: `backend/app/Services/RolService.php` L8–71.

| Método | Delega en | Devuelve | Errores | Uso real |
| --- | --- | --- | --- | --- |
| `getAll()` | `all()` | Colección | — | `RolController::index` |
| `getById(int $id)` | `find($id)` | `?Rol` | — | `RolController::show` |
| `create(array $data)` | `create` | `Rol` | — | `RolController::store` |
| `update(int $id, array $data)` | `getById` + `update` | `?Rol` | — | `RolController::update` |
| `delete(int $id)` | `find` + `delete` | `null` o `bool` | Ver efecto de la llave foránea en `referencia/controladores-api.md` | `RolController::destroy` |
| `getActivos()` | `findActivos()` | Colección | — | Sin ruta |
| `getByNombre(string $nombre)` | `getByName` | `Rol` | Lanza `InvalidArgumentException("No existe un rol con el nombre '...'.")` si no existe (L60–62); `RolController::porNombre` la convierte en 404 | Sin ruta |
| `cambiarEstado(int $id, bool $estado)` | `updateEstado` | `?Rol` | — | Sin ruta |

`getByNombre` es el único método de estos siete servicios que **lanza una excepción** propia en lugar de devolver `null`.

## 5. `UsuarioService`

```php
class UsuarioService
{
    public function __construct(
        protected UsuarioInterface $usuarioRepository
    ) {}

    public function getAll()
    {
        return $this->usuarioRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->usuarioRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->usuarioRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $usuario = $this->usuarioRepository->getById($id);
        if (! $usuario) return null;
        return $this->usuarioRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $usuario = $this->usuarioRepository->getById($id);
        if (! $usuario) return null;
        return $this->usuarioRepository->delete($id);
    }

    public function getByRol(int $rolId)
    {
        return $this->usuarioRepository->getByRol($rolId);
    }

    public function getByEstatus(bool $status)
    {
        return $this->usuarioRepository->getByEstatus($status);
    }

    public function getByNombre(string $nombre)
    {
        return $this->usuarioRepository->getByName($nombre);
    }

    public function getByEmail(string $email)
    {
        return $this->usuarioRepository->getByEmail($email);
    }
}
```

Fuente: `backend/app/Services/UsuarioService.php` L7–61.

| Método | Delega en | Devuelve | Uso real |
| --- | --- | --- | --- |
| `getAll()` | `getAll()` | Colección | `UsuarioController::index` |
| `getById(int $id)` | `getById` | `?Usuario` | `UsuarioController::show` |
| `create(array $data)` | `UsuarioRepository::create` (cifra la contraseña) | `Usuario` | `UsuarioController::store`; `Web/AuthController::register` (L129) |
| `update(int $id, array $data)` | `getById` + `UsuarioRepository::update` | `?Usuario` | `UsuarioController::update` |
| `delete(int $id)` | `getById` + `delete` (borrado físico) | `null` o `bool` | `UsuarioController::destroy` |
| `getByRol`, `getByEstatus`, `getByNombre` | Repositorio | Colección | Sin ruta |
| `getByEmail(string $email)` | `getByEmail` (solo activos) | `?Usuario` | `ApiAuthController::login` (L34); `Web/AuthController::login` (L48) |

`if (! $usuario) return null;` (L31, L38) es la forma abreviada de un `if` de una sola instrucción, sin llaves.

## 6. `ProveedorService`

```php
class ProveedorService
{
    public function __construct(
        private ProveedorInterface $proveedorRepository
    ) {}

    public function list()
    {
        return $this->proveedorRepository->getAll();
    }

    public function show(int $id)
    {
        return $this->proveedorRepository->getById($id);
    }

    public function store(array $data)
    {
        return $this->proveedorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->proveedorRepository->update($data, $id);
    }

    public function destroy(int $id)
    {
        return $this->proveedorRepository->delete($id);
    }

    public function getByContacto(string $contacto)
    {
        return $this->proveedorRepository->getByContacto($contacto);
    }

    public function getByEmail(string $email)
    {
        return $this->proveedorRepository->getByEmail($email);
    }

    public function getByRegistroTributario(string $registro_tributario)
    {
        return $this->proveedorRepository->getByRegistro_tributario($registro_tributario);
    }
}
```

Fuente: `backend/app/Services/ProveedorService.php` L7–52.

Igual que `ProductoService` (sección 8.8 de `capas.md`): `list`, `show`, `store`, `update`, `destroy` delegan en `getAll`, `getById`, `create`, `update`, `delete`. Las búsquedas `getByContacto`, `getByEmail` y `getByRegistroTributario` delegan en el repositorio; la última llama a `getByRegistro_tributario` (nombre con guion bajo en el repositorio y en la interfaz). Las tres tienen ruta (`api.php` L116–121).

## 7. `MovimientoInventarioService`

```php
class MovimientoInventarioService
{
    public function __construct(
        private MovimientoInventarioInterface $movimientoInventarioRepository
    ) {}

    public function list()
    {
        return $this->movimientoInventarioRepository->getAll();
    }

    public function show(int $id)
    {
        return $this->movimientoInventarioRepository->getById($id);
    }

    public function store(array $data)
    {
        return $this->movimientoInventarioRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->movimientoInventarioRepository->update($data, $id);
    }

    public function destroy(int $id)
    {
        return $this->movimientoInventarioRepository->delete($id);
    }

    public function getByFechaHora(string $fecha_hora)
    {
        return $this->movimientoInventarioRepository->getByFecha_Hora($fecha_hora);
    }

    public function getByProducto(int $producto_id)
    {
        return $this->movimientoInventarioRepository->getByProducto($producto_id);
    }
}
```

Fuente: `backend/app/Services/MovimientoInventarioService.php` L7–47.

Misma estructura. `store` y `update` **solo** guardan el movimiento: no leen ni modifican el producto (CAP-09). `getByFechaHora` llama a `getByFecha_Hora` del repositorio; `getByProducto`, a `getByProducto`. Ambas tienen ruta (`api.php` L149–153).

---

## 8. Métodos restantes de los servicios con reglas

### `CitaService`: métodos de paso

```php
    public function getAll(): Collection
    {
        return $this->citaRepository->getAll();
    }

    public function getById(int $id): ?Cita
    {
        return $this->citaRepository->getById($id);
    }
```

Fuente: `backend/app/Services/CitaService.php` L28–36.

```php
    public function delete(int $id): bool
    {
        // El repositorio devuelve null si la cita no existe.
        return (bool) $this->citaRepository->delete($id);
    }

    public function getByCliente(int $clienteId): Collection
    {
        return $this->citaRepository->getByCliente($clienteId);
    }

    public function getByEmpleado(int $empleadoId): Collection
    {
        return $this->citaRepository->getByEmpleado($empleadoId);
    }

    public function getByEstado(string $estado): Collection
    {
        return $this->citaRepository->getByEstado($estado);
    }

    public function getByRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return $this->citaRepository->getByRangoFechas($fechaInicio, $fechaFin);
    }

    public function cambiarEstado(int $codigoCita, string $estado): bool
    {
        return $this->citaRepository->cambiarEstado($codigoCita, $estado);
    }
```

Fuente: `backend/app/Services/CitaService.php` L141–170.

| Método | Delega en | Uso real |
| --- | --- | --- |
| `getAll(): Collection`, `getById(int $id): ?Cita` | `getAll`, `getById` | Ningún código los llama |
| `delete(int $id): bool` | `delete($id)` con `(bool)` | Ningún código lo llama; la eliminación real usa `eliminar(Cita)` |
| `getByCliente`, `getByEmpleado`, `getByEstado`, `getByRangoFechas` | Métodos del mismo nombre en `CitaRepository` | Solo los métodos sin ruta de `CitaController` |
| `cambiarEstado(int $codigoCita, string $estado): bool` | `CitaRepository::cambiarEstado` | Solo `CitaController::cambiarEstado`, sin ruta (CAP-08) |

### `FacturaService`: `list`, `listarPara`, `buscarVisible`, `show`, `getByCliente`

```php
    public function list()
    {
        return $this->facturaRepository->all();
    }

    /**
     * Facturas que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->facturaRepository->visiblesPara($actor);
    }

    /**
     * Factura por id solo si el usuario puede verla; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor): ?Factura
    {
        return $this->facturaRepository->buscarVisible($id, $actor);
    }

    public function show(int $id)
    {
        return $this->facturaRepository->find($id);
    }
```

Fuente: `backend/app/Services/FacturaService.php` L27–51.

| Método | Delega en | Uso real |
| --- | --- | --- |
| `list()` | `all()` | Ningún código lo llama |
| `listarPara(Usuario $actor): Collection` | `visiblesPara` | `FacturaController::index` |
| `buscarVisible(int $id, Usuario $actor): ?Factura` | `buscarVisible` | `FacturaController::show`, `update`, `destroy` |
| `show(int $id)` | `find($id)` | Ningún código lo llama |
| `getByCliente(int $cliente_id)` (L109–112) | `getByCliente`, que devuelve `null` si no hay facturas | Ningún código lo llama |

### `FacturaServicioService`: métodos restantes

```php
    /**
     * Solo se modifica la cantidad; el precio unitario histórico se conserva.
     */
    public function update(FacturaServicio $linea, array $data, ?Usuario $actor = null): FacturaServicio
    {
        $clave = ['factura_id' => $linea->factura_id, 'servicio_id' => $linea->servicio_id];

        $actualizada = DB::transaction(function () use ($clave, $data) {
            $factura = Factura::query()->lockForUpdate()->findOrFail($clave['factura_id']);

            $registro = $this->facturaServicioRepository->update(['cantidad' => (int) $data['cantidad']], $clave);

            $this->facturaService->recalcularTotales($factura);

            return $registro;
        });

        $this->auditar('factura_detalle.actualizado', $actualizada, $actor);

        return $actualizada;
    }
```

Fuente: `backend/app/Services/FacturaServicioService.php` L81–101.

| Aspecto | Detalle |
| --- | --- |
| Propósito de `update` | Cambiar **solo** la cantidad de una línea y recalcular la factura. El comentario L82 lo indica: el precio unitario histórico se conserva. |
| Parámetros | `$linea` (`FacturaServicio`): la línea encontrada y autorizada por el controlador. `$data` (`array`): con `cantidad`. `$actor` (`?Usuario`). |
| Variable `$clave` | Arreglo con `factura_id` y `servicio_id`: la llave compuesta que espera `FacturaServicioRepository::update`. |
| L88–96 | Transacción: bloquea la factura (`lockForUpdate`, modelo directo), actualiza la cantidad por el repositorio y recalcula los totales con `FacturaService`. `$actualizada` es la línea releída. |
| L98 | Audita `factura_detalle.actualizado`. |
| Devuelve | La línea actualizada. La firma declara `FacturaServicio`, pero el repositorio puede devolver `null` si la línea no existe; en el flujo real la línea ya fue encontrada por el controlador. |
| Errores | `findOrFail` si la factura desapareciera entre la búsqueda y la transacción (no capturado). |

| Otros métodos | Delega en | Uso real |
| --- | --- | --- |
| `listarPara(Usuario $actor): Collection` (L33–36) | `visiblesPara` | `FacturaServicioController::index` |
| `buscarVisible(int $facturaId, int $servicioId, Usuario $actor)` (L41–44) | `buscarVisible` | `show`, `update`, `destroy` del controlador |
| `show(array $id)` (L46–49) | `getById($id)` con llave compuesta | Ningún código lo llama |
| `getByFactura`, `getByServicio` (L103–111) | Repositorio; devuelven `null` si no hay líneas | Ningún código los llama |
| `auditar` (L113–122, privado) | `Log::channel('auditoria')` con factura, servicio, cantidad, precio unitario y usuario | `store`, `update` |

### `PagoService`: métodos restantes

```php
class PagoService
{
    public function __construct(
        private PagoInterface $pagoRepository
    ) {}

    public function list()
    {
        return $this->pagoRepository->all();
    }

    /**
     * Pagos que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->pagoRepository->visiblesPara($actor);
    }

    /**
     * Pago por id solo si el usuario puede verlo; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor): ?Pago
    {
        return $this->pagoRepository->buscarVisible($id, $actor);
    }

    public function show(int $pagoId)
    {
        return $this->pagoRepository->find($pagoId);
    }
```

Fuente: `backend/app/Services/PagoService.php` L20–50.

```php
    public function getByCita(int $codigo_cita)
    {
        return $this->pagoRepository->getByCita($codigo_cita);
    }

    public function getByEstado(string $estado)
    {
        return $this->pagoRepository->getByEstado($estado);
    }

    public function getByFechaHora(DateTime $fechaHora)
    {
        return $this->pagoRepository->getByFechaHora($fechaHora);
    }
```

Fuente: `backend/app/Services/PagoService.php` L85–98.

| Método | Delega en | Uso real |
| --- | --- | --- |
| Constructor | Recibe `PagoInterface` (`private`) | Contenedor |
| `list()` | `all()` | Ningún código lo llama |
| `listarPara`, `buscarVisible` | Repositorio con alcance | `PagoController` |
| `show(int $pagoId)` | `find` | Ningún código lo llama |
| `getByCita`, `getByEstado`, `getByFechaHora` | Repositorio; devuelven `null` si no hay resultados | Ningún código los llama |
