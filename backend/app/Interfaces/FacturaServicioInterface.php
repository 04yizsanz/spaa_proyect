<?php
namespace App\Interfaces;

use App\Models\FacturaServicio;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface FacturaServicioInterface extends BaseInterface

{
    public function getByFactura(int $factura_id);

    public function getByServicio(int $servicio_id);

    public function getByCantidad(int $cantidad);

    public function getByPrecioUnitario(float $precio_unitario);

    /**
     * Líneas de factura que el usuario puede consultar (scope FacturaServicio::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca una línea (PK compuesta) solo entre las que el usuario puede consultar.
     */
    public function buscarVisible(int $facturaId, int $servicioId, Usuario $usuario): ?FacturaServicio;

    /**
     * Indica si el servicio ya forma parte de la factura.
     */
    public function existeLinea(int $facturaId, int $servicioId): bool;

}
