<?php

namespace App\Services;

use App\Repositories\Interfaces\EmpleadoInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Empleado;

class EmpleadoService
{
    public function __construct(
        protected EmpleadoInterface $empleadoRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->empleadoRepository->getAll();
    }

    public function getById(int $id): ?Empleado
    {
        return $this->empleadoRepository->getById($id);
    }

    public function create(array $data): Empleado
    {
        return $this->empleadoRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->empleadoRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->empleadoRepository->delete($id);
    }

    public function getDisponibles(): Collection
    {
        return $this->empleadoRepository->getDisponibles();
    }

    public function getByRol(string $rol): Collection
    {
        return $this->empleadoRepository->getByRol($rol);
    }

    public function getByDocumento(string $documento): ?Empleado
    {
        return $this->empleadoRepository->getByDocumento($documento);
    }

    public function cambiarDisponibilidad(int $id, bool $disponibilidad): bool
    {
        return $this->empleadoRepository->cambiarDisponibilidad($id, $disponibilidad);
    }
}