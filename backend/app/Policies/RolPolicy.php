<?php

namespace App\Policies;

class RolPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'sistema.administrar';

    protected string $permisoGestionar = 'sistema.administrar';
}
