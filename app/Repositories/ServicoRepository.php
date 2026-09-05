<?php

namespace App\Repositories;

use App\Models\Servicio;
use App\Repositories\Interfaces\ServicioInterface;
use Illuminate\Database\Eloquent\Collection;

class ServicioRepository extends BaseRepository implements ServicioInterface
{
    /**
     * Inyecta el modelo Servicio al BaseRepository.
     */
    public function __construct(Servicio $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener todos los servicios activos.
     */
    public function getActivos(): Collection
    {
        return $this->model->where('activo', true)->get();
    }

    /**
     * Buscar un servicio por nombre.
     */
    public function getByNombre(string $nombre): ?Servicio
    {
        return $this->model->where('nombre', $nombre)->first();
    }

    /**
     * Cambiar el estado (activo/inactivo) de un servicio.
     */
    public function cambiarEstado(int $id, bool $activo): bool
    {
        $servicio = $this->model->find($id);

        if (! $servicio) {
            return false;
        }

        $servicio->activo = $activo;

        return $servicio->save();
    }
}