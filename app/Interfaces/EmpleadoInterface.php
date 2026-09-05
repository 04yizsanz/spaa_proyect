<?php

namespace App\Repositories\Interfaces;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Collection;

interface EmpleadoInterface extends BaseInterface
{
    /**
     * Obtener todos los empleados disponibles.
     */
    public function getDisponibles(): Collection;

    /**
     * Buscar empleados por rol.
     */
    public function getByRol(string $rol): Collection;

    /**
     * Buscar un empleado por su documento.
     */
    public function getByDocumento(string $documento): ?Empleado;

    /**
     * Cambiar el estado de disponibilidad de un empleado.
     */
    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool;
}
