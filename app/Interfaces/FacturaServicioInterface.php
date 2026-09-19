<?php
namespace App\Interfaces;

interface FacturaServicioInterface extends BaseInterface

{
    public function getByFactura(int $factura_id);

    public function getByServicio(int $servicio_id);

    public function getByCantidad(int $cantidad);

    public function getByPrecioUnitario(float $precio_unitario);


}

