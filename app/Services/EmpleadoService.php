<?php

namespace App\Services;

use App\Interfaces\EmpleadoInterface;
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

    public function update(int $id, array $data)
    {
    return $this->empleadoRepository->update($data, $id);
    }

    public function delete(int $id): bool
    {
    $registro = $this->empleadoRepository->delete($id);

    return $registro !== null;
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