<?php

namespace App\Services;

use App\Interfaces\RolInterface;
use InvalidArgumentException;

class RolService
{
    public function __construct(
        protected RolInterface $rolRepository
    ) {}

    public function getAll()
    {
        return $this->rolRepository->all();
    }

    public function getById(int $id)
    {
        return $this->rolRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->rolRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $rol = $this->rolRepository->find($id);

        if (! $rol) {
            return null;
        }

        return $this->rolRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $rol = $this->rolRepository->find($id);

        if (! $rol) {
            return null;
        }

        return $this->rolRepository->delete($id);
    }

    public function getActivos()
    {
        return $this->rolRepository->findActivos();
    }

    public function getByNombre(string $nombre)
    {
        $rol = $this->rolRepository->getByName($nombre);

        if (! $rol) {
            throw new InvalidArgumentException("No existe un rol con el nombre '{$nombre}'.");
        }

        return $rol;
    }

    public function cambiarEstado(int $id, bool $estado)
    {
        return $this->rolRepository->updateEstado($id, $estado);
    }
}