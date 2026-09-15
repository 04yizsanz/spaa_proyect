<?php

namespace App\Repositories;

use App\Models\Empleado;
use App\Interfaces\EmpleadoInterface;
use Illuminate\Database\Eloquent\Collection;

class EmpleadoRepository extends BaseRepository implements EmpleadoInterface
{
    /**
     * Inyecta el modelo Empleado al BaseRepository.
     */
    public function __construct(Empleado $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener todos los empleados disponibles.
     */
    public function getDisponibles(): Collection
    {
        return $this->model->where('disponibilidad', true)->get();
    }

    /**
     * Buscar empleados por rol.
     */
    public function getByRol(string $rol): Collection
    {
        return $this->model->where('rol', $rol)->get();
    }

    /**
     * Buscar un empleado por su documento.
     */
    public function getByDocumento(string $documento): ?Empleado
    {
        return $this->model->where('documento', $documento)->first();
    }

    /**
     * Cambiar el estado de disponibilidad de un empleado.
     */
    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool
    {
        $empleado = $this->model->find($id);

        if (! $empleado) {
            return false;
        }

        $empleado->disponibilidad = $disponibilidad;

        return $empleado->save();
    }
}