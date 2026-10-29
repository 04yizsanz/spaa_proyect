<?php

namespace App\Repositories;

use App\Interfaces\UsuarioInterface;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository extends BaseRepository implements UsuarioInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    /**
     * Crea un usuario hasheando el password automáticamente.
     */
    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->model->create($data);
    }

    public function getByRol(int $idRol)
    {
        return $this->model->where('rol_id', $idRol)->get();
    }

    public function getByEstatus(bool $status)
    {
        return $this->model->where('estado', $status)->get();
    }

    public function getByName(string $name)
    {
        return $this->model->where('nombre', $name)->get();
    }

    public function getByEmail(string $email): ?Usuario
    {
        return $this->model->where('email', $email)->where('estado', true)->first();
    }
}
