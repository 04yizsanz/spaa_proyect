<?php
namespace App\Interfaces;

use DateTime;

interface PagoInterface extends BaseInterface

{
    public function getByMonto(float $monto);

    public function getByFechaHora(DateTime $fecha_hora);

    public function getByCita(int $codigo_cita);

    public function getByEstado(string $estado);

}