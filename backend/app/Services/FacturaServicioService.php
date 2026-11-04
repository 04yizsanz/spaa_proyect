<?php

namespace App\Services;

use App\Interfaces\FacturaServicioInterface;

class FacturaServicioService
{
    public function __construct(
        private FacturaServicioInterface $facturaServicioRepository
    ) {}

    public function show(array $id)
    {
        return $this->facturaServicioRepository->getById($id);
    }

    public function store(array $data)
    {
        return $this->facturaServicioRepository->create($data);
    }

    public function update(array $id, array $data)
    {
        return $this->facturaServicioRepository->update($data, $id);
    }

    public function destroy(array $id)
    {
        return $this->facturaServicioRepository->delete($id);
    }

    public function getByFactura(int $factura_id)
    {
        return $this->facturaServicioRepository->getByFactura($factura_id);
    }

    public function getByServicio(int $servicio_id)
    {
        return $this->facturaServicioRepository->getByServicio($servicio_id);
    }
}