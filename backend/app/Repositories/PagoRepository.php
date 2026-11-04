<?php

namespace App\Repositories;

use App\Interfaces\PagoInterface;
use App\Models\Pago;
use DateTime;

class PagoRepository extends BaseRepository implements PagoInterface
{
    public function __construct(Pago $model)
    {
        parent::__construct($model);
    }

    public function getByMonto(float $monto)
    {
        $pagos = $this->model->where('monto', $monto)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByFechaHora(DateTime $fecha_hora)
    {
        $pagos = $this->model->where('fecha_hora', $fecha_hora)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByCita(int $codigo_cita)
    {
        $pagos = $this->model->where('codigo_cita', $codigo_cita)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }

    public function getByEstado(string $estado)
    {
        $pagos = $this->model->where('estado', $estado)->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return $pagos;
    }
}