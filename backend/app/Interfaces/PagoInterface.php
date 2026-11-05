<?php
namespace App\Interfaces;

use App\Models\Pago;
use App\Models\Usuario;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

interface PagoInterface extends BaseInterface

{
    public function getByMonto(float $monto);

    public function getByFechaHora(DateTime $fecha_hora);

    public function getByCita(int $codigo_cita);

    public function getByEstado(string $estado);

    /**
     * Pagos que el usuario puede consultar (scope Pago::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca un pago por id solo entre los que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Pago;

}
