<?php

namespace App\Services;

use App\Interfaces\ProveedorInterface;

class ProveedorService
{
    public function __construct(
        private ProveedorInterface $proveedorRepository
    ) {}

    public function list()
    {
        return $this->proveedorRepository->getAll();
    }

    public function show(int $id)
    {
        return $this->proveedorRepository->getById($id);
    }

    public function store(array $data)
    {
        return $this->proveedorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->proveedorRepository->update($data, $id);
    }

    public function destroy(int $id)
    {
        return $this->proveedorRepository->delete($id);
    }
}