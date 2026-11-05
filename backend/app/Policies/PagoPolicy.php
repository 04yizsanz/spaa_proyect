<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\Pago;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Reglas de acceso a pagos:
 *  - 'pagos.ver_todos'  → consulta todos los pagos.
 *  - 'pagos.propios'    → consulta los pagos de sus propias citas.
 *  - 'pagos.registrar'  → registra pagos y edita los que siguen 'pendiente'.
 *  - 'pagos.aprobar'    → aprueba o rechaza pagos.
 *
 * Un pago 'aprobado' o 'rechazado' es definitivo: no se modifica (409).
 * Ningún pago se elimina, para ningún rol (trazabilidad financiera).
 */
class PagoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('pagos.ver_todos') || $usuario->tienePermiso('pagos.propios');
    }

    public function view(Usuario $usuario, Pago $pago): Response
    {
        return $this->puedeVer($usuario, $pago)
            ? Response::allow()
            : Response::denyAsNotFound('Pago no encontrado');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('pagos.registrar');
    }

    public function update(Usuario $usuario, Pago $pago): Response
    {
        if (! $usuario->tienePermiso('pagos.registrar')) {
            return $this->puedeVer($usuario, $pago)
                ? Response::deny('No tienes permiso para modificar pagos.')
                : Response::denyAsNotFound('Pago no encontrado');
        }

        if ($pago->estado !== 'pendiente') {
            return Response::denyWithStatus(409, 'Un pago aprobado o rechazado no puede modificarse.');
        }

        return Response::allow();
    }

    /**
     * Registrar un pago con estado distinto de 'pendiente' o cambiar su estado.
     */
    public function aprobar(Usuario $usuario, ?Pago $pago = null): Response
    {
        return $usuario->tienePermiso('pagos.aprobar')
            ? Response::allow()
            : Response::deny('No tienes permiso para aprobar o rechazar pagos.');
    }

    public function delete(Usuario $usuario, Pago $pago): Response
    {
        return $this->puedeVer($usuario, $pago)
            ? Response::deny('Los registros financieros no se eliminan.')
            : Response::denyAsNotFound('Pago no encontrado');
    }

    private function puedeVer(Usuario $usuario, Pago $pago): bool
    {
        if ($usuario->tienePermiso('pagos.ver_todos')) {
            return true;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if (! $usuario->tienePermiso('pagos.propios') || $clienteId === null) {
            return false;
        }

        return Cita::withTrashed()
            ->where('codigo_cita', $pago->codigo_cita)
            ->where('cliente_id', $clienteId)
            ->exists();
    }
}
