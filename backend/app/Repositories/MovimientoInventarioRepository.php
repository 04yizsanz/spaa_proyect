<?php

namespace App\Repositories;

use App\Interfaces\MovimientoInventarioInterface;
use App\Models\MovimientoInventario;

class MovimientoInventarioRepository extends BaseRepository implements MovimientoInventarioInterface
{
    public function __construct(MovimientoInventario $movimientoInventario)
    {
        parent::__construct($movimientoInventario);
    }

    public function getByFecha_Hora(string $fecha_hora)
    {
        $movimientos = $this->model->where('fecha_hora', $fecha_hora)
                                    ->get();

        if ($movimientos->isEmpty()) {
            return null;
        }

        return $movimientos;
    }

    public function getByProducto(int $producto_id)
    {
        $movimientos = $this->model->where('producto_id', $producto_id)
                                    ->get();

        if ($movimientos->isEmpty()) {
            return null;
        }

        return $movimientos;
    }
}