<?php

namespace App\Repositories;

use App\Interfaces\UsuarioInterface;
use App\Models\Usuario;

class UsuarioRepository extends BaseRepository implements UsuarioInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    public function getByRol(int $idRol)
    {
        return $this->model
            ->where('rol_id', $idRol)
            ->get();
    }

    public function getByEstatus(bool $status)
    {
        return $this->model
            ->where('estado', $status)
            ->get();
    }

    public function getByName(string $name)
    {
        return $this->model
            ->where('nombre', $name)
            ->get();
    }
}

