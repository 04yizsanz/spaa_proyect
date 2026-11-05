<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Reglas de acceso a facturas:
 *  - 'facturas.ver_todas'  → consulta todas las facturas.
 *  - 'facturas.propias'    → consulta sus propias facturas.
 *  - 'facturas.gestionar'  → emite facturas y edita su detalle e impuestos.
 *
 * Ninguna factura se elimina, para ningún rol (trazabilidad financiera).
 */
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
