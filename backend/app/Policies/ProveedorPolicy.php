<?php

namespace App\Policies;

class ProveedorPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'inventario.ver';

    protected string $permisoGestionar = 'inventario.gestionar';
}
