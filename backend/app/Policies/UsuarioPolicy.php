<?php

namespace App\Policies;

class UsuarioPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'sistema.administrar';

    protected string $permisoGestionar = 'sistema.administrar';
}
