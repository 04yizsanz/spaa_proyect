<?php

namespace App\Policies;

use App\Models\Usuario;

/**
 * Policy base para recursos cuyo acceso depende solo del permiso del rol
 * (sin reglas de propiedad). Las subclases indican qué permiso permite
 * consultar y cuál permite crear, modificar o eliminar.
 *
 * Denegación por defecto: si el usuario no tiene el permiso, se deniega.
 */
abstract class PermisoPolicy
{
    /** Permiso necesario para listar y consultar. */
    protected string $permisoVer;

    /** Permiso necesario para crear, modificar y eliminar. */
    protected string $permisoGestionar;

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso($this->permisoVer);
    }

    public function view(Usuario $usuario, mixed $modelo = null): bool
    {
        return $usuario->tienePermiso($this->permisoVer);
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso($this->permisoGestionar);
    }

    public function update(Usuario $usuario, mixed $modelo = null): bool
    {
        return $usuario->tienePermiso($this->permisoGestionar);
    }

    public function delete(Usuario $usuario, mixed $modelo = null): bool
    {
        return $usuario->tienePermiso($this->permisoGestionar);
    }
}
