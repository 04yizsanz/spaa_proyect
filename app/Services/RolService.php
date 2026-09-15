<?php

namespace App\Services;

use App\Interfaces\RolInterface;

class RolService
{
    public function __construct(
        private RolInterface $rolRepository
    ) {
    }

    public function list()
    {
        return $this->rolRepository->getAll();
    }

    public function store(array $datos)
    {
        return $this->rolRepository->create($datos);
    }

    public function show(int $id)
    {
        return $this->rolRepository->getById($id);
    }

    public function update(int $id, array $datos)
    {
        return $this->rolRepository->update($datos, $id);
    }

    public function destroy(int $id)
    {
        return $this->rolRepository->updateEstado($id, false);
    }

    public function activos()
    {
        return $this->rolRepository->findActivos();
    }

    public function buscarPorNombre(string $nombre)
    {
        return $this->rolRepository->getByName($nombre);
    }

    public function actualizarEstado(int $id, bool $estado)
    {
        return $this->rolRepository->updateEstado($id, $estado);
    }
}