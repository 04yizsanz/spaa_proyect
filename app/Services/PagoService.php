<?php

namespace App\Services;

use App\Interfaces\PagoInterface;
use Carbon\Carbon;

class PagoService
{
    public function __construct(
        private PagoInterface $pagoRepository
    ) {}

    public function list()
    {
        return $this->pagoRepository->all();
    }

    public function show(int $id)
    {
        return $this->pagoRepository->find($id);
    }

    public function store(array $data)
    {
        return $this->pagoRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->pagoRepository->update($id, $data);
    }

    public function destroy(int $id)
    {
        return $this->pagoRepository->delete($id);
    }

    public function getByCita(int $codigo_cita)
    {
        return $this->pagoRepository->getByCita($codigo_cita);
    }

    public function getByEstado(string $estado)
    {
        return $this->pagoRepository->getByEstado($estado);
    }

    public function getByFecha(Carbon $fechaPago)
    {
        return $this->pagoRepository->getByFecha($fechaPago);
    }
}