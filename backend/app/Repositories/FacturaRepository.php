<?php

namespace App\Repositories;

use App\Interfaces\FacturaInterface;
use App\Models\Factura;
use DateTime;

class FacturaRepository extends BaseRepository implements FacturaInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
    }

    public function getByFechaHora(DateTime $fechaHora)
    {
        $facturas = $this->model->where('fecha_hora', $fechaHora)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getBySubtotal(float $subtotal)
    {
        $facturas = $this->model->where('subtotal', $subtotal)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByImpuestos(float $impuestos)
    {
        $facturas = $this->model->where('impuestos', $impuestos)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByTotal(float $total)
    {
        $facturas = $this->model->where('total', $total)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }

    public function getByCliente(int $cliente_id)
    {
        $facturas = $this->model->where('cliente_id', $cliente_id)->get();

        if ($facturas->isEmpty()) {
            return null;
        }

        return $facturas;
    }
}