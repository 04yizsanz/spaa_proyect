<?php

namespace App\Services;

use App\Repositories\Interfaces\ServicioInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Servicio;

class ServicioService
{
    public function __construct(
        protected ServicioInterface $servicioRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->servicioRepository->getAll();
    }

    public function getById(int $id): ?Servicio
    {
        return $this->servicioRepository->getById($id);
    }

    public function create(array $data): Servicio
    {
        return $this->servicioRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->servicioRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->servicioRepository->delete($id);
    }

    public function getActivos(): Collection
    {
        return $this->servicioRepository->getActivos();
    }

    public function getByNombre(string $nombre): ?Servicio
    {
        return $this->servicioRepository->getByNombre($nombre);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        return $this->servicioRepository->cambiarEstado($id, $activo);
    }
}