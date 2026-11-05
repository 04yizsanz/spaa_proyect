<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Reglas de acceso a citas (las mismas para la web y la API):
 *  - 'citas.ver_todas'  → consulta toda la agenda (solo lectura).
 *  - 'citas.gestionar'  → crea y edita cualquier cita, la reasigna y cambia su estado.
 *  - 'citas.eliminar'   → elimina citas.
 *  - 'citas.propias'    → consulta y agenda sus citas; solo edita las suyas en
 *                         estado 'pendiente' y solo puede cancelarlas.
 *
 * Una cita que el usuario no puede ver responde 404 (no revela que existe).
 */
class CitaPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('citas.ver_todas') || $usuario->tienePermiso('citas.propias');
    }

    public function view(Usuario $usuario, Cita $cita): Response
    {
        return $this->puedeVer($usuario, $cita)
            ? Response::allow()
            : Response::denyAsNotFound('Cita no encontrada');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('citas.gestionar') || $usuario->tienePermiso('citas.propias');
    }

    public function update(Usuario $usuario, Cita $cita): Response
    {
        if ($usuario->tienePermiso('citas.gestionar')) {
            return Response::allow();
        }

        if (! $this->esPropia($usuario, $cita)) {
            return $this->puedeVer($usuario, $cita)
                ? Response::deny('No tienes permiso para modificar citas.')
                : Response::denyAsNotFound('Cita no encontrada');
        }

        return $cita->estado === 'pendiente'
            ? Response::allow()
            : Response::deny('Solo puedes modificar citas pendientes.');
    }

    public function delete(Usuario $usuario, Cita $cita): Response
    {
        if ($usuario->tienePermiso('citas.eliminar')) {
            return Response::allow();
        }

        return $this->puedeVer($usuario, $cita)
            ? Response::deny('No tienes permiso para eliminar citas.')
            : Response::denyAsNotFound('Cita no encontrada');
    }

    /**
     * Estado con el que se registra una cita nueva.
     */
    public function crearConEstado(Usuario $usuario, string $estado): Response
    {
        if ($usuario->tienePermiso('citas.gestionar') || $estado === 'pendiente') {
            return Response::allow();
        }

        return Response::deny('Las citas nuevas se registran como pendientes.');
    }

    /**
     * Cambio de estado de una cita existente.
     */
    public function cambiarEstado(Usuario $usuario, Cita $cita, string $estado): Response
    {
        if ($usuario->tienePermiso('citas.gestionar')) {
            return Response::allow();
        }

        if ($this->esPropia($usuario, $cita) && $estado === 'cancelada') {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para asignar ese estado a la cita.');
    }

    /**
     * Cambio del cliente propietario de la cita.
     */
    public function reasignar(Usuario $usuario, Cita $cita): Response
    {
        return $usuario->tienePermiso('citas.gestionar')
            ? Response::allow()
            : Response::deny('No puedes reasignar la cita a otro cliente.');
    }

    private function puedeVer(Usuario $usuario, Cita $cita): bool
    {
        return $usuario->tienePermiso('citas.ver_todas') || $this->esPropia($usuario, $cita);
    }

    private function esPropia(Usuario $usuario, Cita $cita): bool
    {
        if (! $usuario->tienePermiso('citas.propias')) {
            return false;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        return $clienteId !== null && (int) $cita->cliente_id === (int) $clienteId;
    }
}
