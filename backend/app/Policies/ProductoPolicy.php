<?php

namespace App\Policies;

class ProductoPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'inventario.ver';

    protected string $permisoGestionar = 'inventario.gestionar';
}
