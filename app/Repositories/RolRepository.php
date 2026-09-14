<?php

namespace App\Repositories;

use App\Interfaces\RolInterface;
use App\Models\Rol;

class RolRepository extends BaseRepository implements RolInterface
{
    public function __construct(Rol $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener todos los roles activos.
     */
    public function findActivos()
    {
        return $this->model
            ->where('estado', true)
            ->get();
    }

    /**
     * Buscar un rol por nombre.
     */
    public function getByName(string $nombre)
    {
        return $this->model
            ->where('nombre', $nombre)
            ->first();
    }

    /**
     * Cambiar el estado de un rol.
     */
    public function updateEstado(int $id, bool $estado)
    {
        $rol = $this->model->find($id);

        if (!$rol) {
            return null;
        }

        $rol->estado = $estado;
        $rol->save();

        return $rol->fresh();
    }
}