<?php
namespace App\Interfaces;

use DateTime;

interface FacturaInterface extends BaseInterface

{
    public function getByFechaHora(dateTime $fecheHora);

    public function getBySubtotal(decimal $subtotal);

    public function getByImpuestos(decimal $impuestos);

    public function getByTotal(decimal $total);


}