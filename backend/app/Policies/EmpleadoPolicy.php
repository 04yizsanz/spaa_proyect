<?php

namespace App\Policies;

/**
 * Consultar el personal requiere 'empleados.ver'. Los campos de contacto y el
 * salario se ocultan según 'empleados.ver_contacto' y 'empleados.ver_salario'
 * en los controladores.
 */
class EmpleadoPolicy extends PermisoPolicy
{
    protected string $permisoVer = 'empleados.ver';

    protected string $permisoGestionar = 'empleados.gestionar';
}
