<?php

namespace App\Services;

use App\Interfaces\FacturaServicioInterface;
use App\Models\Factura;
use App\Models\FacturaServicio;
use App\Models\Servicio;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Detalle de facturas (tabla intermedia factura_servicio).
 *
 *  - El precio unitario se toma del servicio en el momento de facturar; el
 *    cliente de la API no puede fijarlo (se conserva el histórico).
 *  - Cada alta o cambio recalcula los totales de la factura en la misma transacción.
 *  - Las líneas no se eliminan (FacturaServicioPolicy).
 */
class FacturaServicioService
{
    public function __construct(
        private FacturaServicioInterface $facturaServicioRepository,
        private FacturaService $facturaService
    ) {}

    /**
     * Líneas que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->facturaServicioRepository->visiblesPara($actor);
    }

    /**
     * Línea por clave compuesta solo si el usuario puede verla; null en otro caso (404).
     */
    public function buscarVisible(int $facturaId, int $servicioId, Usuario $actor): ?FacturaServicio
    {
        return $this->facturaServicioRepository->buscarVisible($facturaId, $servicioId, $actor);
    }

    public function show(array $id)
    {
        return $this->facturaServicioRepository->getById($id);
    }

    /**
     * @throws InvalidArgumentException si el servicio ya forma parte de la factura.
     */
    public function store(array $data, ?Usuario $actor = null): FacturaServicio
    {
        $linea = DB::transaction(function () use ($data) {
            $factura = Factura::query()->lockForUpdate()->findOrFail($data['factura_id']);
            $servicio = Servicio::query()->findOrFail($data['servicio_id']);

            if ($this->facturaServicioRepository->existeLinea($factura->factura_id, $servicio->servicio_id)) {
                throw new InvalidArgumentException('El servicio ya está incluido en la factura; actualiza su cantidad.');
            }

            $linea = $this->facturaServicioRepository->create([
                'factura_id' => $factura->factura_id,
                'servicio_id' => $servicio->servicio_id,
                'cantidad' => (int) $data['cantidad'],
                'precio_unitario' => $servicio->precio,
            ]);

            $this->facturaService->recalcularTotales($factura);

            return $linea;
        });

        $this->auditar('factura_detalle.agregado', $linea, $actor);

        return $linea;
    }

    /**
     * Solo se modifica la cantidad; el precio unitario histórico se conserva.
     */
    public function update(FacturaServicio $linea, array $data, ?Usuario $actor = null): FacturaServicio
    {
        $clave = ['factura_id' => $linea->factura_id, 'servicio_id' => $linea->servicio_id];

        $actualizada = DB::transaction(function () use ($clave, $data) {
            $factura = Factura::query()->lockForUpdate()->findOrFail($clave['factura_id']);

            $registro = $this->facturaServicioRepository->update(['cantidad' => (int) $data['cantidad']], $clave);

            $this->facturaService->recalcularTotales($factura);

            return $registro;
        });

        $this->auditar('factura_detalle.actualizado', $actualizada, $actor);

        return $actualizada;
    }

    public function getByFactura(int $factura_id)
    {
        return $this->facturaServicioRepository->getByFactura($factura_id);
    }

    public function getByServicio(int $servicio_id)
    {
        return $this->facturaServicioRepository->getByServicio($servicio_id);
    }

    private function auditar(string $operacion, FacturaServicio $linea, ?Usuario $actor): void
    {
        Log::channel('auditoria')->info($operacion, [
            'factura_id' => $linea->factura_id,
            'servicio_id' => $linea->servicio_id,
            'cantidad' => $linea->cantidad,
            'precio_unitario' => (string) $linea->precio_unitario,
            'usuario_id' => $actor?->usuario_id,
        ]);
    }
}
