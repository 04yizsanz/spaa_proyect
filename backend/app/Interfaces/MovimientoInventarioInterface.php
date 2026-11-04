<?php

namespace App\Interfaces;

interface MovimientoInventarioInterface extends BaseInterface

{
    public function getByFecha_Hora(string $fecha_hora);

    public function getByProducto(int $producto_id);

}
