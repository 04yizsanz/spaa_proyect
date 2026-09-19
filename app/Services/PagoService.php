<?php

namespace App\Services;

use App\Interfaces\PagoInterface;
use DateTime;

class PagoService
{
    public function __construct(
        private PagoInterface $pagoRepository
    ) {}

    public function list()
    {
        return $this->pagoRepository->all();
    }

    public function show(int $pagoId)
    {
        return $this->pagoRepository->find($pagoId);
    }

    public function store(array $data)
    {
        return $this->pagoRepository->create($data);
    }

    public function update(int $pagoId, array $data)
    {
        return $this->pagoRepository->update($data, $pagoId);
    }

    public function destroy(int $pagoId)
    {
        return $this->pagoRepository->delete($pagoId);
    }

    public function getByCita(int $codigo_cita)
    {
        return $this->pagoRepository->getByCita($codigo_cita);
    }

    public function getByEstado(string $estado)
    {
        return $this->pagoRepository->getByEstado($estado);
    }

    public function getByFechaHora(DateTime $fechaHora)
    {
        return $this->pagoRepository->getByFechaHora($fechaHora);
    }
}