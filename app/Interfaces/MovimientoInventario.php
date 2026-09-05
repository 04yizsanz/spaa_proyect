<?php

namespace App\Interfaces;

interface Movimiento_InventarioInterface extends BaseInterface

{
    public function getByFecha_Hora(string $fecha_hora);

    public function getByProducto(int $producto_id);

}
