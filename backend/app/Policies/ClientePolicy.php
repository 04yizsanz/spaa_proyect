<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Reglas de acceso a clientes:
 *  - 'clientes.ver'        → directorio completo.
 *  - 'clientes.gestionar'  → crear, editar y eliminar.
 *  - 'clientes.propio'     → solo su propio perfil (lectura).
 *
 * Un perfil ajeno responde 404 (no revela que existe).
 */
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
