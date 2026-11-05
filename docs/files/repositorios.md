# Referencia: repositorios

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Ninguna consulta se ejecutó.

Este documento completa la [capa 6 de `backend/capas.md`](../capas.md#10-capa-6--repositorio). `BaseRepository`, los métodos principales de `CitaRepository`, `UsuarioRepository::create`/`update`/`getByEmail`, `ClienteRepository` y la sobrescritura de `FacturaServicioRepository` están explicados allí.

## Patrones de consulta que se repiten

| Patrón | Código típico | Qué hace | Qué devuelve si no hay resultados |
| --- | --- | --- | --- |
| **Lista simple** | `$this->model->where('columna', $valor)->get()` | `SELECT * ... WHERE columna = valor`, excluyendo borrados lógicamente si el modelo usa `SoftDeletes` | Colección vacía |
| **Lista con `null`** | `get()` seguido de `if ($x->isEmpty()) return null;` | Igual, pero convierte “vacío” en `null` | `null` |
| **Uno** | `->first()` | Primera fila que cumpla | `null` |
| **Cambio de un campo** | `find($id)`, asignar, `save()` | Busca por llave primaria y guarda | `false` o `null` según el repositorio |
| **Visibilidad** | `newQuery()->visiblePara($usuario)` | Aplica el alcance del modelo (sección 11 de `capas.md`) | Colección vacía o `null` |

Todas las comparaciones `where('columna', $valor)` son de **igualdad exacta**: una búsqueda por nombre no encuentra coincidencias parciales.

Cada repositorio tiene el mismo constructor: recibe su modelo y llama a `parent::__construct($modelo)`, que lo guarda en `$this->model` (`BaseRepository.php` L12–15). El contenedor de Laravel crea el modelo y el repositorio cuando un servicio pide la interfaz.

---

## `CitaRepository`: consultas simples

```php
    /**
     * Obtener las citas de un cliente específico.
     */
    public function getByCliente(int $clienteId): Collection
    {
        return $this->model->where('cliente_id', $clienteId)->get();
    }

    /**
     * Obtener las citas asignadas a un empleado específico.
     */
    public function getByEmpleado(int $empleadoId): Collection
    {
        return $this->model->where('empleado_id', $empleadoId)->get();
    }

    /**
     * Obtener citas filtradas por estado.
     */
    public function getByEstado(string $estado): Collection
    {
        return $this->model->where('estado', $estado)->get();
    }

    /**
     * Obtener citas dentro de un rango de fechas.
     */
    public function getByRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return $this->model->whereBetween('fecha', [$fechaInicio, $fechaFin])->get();
    }
```

Fuente: `backend/app/Repositories/CitaRepository.php` L20–50.

| Método | Parámetros | Consulta | Devuelve | Uso real |
| --- | --- | --- | --- | --- |
| `getByCliente` | `int $clienteId` | `where('cliente_id', ...)` | `Collection` | Sin ruta |
| `getByEmpleado` | `int $empleadoId` | `where('empleado_id', ...)` | `Collection` | Sin ruta |
| `getByEstado` | `string $estado` | `where('estado', ...)` | `Collection` | Sin ruta |
| `getByRangoFechas` | `string $fechaInicio`, `string $fechaFin` | `whereBetween('fecha', [inicio, fin])`, ambos extremos incluidos | `Collection` | Sin ruta |

Ninguno aplica `visiblePara`: devolverían citas de cualquier cliente.

## `UsuarioRepository`: constructor y búsquedas

```php
class UsuarioRepository extends BaseRepository implements UsuarioInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }
```

Fuente: `backend/app/Repositories/UsuarioRepository.php` L9–14.

```php
    public function getByRol(int $idRol)
    {
        return $this->model->where('rol_id', $idRol)->get();
    }

    public function getByEstatus(bool $status)
    {
        return $this->model->where('estado', $status)->get();
    }

    public function getByName(string $name)
    {
        return $this->model->where('nombre', $name)->get();
    }
```

Fuente: `backend/app/Repositories/UsuarioRepository.php` L44–57.

| Método | Parámetros | Consulta | Uso real |
| --- | --- | --- | --- |
| `getByRol` | `int $idRol` | `where('rol_id', ...)` | Sin ruta |
| `getByEstatus` | `bool $status` | `where('estado', ...)`: activos o inactivos | Sin ruta |
| `getByName` | `string $name` | `where('nombre', ...)` | Sin ruta |

Devuelven colecciones de `Usuario`; la contraseña no aparece si se convierten a JSON, por `$hidden` del modelo.

## `FacturaRepository`

```php
class FacturaRepository extends BaseRepository implements FacturaInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
    }

    public function getByFechaHora(DateTime $fechaHora)
    {
        $facturas = $this->model->where('fecha_hora', $fechaHora)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getBySubtotal(float $subtotal)
    {
        $facturas = $this->model->where('subtotal', $subtotal)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByImpuestos(float $impuestos)
    {
        $facturas = $this->model->where('impuestos', $impuestos)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByTotal(float $total)
    {
        $facturas = $this->model->where('total', $total)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByCliente(int $cliente_id)
    {
        $facturas = $this->model->where('cliente_id', $cliente_id)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function visiblesPara(Usuario $usuario): Collection
    {
        return $this->model->newQuery()->visiblePara($usuario)->get();
    }

    public function buscarVisible(int $id, Usuario $usuario): ?Factura
    {
        return $this->model->newQuery()->visiblePara($usuario)->find($id);
    }
}
```

Fuente: `backend/app/Repositories/FacturaRepository.php` L11–82.

| Método | Parámetros | Patrón | Uso real |
| --- | --- | --- | --- |
| `getByFechaHora` | `DateTime $fechaHora` | Lista con `null` | Ningún llamador |
| `getBySubtotal`, `getByImpuestos`, `getByTotal` | `float` | Lista con `null` | Ningún llamador |
| `getByCliente` | `int $cliente_id` | Lista con `null` | Ningún llamador con ruta |
| `visiblesPara` | `Usuario $usuario` | Visibilidad | `FacturaController::index` |
| `buscarVisible` | `int $id`, `Usuario $usuario` | Visibilidad + `find($id)` | `show`, `update`, `destroy` |

Comparar importes `float` con igualdad exacta (`getBySubtotal`) puede no encontrar valores que en pantalla parecen iguales, por la representación decimal; los métodos no tienen ruta.

## `PagoRepository`

```php
class PagoRepository extends BaseRepository implements PagoInterface
{
    public function __construct(Pago $model)
    {
        parent::__construct($model);
    }

    public function getByMonto(float $monto)
    {
        $pagos = $this->model->where('monto', $monto)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByFechaHora(DateTime $fecha_hora)
    {
        $pagos = $this->model->where('fecha_hora', $fecha_hora)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByCita(int $codigo_cita)
    {
        $pagos = $this->model->where('codigo_cita', $codigo_cita)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByEstado(string $estado)
    {
        $pagos = $this->model->where('estado', $estado)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function visiblesPara(Usuario $usuario): Collection
    {
        return $this->model->newQuery()->visiblePara($usuario)->get();
    }

    public function buscarVisible(int $id, Usuario $usuario): ?Pago
    {
        return $this->model->newQuery()->visiblePara($usuario)->find($id);
    }
}
```

Fuente: `backend/app/Repositories/PagoRepository.php` L11–71.

| Método | Parámetros | Patrón | Uso real |
| --- | --- | --- | --- |
| `getByMonto` | `float $monto` | Lista con `null` | Ningún llamador |
| `getByFechaHora` | `DateTime $fecha_hora` | Lista con `null` | Ningún llamador con ruta |
| `getByCita` | `int $codigo_cita` | Lista con `null` (por `codigo_cita`, no por `citas_id`) | Ningún llamador con ruta |
| `getByEstado` | `string $estado` | Lista con `null` | Ningún llamador con ruta |
| `visiblesPara`, `buscarVisible` | Como en facturas | Visibilidad (`Pago::scopeVisiblePara`) | `PagoController` |

## `FacturaServicioRepository`: métodos restantes

```php
    public function getByFactura(int $factura_id)
    {
        $registros = $this->model->where('factura_id', $factura_id)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByServicio(int $servicio_id)
    {
        $registros = $this->model->where('servicio_id', $servicio_id)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByCantidad(int $cantidad)
    {
        $registros = $this->model->where('cantidad', $cantidad)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByPrecioUnitario(float $precio_unitario)
    {
        $registros = $this->model->where('precio_unitario', $precio_unitario)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function visiblesPara(Usuario $usuario): Collection
    {
        return $this->model->newQuery()->visiblePara($usuario)->get();
    }

    public function buscarVisible(int $facturaId, int $servicioId, Usuario $usuario): ?FacturaServicio
    {
        return $this->model->newQuery()
            ->visiblePara($usuario)
            ->where('factura_id', $facturaId)
            ->where('servicio_id', $servicioId)
            ->first();
    }

    public function existeLinea(int $facturaId, int $servicioId): bool
    {
        return $this->model->newQuery()
            ->where('factura_id', $facturaId)
            ->where('servicio_id', $servicioId)
            ->exists();
    }
```

Fuente: `backend/app/Repositories/FacturaServicioRepository.php` L55–119.

| Método | Parámetros | Qué hace | Uso real |
| --- | --- | --- | --- |
| `getByFactura`, `getByServicio`, `getByCantidad`, `getByPrecioUnitario` | `int` o `float` | Lista con `null` | Ningún llamador con ruta |
| `visiblesPara` | `Usuario $usuario` | Aplica `FacturaServicio::scopeVisiblePara` (líneas de facturas visibles) | `FacturaServicioController::index` |
| `buscarVisible` | `int $facturaId`, `int $servicioId`, `Usuario $usuario` | Aplica el alcance y filtra por las dos columnas de la llave; `first()` | `show`, `update`, `destroy` |
| `existeLinea` | `int $facturaId`, `int $servicioId` | `exists()` sobre la combinación | `FacturaServicioService::store` (evita líneas duplicadas) |

## `RolRepository`

```php
class RolRepository extends BaseRepository implements RolInterface
{
    public function __construct(Rol $model)
    {
        parent::__construct($model);
    }

    public function findActivos()
    {
        return $this->model->where('estado', true)->get();
    }

    public function getByName(string $nombre)
    {
        return $this->model->where('nombre', $nombre)->first();
    }   

    public function updateEstado(int $id, bool $estado)
    {
        $registro = $this->model->find($id);

        if (! $registro) {
            return null;
        }

        $registro->estado = $estado;
        $registro->save();

        return $registro->fresh();
    }
}
```

Fuente: `backend/app/Repositories/RolRepository.php` L8–38.

| Método | Parámetros | Qué hace | Devuelve | Uso real |
| --- | --- | --- | --- | --- |
| `findActivos` | — | `where('estado', true)` | Colección | Sin ruta |
| `getByName` | `string $nombre` | `where('nombre', ...)->first()` | `?Rol` | Sin ruta |
| `updateEstado` | `int $id`, `bool $estado` | Busca, asigna `estado`, guarda y relee con `fresh()` | `?Rol` | Sin ruta |

## `EmpleadoRepository`

```php
class EmpleadoRepository extends BaseRepository implements EmpleadoInterface
{
    /**
     * Inyecta el modelo Empleado al BaseRepository.
     */
    public function __construct(Empleado $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener todos los empleados disponibles.
     */
    public function getDisponibles(): Collection
    {
        return $this->model->where('disponibilidad', true)->get();
    }

    /**
     * Buscar empleados por rol.
     */
    public function getByRol(string $rol): Collection
    {
        return $this->model->where('rol', $rol)->get();
    }

    /**
     * Buscar un empleado por su documento.
     */
    public function getByDocumento(string $documento): ?Empleado
    {
        return $this->model->where('documento', $documento)->first();
    }

    /**
     * Cambiar el estado de disponibilidad de un empleado.
     */
    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool
    {
        $empleado = $this->model->find($id);

        if (! $empleado) {
            return false;
        }

        $empleado->disponibilidad = $disponibilidad;

        return $empleado->save();
    }
}
```

Fuente: `backend/app/Repositories/EmpleadoRepository.php` L9–58.

| Método | Parámetros | Qué hace | Devuelve | Uso real |
| --- | --- | --- | --- | --- |
| `getDisponibles` | — | `where('disponibilidad', true)` | `Collection` | Sin ruta |
| `getByRol` | `string $rol` | `where('rol', ...)`: el **cargo**, no el rol de acceso | `Collection` | Sin ruta |
| `getByDocumento` | `string $documento` | `first()` | `?Empleado` | Ningún llamador |
| `cambiarDisponibilidad` | `int $id`, `bool $disponibilidad` | Busca; si no existe, `false`; asigna y `save()` | `bool` | Sin ruta |

## `ServicioRepository`

```php
class ServicioRepository extends BaseRepository implements ServicioInterface
{
    /**
     * Inyecta el modelo Servicio al BaseRepository.
     */
    public function __construct(Servicio $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener todos los servicios activos.
     */
    public function getActivos(): Collection
    {
        return $this->model->where('activo', true)->get();
    }

    /**
     * Buscar un servicio por nombre.
     */
    public function getByNombre(string $nombre): ?Servicio
    {
        return $this->model->where('nombre', $nombre)->first();
    }

    /**
     * Cambiar el estado (activo/inactivo) de un servicio.
     */
    public function cambiarEstado(int $id, bool $activo): bool
    {
        $servicio = $this->model->find($id);

        if (! $servicio) {
            return false;
        }

        $servicio->activo = $activo;

        return $servicio->save();
    }
}
```

Fuente: `backend/app/Repositories/ServicioRepository.php` L9–50.

| Método | Parámetros | Qué hace | Devuelve | Uso real |
| --- | --- | --- | --- | --- |
| `getActivos` | — | `where('activo', true)` | `Collection` | Sin ruta |
| `getByNombre` | `string $nombre` | `first()` | `?Servicio` | Ningún llamador |
| `cambiarEstado` | `int $id`, `bool $activo` | Busca; `false` si no existe; asigna `activo` y guarda | `bool` | Sin ruta |

## `ProveedorRepository`, `ProductoRepository`, `MovimientoInventarioRepository`

```php
class ProveedorRepository extends BaseRepository implements ProveedorInterface
{
    public function __construct(Proveedor $proveedor)
    {
        parent::__construct($proveedor);
    }

    public function getByContacto(string $contacto)
    {
        $proveedores = $this->model->where('contacto', $contacto)
                                    ->get();

        if ($proveedores->isEmpty()) {
            return null;
        }

        return $proveedores;
    }

    public function getByEmail(string $email)
    {
        $proveedores = $this->model->where('email', $email)
                                    ->get();

        if ($proveedores->isEmpty()) {
            return null;
        }

        return $proveedores;
    }

    public function getByRegistro_tributario(string $registro_tributario)
    {
        $proveedor = $this->model->where('registro_tributario', $registro_tributario)
                                  ->first();

        if (!$proveedor) {
            return null;
        }

        return $proveedor;
    }
}
```

Fuente: `backend/app/Repositories/ProveedorRepository.php` L8–50.

```php
class ProductoRepository extends BaseRepository implements ProductoInterface
{
    public function __construct(Producto $producto)
    {
        parent::__construct($producto);
    }

    public function getByNombre(string $nombre)
    {
        $productos = $this->model->where('nombre', $nombre)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }

    public function getByFecha_registro(string $fecha_registro)
    {
        $productos = $this->model->where('fecha_registro', $fecha_registro)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }

    public function getByProveedor(int $proveedor_id)
    {
        $productos = $this->model->where('proveedor_id', $proveedor_id)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }
}
```

Fuente: `backend/app/Repositories/ProductoRepository.php` L8–50.

```php
class MovimientoInventarioRepository extends BaseRepository implements MovimientoInventarioInterface
{
    public function __construct(MovimientoInventario $movimientoInventario)
    {
        parent::__construct($movimientoInventario);
    }

    public function getByFecha_Hora(string $fecha_hora)
    {
        $movimientos = $this->model->where('fecha_hora', $fecha_hora)
                                    ->get();

        if ($movimientos->isEmpty()) {
            return null;
        }

        return $movimientos;
    }

    public function getByProducto(int $producto_id)
    {
        $movimientos = $this->model->where('producto_id', $producto_id)
                                    ->get();

        if ($movimientos->isEmpty()) {
            return null;
        }

        return $movimientos;
    }
}
```

Fuente: `backend/app/Repositories/MovimientoInventarioRepository.php` L8–38.

| Repositorio | Método | Patrón | Ruta que lo usa |
| --- | --- | --- | --- |
| `ProveedorRepository` | `getByContacto(string $contacto)` | Lista con `null` | `GET /api/proveedores/contacto/{contacto}` |
| | `getByEmail(string $email)` | Lista con `null` (aunque devuelve una colección, el nombre sugiere uno) | `GET /api/proveedores/email/{email}` |
| | `getByRegistro_tributario(string $registro_tributario)` | Uno (`first()`); el `if` que devuelve `null` es redundante | `GET /api/proveedores/registro-tributario/{...}` |
| `ProductoRepository` | `getByNombre`, `getByFecha_registro`, `getByProveedor` | Lista con `null` | Búsquedas de `producto` |
| `MovimientoInventarioRepository` | `getByFecha_Hora(string $fecha_hora)` | Lista con `null`; igualdad exacta de fecha y hora | `GET /api/movimientoinventario/fecha-hora/{fecha_hora}` |
| | `getByProducto(int $producto_id)` | Lista con `null` | `GET /api/movimientoinventario/producto/{producto_id}` |

En estos tres repositorios el constructor nombra el parámetro como el modelo (`Proveedor $proveedor`) en lugar de `$model`; el comportamiento es el mismo. Cuando una búsqueda devuelve `null`, el controlador responde 200 con `data: null` (CAP-19).
