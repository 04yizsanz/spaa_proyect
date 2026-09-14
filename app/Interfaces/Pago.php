<?php
namespace App\Interfaces;

use DateTime;
use Ramsey\Uuid\Type\Decimal;

interface PagoInterface extends BaseInterface

{
    public function getByMonto(Float $monto);

    public function getByFechaHora(dateTime $fecha_hora);

}