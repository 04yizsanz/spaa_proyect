<?php
namespace App\Interfaces;

use DateTime;

interface PagoInterface extends BaseInterface

{
    public function getByMonto(decimal $monto);

    public function getByFechaHora(dateTime $fecha_hora);

}