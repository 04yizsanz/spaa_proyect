<?php

namespace App\Services;

use App\Interfaces\PagoInterface;
use App\Models\Pago;
use App\Models\Usuario;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

/**
 * Pagos.
 *  - Ciclo de estados: 'pendiente' → 'aprobado' | 'rechazado' (ambos definitivos).
 *  - Registrar con estado distinto de 'pendiente' o cambiar el estado exige 'pagos.aprobar'.
 *  - Los pagos no se eliminan (PagoPolicy): este servicio no ofrece borrado.
 *  - Las operaciones se registran en el canal de log 'auditoria' (sin datos personales).
 */
class PagoService
{
    public function __construct(
        private PagoInterface $pagoRepository
    ) {}

    public function list()
    {
        return $this->pagoRepository->all();
    }

    /**
     * Pagos que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->pagoRepository->visiblesPara($actor);
    }

    /**
     * Pago por id solo si el usuario puede verlo; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor): ?Pago
    {
        return $this->pagoRepository->buscarVisible($id, $actor);
    }

    public function show(int $pagoId)
    {
        return $this->pagoRepository->find($pagoId);
    }

    public function store(array $data, Usuario $actor): Pago
    {
        $data['estado'] = $data['estado'] ?? 'pendiente';

        if ($data['estado'] !== 'pendiente') {
            Gate::forUser($actor)->authorize('aprobar', Pago::class);
        }

        $pago = $this->pagoRepository->create($data);

        $this->auditar('pago.registrado', $pago, $actor);

        return $pago->fresh();
    }

    /**
     * Se asume que PagoPolicy::update ya verificó el permiso y que el pago sigue 'pendiente'.
     */
    public function update(Pago $pago, array $data, Usuario $actor): Pago
    {
        $estadoAnterior = $pago->estado;

        if (array_key_exists('estado', $data) && $data['estado'] !== $estadoAnterior) {
            Gate::forUser($actor)->authorize('aprobar', $pago);
        }

        $pago->update($data);

        $this->auditar('pago.actualizado', $pago, $actor, ['estado_anterior' => $estadoAnterior]);

        return $pago->fresh();
    }

    public function getByCita(int $codigo_cita)
    {
        return $this->pagoRepository->getByCita($codigo_cita);
    }

    public function getByEstado(string $estado)
    {
        return $this->pagoRepository->getByEstado($estado);
    }

    public function getByFechaHora(DateTime $fechaHora)
    {
        return $this->pagoRepository->getByFechaHora($fechaHora);
    }

    private function auditar(string $operacion, Pago $pago, Usuario $actor, array $extra = []): void
    {
        Log::channel('auditoria')->info($operacion, [
            'pago_id' => $pago->pago_id,
            'codigo_cita' => $pago->codigo_cita,
            'monto' => (string) $pago->monto,
            'estado' => $pago->estado,
            'usuario_id' => $actor->usuario_id,
        ] + $extra);
    }
}
