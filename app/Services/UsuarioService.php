<?php

namespace App\Services;

use App\Interfaces\UsuarioInterface;

class UsuarioService
{
    public function __construct(
        protected UsuarioInterface $usuarioRepository
    ) {}

    public function getAll()
    {
        return $this->usuarioRepository->all();
    }

    public function getById(int $id)
    {
        return $this->usuarioRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->usuarioRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $usuario = $this->usuarioRepository->find($id);

        if (! $usuario) {
            return null;
        }

        return $this->usuarioRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $usuario = $this->usuarioRepository->find($id);

        if (! $usuario) {
            return null;
        }

        return $this->usuarioRepository->delete($id);
    }

    public function getByRol(int $rolId)
    {
        return $this->usuarioRepository->getByRol($rolId);
    }

    public function getByEstatus(bool $status)
    {
        return $this->usuarioRepository->getByEstatus($status);
    }

    public function getByNombre(string $nombre)
    {
        return $this->usuarioRepository->getByName($nombre);
    }
}