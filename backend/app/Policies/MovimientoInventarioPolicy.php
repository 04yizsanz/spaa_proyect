<?php

namespace App\Policies;

class MovimientoInventarioPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'inventario.ver';

    protected string $permisoGestionar = 'inventario.gestionar';
}
