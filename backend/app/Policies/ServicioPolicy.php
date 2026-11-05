<?php

namespace App\Policies;

class ServicioPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'servicios.ver';

    protected string $permisoGestionar = 'servicios.gestionar';
}
