<?php
namespace App\Interfaces;

use App\Models\Factura;
use App\Models\Usuario;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

interface FacturaInterface extends BaseInterface

{
    public function getByFechaHora(DateTime $fechaHora);

    public function getBySubtotal(float $subtotal);

    public function getByImpuestos(float $impuestos);

    public function getByTotal(float $total);

    public function getByCliente(int $cliente_id);

    /**
     * Facturas que el usuario puede consultar (scope Factura::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca una factura por id solo entre las que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Factura;

}
