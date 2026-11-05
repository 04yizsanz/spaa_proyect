# Referencia: interfaces

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones.

Este documento completa la [capa 5 de `backend/capas.md`](../capas.md#9-capa-5--interfaz), donde se explican `BaseInterface`, `CitaInterface` y el enlace con los repositorios en `AppServiceProvider`.

## Cómo leer una interfaz

Una interfaz **no contiene código ejecutable**: solo declara firmas (nombre, parámetros y, a veces, tipo de retorno). No recibe, modifica ni devuelve datos por sí misma. Su efecto es doble:

1. Obliga al repositorio que la implementa a tener esos métodos; si faltara uno, PHP no podría cargar la clase.
2. Permite que el servicio declare “necesito un `ClienteInterface`” sin conocer `ClienteRepository`. `AppServiceProvider::register` decide qué clase se entrega.

Todas las interfaces de entidad **extienden `BaseInterface`**, así que además de sus métodos propios incluyen `create`, `getAll`, `getById`, `update` y `delete`.

La columna “Uso real” indica si el método participa en una operación con ruta.

---

## `ClienteInterface`

```php
interface ClienteInterface extends BaseInterface
{
    public function getByName (string $name);
    public function getByLastname (string $lastname);
    public function getByDocument (string $document);

    /**
     * Clientes que el usuario puede consultar (scope Cliente::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca un cliente por id solo entre los que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Cliente;
}
```

Fuente: `backend/app/Interfaces/ClienteInterface.php` L9–24.

| Método | Parámetros | Retorno declarado | Uso real |
| --- | --- | --- | --- |
| `getByName` | `string $name` | — | Sin ruta (`ClienteController::porNombre`) |
| `getByLastname` | `string $lastname` | — | Sin ruta |
| `getByDocument` | `string $document` | — | Sin ruta |
| `visiblesPara` | `Usuario $usuario` | `Collection` | `GET /api/clientes` |
| `buscarVisible` | `int $id`, `Usuario $usuario` | `?Cliente` | `GET`, `PUT`/`PATCH` y `DELETE /api/clientes/{cliente}` |

Los nombres de las búsquedas están en inglés, mientras que los métodos de servicio que las llaman están en español (`getByNombre` → `getByName`).

## `EmpleadoInterface`

```php
interface EmpleadoInterface extends BaseInterface
{
    /**
     * Obtener todos los empleados disponibles.
     */
    public function getDisponibles(): Collection;

    /**
     * Buscar empleados por rol.
     */
    public function getByRol(string $rol): Collection;

    /**
     * Buscar un empleado por su documento.
     */
    public function getByDocumento(string $documento): ?Empleado;

    /**
     * Cambiar el estado de disponibilidad de un empleado.
     */
    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool;
}
```

Fuente: `backend/app/Interfaces/EmpleadoInterface.php` L10–31.

| Método | Parámetros | Retorno | Uso real |
| --- | --- | --- | --- |
| `getDisponibles` | — | `Collection` | Sin ruta |
| `getByRol` | `string $rol` (cargo: `estilista`, `Estetisista`, …) | `Collection` | Sin ruta |
| `getByDocumento` | `string $documento` | `?Empleado` | Ningún llamador |
| `cambiarDisponibilidad` | `int $id`, `bool $disponibilidad` | `bool` | Sin ruta |

## `ServicioInterface`

```php
interface ServicioInterface extends BaseInterface
{
    /**
     * Obtener todos los servicios activos.
     */
    public function getActivos(): Collection;

    /**
     * Buscar un servicio por nombre.
     */
    public function getByNombre(string $nombre): ?Servicio;

    /**
     * Cambiar el estado (activo/inactivo) de un servicio.
     */
    public function cambiarEstado(int $id, bool $activo): bool;
}
```

Fuente: `backend/app/Interfaces/ServicioInterface.php` L8–24.

| Método | Parámetros | Retorno | Uso real |
| --- | --- | --- | --- |
| `getActivos` | — | `Collection` | Sin ruta |
| `getByNombre` | `string $nombre` | `?Servicio` | Ningún llamador |
| `cambiarEstado` | `int $id`, `bool $activo` | `bool` | Sin ruta |

## `FacturaInterface`

```php
interface FacturaInterface extends BaseInterface

{
    public function getByFechaHora(DateTime $fechaHora);

    public function getBySubtotal(float $subtotal);

    public function getByImpuestos(float $impuestos);

    public function getByTotal(float $total);

    public function getByCliente(int $cliente_id);

    /**
     * Facturas que el usuario puede consultar (scope Factura::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca una factura por id solo entre las que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Factura;

}
```

Fuente: `backend/app/Interfaces/FacturaInterface.php` L9–32.

| Método | Parámetros | Retorno | Uso real |
| --- | --- | --- | --- |
| `getByFechaHora` | `DateTime $fechaHora` (objeto de fecha de PHP) | — | Ningún llamador |
| `getBySubtotal`, `getByImpuestos`, `getByTotal` | `float` | — | Ningún llamador |
| `getByCliente` | `int $cliente_id` | — | Ningún llamador (solo `FacturaService::getByCliente`, que nadie llama) |
| `visiblesPara` | `Usuario $usuario` | `Collection` | `GET /api/facturas` |
| `buscarVisible` | `int $id`, `Usuario $usuario` | `?Factura` | Rutas de una factura |

## `PagoInterface`

```php
interface PagoInterface extends BaseInterface

{
    public function getByMonto(float $monto);

    public function getByFechaHora(DateTime $fecha_hora);

    public function getByCita(int $codigo_cita);

    public function getByEstado(string $estado);

    /**
     * Pagos que el usuario puede consultar (scope Pago::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca un pago por id solo entre los que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Pago;

}
```

Fuente: `backend/app/Interfaces/PagoInterface.php` L9–30.

| Método | Parámetros | Retorno | Uso real |
| --- | --- | --- | --- |
| `getByMonto` | `float $monto` | — | Ningún llamador |
| `getByFechaHora` | `DateTime $fecha_hora` | — | Ningún llamador con ruta |
| `getByCita` | `int $codigo_cita` | — | Ningún llamador con ruta |
| `getByEstado` | `string $estado` | — | Ningún llamador con ruta |
| `visiblesPara`, `buscarVisible` | Como en `FacturaInterface` | `Collection`, `?Pago` | Rutas de pagos |

## `FacturaServicioInterface`

```php
interface FacturaServicioInterface extends BaseInterface

{
    public function getByFactura(int $factura_id);

    public function getByServicio(int $servicio_id);

    public function getByCantidad(int $cantidad);

    public function getByPrecioUnitario(float $precio_unitario);

    /**
     * Líneas de factura que el usuario puede consultar (scope FacturaServicio::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca una línea (PK compuesta) solo entre las que el usuario puede consultar.
     */
    public function buscarVisible(int $facturaId, int $servicioId, Usuario $usuario): ?FacturaServicio;

    /**
     * Indica si el servicio ya forma parte de la factura.
     */
    public function existeLinea(int $facturaId, int $servicioId): bool;

}
```

Fuente: `backend/app/Interfaces/FacturaServicioInterface.php` L8–34.

| Método | Parámetros | Retorno | Uso real |
| --- | --- | --- | --- |
| `getByFactura`, `getByServicio`, `getByCantidad`, `getByPrecioUnitario` | `int` o `float` | — | Ningún llamador con ruta |
| `visiblesPara` | `Usuario $usuario` | `Collection` | `GET /api/factura-servicios` |
| `buscarVisible` | `int $facturaId`, `int $servicioId`, `Usuario $usuario` | `?FacturaServicio` | Rutas de una línea |
| `existeLinea` | `int $facturaId`, `int $servicioId` | `bool` | `FacturaServicioService::store` |

`BaseInterface` declara `getById(int $id)`, pero `FacturaServicioRepository` lo implementa como `getById(mixed $id)`. PHP permite que una implementación acepte un tipo más amplio que el declarado, por eso la clase carga sin error.

## `RolInterface`

```php
interface RolInterface extends BaseInterface
{
    public function findActivos();

    public function getByName(string $nombre);

    public function updateEstado(int $id, bool $estado);
}
```

Fuente: `backend/app/Interfaces/RolInterface.php` L5–12.

| Método | Parámetros | Uso real |
| --- | --- | --- |
| `findActivos` | — | Sin ruta |
| `getByName` | `string $nombre` | Sin ruta |
| `updateEstado` | `int $id`, `bool $estado` | Sin ruta |

## `UsuarioInterface`

```php
interface UsuarioInterface extends BaseInterface
{
    public function getByRol(int $idRol);
    public function getByEstatus(bool $status);
    public function getByName(string $name);
    public function getByEmail(string $email);
}
```

Fuente: `backend/app/Interfaces/UsuarioInterface.php` L5–11.

| Método | Parámetros | Uso real |
| --- | --- | --- |
| `getByRol` | `int $idRol` | Sin ruta |
| `getByEstatus` | `bool $status` | Sin ruta |
| `getByName` | `string $name` | Sin ruta |
| `getByEmail` | `string $email` | Inicio de sesión web y API |

`UsuarioInterface` no declara `visiblesPara` ni `buscarVisible`: los usuarios solo los gestiona el Administrador, sin reglas por registro.

## `ProveedorInterface`, `ProductoInterface`, `MovimientoInventarioInterface`

```php
interface ProveedorInterface extends BaseInterface

{
    public function getByContacto(string $contacto);

    public function getByEmail(string $email);

    public function getByRegistro_tributario(string $registro_tributario);
    
}
```

Fuente: `backend/app/Interfaces/ProveedorInterface.php` L5–14.

```php
interface ProductoInterface extends BaseInterface

{
    public function getByNombre(string $nombre);

    public function getByFecha_registro(string $fecha_registro);

   public function getByProveedor(int $proveedor_id);
    
}
```

Fuente: `backend/app/Interfaces/ProductoInterface.php` L5–14.

```php
interface MovimientoInventarioInterface extends BaseInterface

{
    public function getByFecha_Hora(string $fecha_hora);

    public function getByProducto(int $producto_id);

}
```

Fuente: `backend/app/Interfaces/MovimientoInventarioInterface.php` L5–12.

| Interfaz | Métodos propios | Uso real |
| --- | --- | --- |
| `ProveedorInterface` | `getByContacto(string)`, `getByEmail(string)`, `getByRegistro_tributario(string)` | Las tres, por las búsquedas con ruta |
| `ProductoInterface` | `getByNombre(string)`, `getByFecha_registro(string)`, `getByProveedor(int)` | Las tres |
| `MovimientoInventarioInterface` | `getByFecha_Hora(string)`, `getByProducto(int)` | Las dos |

Estas tres interfaces usan nombres con guion bajo (`getByFecha_registro`), a diferencia de las demás. Ninguna declara tipos de retorno.
