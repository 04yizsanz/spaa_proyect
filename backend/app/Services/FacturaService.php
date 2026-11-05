<?php

namespace App\Services;

use App\Interfaces\FacturaInterface;
use App\Models\Factura;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Facturas. subtotal y total los calcula siempre el servidor:
 *   subtotal = Σ (cantidad × precio_unitario) de su detalle
 *   total    = subtotal + impuestos
 *
 * Las facturas no se eliminan (FacturaPolicy): este servicio no ofrece
 * ninguna operación de borrado. Las operaciones se registran en el canal
 * de log 'auditoria' (solo identificadores, sin datos personales).
 */
class FacturaService
{
    public function __construct(
        private FacturaInterface $facturaRepository
    ) {}

    public function list()
    {
        return $this->facturaRepository->all();
    }

    /**
     * Facturas que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->facturaRepository->visiblesPara($actor);
    }

    /**
     * Factura por id solo si el usuario puede verla; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor): ?Factura
    {
        return $this->facturaRepository->buscarVisible($id, $actor);
    }

    public function show(int $id)
    {
        return $this->facturaRepository->find($id);
    }

    public function store(array $data, ?Usuario $actor = null): Factura
    {
        $factura = DB::transaction(function () use ($data) {
            $impuestos = round((float) ($data['impuestos'] ?? 0), 2);

            // Una factura nueva no tiene detalle: subtotal 0 y total = impuestos.
            return $this->facturaRepository->create([
                'cliente_id' => $data['cliente_id'],
                'fecha_hora' => $data['fecha_hora'],
                'pdf_url' => $data['pdf_url'] ?? null,
                'impuestos' => $impuestos,
                'subtotal' => 0,
                'total' => $impuestos,
            ]);
        });

        $this->auditar('factura.creada', $factura, $actor);

        return $factura->fresh();
    }

    /**
     * Se asume que FacturaPolicy::update ya autorizó la edición.
     * Solo se aceptan campos editables; los totales se recalculan.
     */
    public function update(Factura $factura, array $data, ?Usuario $actor = null): Factura
    {
        DB::transaction(function () use ($factura, $data) {
            $factura->fill(array_intersect_key($data, array_flip(['cliente_id', 'fecha_hora', 'pdf_url', 'impuestos'])));
            $this->recalcularTotales($factura);
        });

        $this->auditar('factura.actualizada', $factura, $actor, ['campos' => array_keys($data)]);

        return $factura->fresh();
    }

    /**
     * Recalcula subtotal y total a partir del detalle y guarda la factura.
     * Se trabaja en céntimos para evitar errores de redondeo de coma flotante.
     */
    public function recalcularTotales(Factura $factura): Factura
    {
        $subtotalCentimos = $factura->servicios()
            ->get(['cantidad', 'precio_unitario'])
            ->sum(fn ($linea) => (int) $linea->cantidad * (int) round(((float) $linea->precio_unitario) * 100));

        $impuestosCentimos = (int) round(((float) $factura->impuestos) * 100);

        $factura->subtotal = $subtotalCentimos / 100;
        $factura->total = ($subtotalCentimos + $impuestosCentimos) / 100;
        $factura->save();

        return $factura;
    }

    public function getByCliente(int $cliente_id)
    {
        return $this->facturaRepository->getByCliente($cliente_id);
    }

    private function auditar(string $operacion, Factura $factura, ?Usuario $actor, array $extra = []): void
    {
        Log::channel('auditoria')->info($operacion, [
            'factura_id' => $factura->factura_id,
            'usuario_id' => $actor?->usuario_id,
            'subtotal' => (string) $factura->subtotal,
            'impuestos' => (string) $factura->impuestos,
            'total' => (string) $factura->total,
        ] + $extra);
    }
}
