<?php

namespace App\Services;

use App\Interfaces\FacturaInterface;

class FacturaService
{
    public function __construct(
        private FacturaInterface $facturaRepository
    ) {}

    public function list()
    {
        return $this->facturaRepository->all();
    }

    public function show(int $id)
    {
        return $this->facturaRepository->find($id);
    }

    public function store(array $data)
    {
        return $this->facturaRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->facturaRepository->update($id, $data);
    }

    public function destroy(int $id)
    {
        return $this->facturaRepository->delete($id);
    }

    public function getByCliente(int $cliente_id)
    {
        return $this->facturaRepository->getByCliente($cliente_id);
    }
}