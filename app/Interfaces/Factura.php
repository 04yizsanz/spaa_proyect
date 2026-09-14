<?php
namespace App\Interfaces;

use DateTime;

interface FacturaInterface extends BaseInterface

{
    public function getByFechaHora(dateTime $fechaHora);

    public function getBySubtotal(float $subtotal);

    public function getByImpuestos(float $impuestos);

    public function getByTotal(float $total);


}