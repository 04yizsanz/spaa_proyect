# Referencia: policies con reglas por registro

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Ninguna autorización se probó.

Este documento completa la sección 12 de [`backend/capas.md`](../capas.md#12-componentes-transversales-middleware-policies-y-proveedor-de-servicios), donde están `PermisoPolicy`, sus hijas, `CitaPolicy` y `PagoPolicy`. La matriz completa de permisos por rol se documentará en `seguridad/matriz-de-permisos.md`.

## Cómo responde una policy

| Valor devuelto | Efecto cuando se usa `Gate::authorize` o el middleware `can` |
| --- | --- |
| `true` o `Response::allow()` | Continúa |
| `false` | 403 con el mensaje genérico de Laravel |
| `Response::deny('mensaje')` | 403 con ese mensaje |
| `Response::denyAsNotFound('mensaje')` | 404: oculta que el registro existe |
| `Response::denyWithStatus(409, 'mensaje')` | 409 (solo en `PagoPolicy::update`) |

Laravel encuentra estas clases por **convención de nombre**: `ClientePolicy` para `App\Models\Cliente`. No se registran en ningún proveedor.

---

## `ClientePolicy`

```php
class ClientePolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('clientes.ver');
    }

    public function view(Usuario $usuario, Cliente $cliente): Response
    {
        return $this->puedeVer($usuario, $cliente)
            ? Response::allow()
            : Response::denyAsNotFound('Cliente no encontrado');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('clientes.gestionar');
    }

    public function update(Usuario $usuario, Cliente $cliente): Response
    {
        return $this->gestionar($usuario, $cliente);
    }

    public function delete(Usuario $usuario, Cliente $cliente): Response
    {
        return $this->gestionar($usuario, $cliente);
    }

    private function gestionar(Usuario $usuario, Cliente $cliente): Response
    {
        if ($usuario->tienePermiso('clientes.gestionar')) {
            return Response::allow();
        }

        return $this->puedeVer($usuario, $cliente)
            ? Response::deny('No tienes permiso para modificar clientes.')
            : Response::denyAsNotFound('Cliente no encontrado');
    }

    private function puedeVer(Usuario $usuario, Cliente $cliente): bool
    {
        if ($usuario->tienePermiso('clientes.ver')) {
            return true;
        }

        return $usuario->tienePermiso('clientes.propio')
            && (int) $cliente->usuario_id === (int) $usuario->usuario_id;
    }
}
```

Fuente: `backend/app/Policies/ClientePolicy.php` L17–66.

| Método | Parámetros | Regla | Resultado si deniega | Quién lo invoca |
| --- | --- | --- | --- | --- |
| `viewAny` | `Usuario $usuario` | `clientes.ver` | 403 | Ruta `can:viewAny` (`api.php` L112; `web.php` L57) |
| `view` | `$usuario`, `Cliente $cliente` | `puedeVer` | 404 “Cliente no encontrado” | `ClienteController::show` |
| `create` | `$usuario` | `clientes.gestionar` | 403 | Ruta `can:create` (`api.php` L113) |
| `update`, `delete` | `$usuario`, `$cliente` | Delegan en `gestionar` | Ver `gestionar` | `ClienteController::update` y `destroy` |
| `gestionar` (privado) | `$usuario`, `$cliente` | `clientes.gestionar` | 403 “No tienes permiso para modificar clientes.” si puede ver la ficha; 404 si no | `update`, `delete` |
| `puedeVer` (privado) | `$usuario`, `$cliente` | `clientes.ver`; o `clientes.propio` y el `usuario_id` de la ficha es el del usuario | — | `view`, `gestionar` |

**Por qué existe `gestionar`.** Evita repetir la misma lógica en `update` y `delete`. El nombre resume la acción común: modificar la ficha. Con los roles actuales, solo Recepcionista y Administrador gestionan clientes; un Cliente puede ver su ficha pero no modificarla por la API.

## `FacturaPolicy`

```php
class FacturaPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.ver_todas') || $usuario->tienePermiso('facturas.propias');
    }

    public function view(Usuario $usuario, Factura $factura): Response
    {
        return $this->puedeVer($usuario, $factura)
            ? Response::allow()
            : Response::denyAsNotFound('Factura no encontrada');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.gestionar');
    }

    public function update(Usuario $usuario, Factura $factura): Response
    {
        if ($usuario->tienePermiso('facturas.gestionar')) {
            return Response::allow();
        }

        return $this->puedeVer($usuario, $factura)
            ? Response::deny('No tienes permiso para modificar facturas.')
            : Response::denyAsNotFound('Factura no encontrada');
    }

    public function delete(Usuario $usuario, Factura $factura): Response
    {
        return $this->puedeVer($usuario, $factura)
            ? Response::deny('Los registros financieros no se eliminan.')
            : Response::denyAsNotFound('Factura no encontrada');
    }

    public function puedeVer(Usuario $usuario, Factura $factura): bool
    {
        if ($usuario->tienePermiso('facturas.ver_todas')) {
            return true;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        return $usuario->tienePermiso('facturas.propias')
            && $clienteId !== null
            && (int) $factura->cliente_id === (int) $clienteId;
    }
}
```

Fuente: `backend/app/Policies/FacturaPolicy.php` L17–66.

| Método | Regla | Resultado si deniega | Quién lo invoca |
| --- | --- | --- | --- |
| `viewAny` | `facturas.ver_todas` o `facturas.propias` | 403 | Ruta (`api.php` L80) |
| `view` | `puedeVer` | 404 “Factura no encontrada” | `FacturaController::show` |
| `create` | `facturas.gestionar` | 403 | Ruta (`api.php` L81) |
| `update` | `facturas.gestionar` | 403 “No tienes permiso para modificar facturas.” o 404 | `FacturaController::update` |
| `delete` | **Nunca permite** | 403 “Los registros financieros no se eliminan.” o 404 | `FacturaController::destroy` |
| `puedeVer` (**público**) | `facturas.ver_todas`; o `facturas.propias`, ficha de cliente y `cliente_id` coincidente | — | `view`, `update`, `delete` y **`FacturaServicioPolicy::puedeVer`** |

`puedeVer` es público (a diferencia del de las demás policies) porque `FacturaServicioPolicy` lo reutiliza creando un objeto `new FacturaPolicy`.

## `FacturaServicioPolicy`

```php
class FacturaServicioPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.ver_todas') || $usuario->tienePermiso('facturas.propias');
    }

    public function view(Usuario $usuario, FacturaServicio $linea): Response
    {
        return $this->puedeVer($usuario, $linea)
            ? Response::allow()
            : Response::denyAsNotFound('Registro no encontrado');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.gestionar');
    }

    public function update(Usuario $usuario, FacturaServicio $linea): Response
    {
        if ($usuario->tienePermiso('facturas.gestionar')) {
            return Response::allow();
        }

        return $this->puedeVer($usuario, $linea)
            ? Response::deny('No tienes permiso para modificar facturas.')
            : Response::denyAsNotFound('Registro no encontrado');
    }

    public function delete(Usuario $usuario, FacturaServicio $linea): Response
    {
        return $this->puedeVer($usuario, $linea)
            ? Response::deny('Los registros financieros no se eliminan.')
            : Response::denyAsNotFound('Registro no encontrado');
    }

    private function puedeVer(Usuario $usuario, FacturaServicio $linea): bool
    {
        $factura = Factura::find($linea->factura_id);

        return $factura !== null && (new FacturaPolicy)->puedeVer($usuario, $factura);
    }
}
```

Fuente: `backend/app/Policies/FacturaServicioPolicy.php` L14–57.

| Método | Regla | Resultado si deniega | Quién lo invoca |
| --- | --- | --- | --- |
| `viewAny` | Igual que facturas | 403 | Ruta (`api.php` L91) |
| `view` | `puedeVer` | 404 “Registro no encontrado” | `FacturaServicioController::show` |
| `create` | `facturas.gestionar` | 403 | Ruta (`api.php` L93) |
| `update` | `facturas.gestionar` | 403 o 404 | `FacturaServicioController::update` |
| `delete` | **Nunca permite** | 403 o 404 | `FacturaServicioController::destroy` |
| `puedeVer` (privado, L51–56) | Busca la factura de la línea con `Factura::find($linea->factura_id)`; si existe, aplica `FacturaPolicy::puedeVer` | — | `view`, `update`, `delete` |

**Variable `$factura` (L53).** La factura a la que pertenece la línea, o `null`. Si fuera `null`, la línea no es visible. Esta consulta usa el modelo directamente.

No existen permisos propios del detalle: las líneas siguen exactamente las reglas de su factura.
