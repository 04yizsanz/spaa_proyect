<?php

namespace App\Services;

use App\Interfaces\CitaInterface;
use App\Models\Cita;
use App\Models\Usuario;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Reglas de negocio de citas, compartidas por la API y la web.
 *
 * La autorización sobre una cita concreta (ver, editar, eliminar) la resuelve
 * CitaPolicy. Aquí se aplican las reglas de campo: propietario, estado,
 * código y disponibilidad del empleado.
 */
class CitaService
{
    public function __construct(
        protected CitaInterface $citaRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->citaRepository->getAll();
    }

    public function getById(int $id): ?Cita
    {
        return $this->citaRepository->getById($id);
    }

    /**
     * Citas que el usuario puede consultar.
     */
    public function listarPara(Usuario $actor): Collection
    {
        return $this->citaRepository->visiblesPara($actor);
    }

    /**
     * Cita por id solo si el usuario puede verla; null en otro caso (se responde 404).
     */
    public function buscarVisible(int $id, Usuario $actor): ?Cita
    {
        return $this->citaRepository->buscarVisible($id, $actor);
    }

    /**
     * @throws AuthorizationException si el usuario intenta agendar para otro cliente o con un estado no permitido.
     * @throws InvalidArgumentException si el empleado ya tiene una cita en ese horario.
     */
    public function crear(array $data, Usuario $actor): Cita
    {
        if (! $actor->tienePermiso('citas.gestionar')) {
            $clienteId = $actor->cliente?->cliente_id;

            if ($clienteId === null) {
                throw new AuthorizationException('Tu cuenta no tiene un perfil de cliente asociado.');
            }

            if (isset($data['cliente_id']) && (int) $data['cliente_id'] !== (int) $clienteId) {
                throw new AuthorizationException('No puedes agendar citas a nombre de otro cliente.');
            }

            // Un cliente siempre agenda a su nombre y el código lo asigna el servidor.
            $data['cliente_id'] = $clienteId;
            unset($data['codigo_cita']);
        }

        $data['estado'] = $data['estado'] ?? 'pendiente';
        Gate::forUser($actor)->authorize('crearConEstado', [Cita::class, $data['estado']]);

        $data['hora'] = $this->normalizarHora($data['hora']);

        if ($data['estado'] !== 'cancelada') {
            $this->verificarDisponibilidad((int) $data['empleado_id'], $data['fecha'], $data['hora']);
        }

        return $this->crearConCodigo($data);
    }

    /**
     * Se asume que CitaPolicy::update ya autorizó la edición de la cita.
     *
     * @throws AuthorizationException si se intenta reasignar, cambiar el código o usar un estado no permitido.
     * @throws InvalidArgumentException si el empleado ya tiene una cita en ese horario.
     */
    public function actualizar(Cita $cita, array $data, Usuario $actor): Cita
    {
        $gate = Gate::forUser($actor);

        if (array_key_exists('cliente_id', $data) && (int) $data['cliente_id'] !== (int) $cita->cliente_id) {
            $gate->authorize('reasignar', $cita);
        }

        if (array_key_exists('codigo_cita', $data)
            && (int) $data['codigo_cita'] !== (int) $cita->codigo_cita
            && ! $actor->tienePermiso('citas.gestionar')) {
            throw new AuthorizationException('No puedes modificar el código de la cita.');
        }

        if (array_key_exists('estado', $data) && $data['estado'] !== $cita->estado) {
            $gate->authorize('cambiarEstado', [$cita, $data['estado']]);
        }

        if (array_key_exists('hora', $data)) {
            $data['hora'] = $this->normalizarHora($data['hora']);
        }

        $horaActual = $this->normalizarHora((string) $cita->hora);
        $empleadoId = (int) ($data['empleado_id'] ?? $cita->empleado_id);
        $fecha = Carbon::parse($data['fecha'] ?? $cita->fecha)->toDateString();
        $hora = $data['hora'] ?? $horaActual;
        $estado = $data['estado'] ?? $cita->estado;

        $cambiaHorario = $empleadoId !== (int) $cita->empleado_id
            || $fecha !== $cita->fecha?->toDateString()
            || $hora !== $horaActual;
        $seReactiva = $cita->estado === 'cancelada' && $estado !== 'cancelada';

        if (($cambiaHorario || $seReactiva) && $estado !== 'cancelada') {
            $this->verificarDisponibilidad($empleadoId, $fecha, $hora, (int) $cita->getKey());
        }

        $cita->update($data);

        return $cita->fresh();
    }

    public function eliminar(Cita $cita): bool
    {
        return (bool) $cita->delete();
    }

    public function delete(int $id): bool
    {
        // El repositorio devuelve null si la cita no existe.
        return (bool) $this->citaRepository->delete($id);
    }

    public function getByCliente(int $clienteId): Collection
    {
        return $this->citaRepository->getByCliente($clienteId);
    }

    public function getByEmpleado(int $empleadoId): Collection
    {
        return $this->citaRepository->getByEmpleado($empleadoId);
    }

    public function getByEstado(string $estado): Collection
    {
        return $this->citaRepository->getByEstado($estado);
    }

    public function getByRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return $this->citaRepository->getByRangoFechas($fechaInicio, $fechaFin);
    }

    public function cambiarEstado(int $codigoCita, string $estado): bool
    {
        return $this->citaRepository->cambiarEstado($codigoCita, $estado);
    }

    /**
     * Crea la cita asignando el código en el servidor cuando no viene informado.
     * Si dos altas simultáneas obtienen el mismo código, la restricción única de
     * la base de datos lo detecta y se reintenta.
     */
    private function crearConCodigo(array $data): Cita
    {
        if (! empty($data['codigo_cita'])) {
            return $this->citaRepository->create($data);
        }

        for ($intento = 1; ; $intento++) {
            $data['codigo_cita'] = $this->citaRepository->siguienteCodigo();

            try {
                return $this->citaRepository->create($data);
            } catch (UniqueConstraintViolationException $e) {
                if ($intento >= 3) {
                    throw $e;
                }
            }
        }
    }

    private function verificarDisponibilidad(int $empleadoId, string $fecha, string $hora, ?int $exceptoCitasId = null): void
    {
        $fecha = Carbon::parse($fecha)->toDateString();

        if ($this->citaRepository->existeCitaEnHorario($empleadoId, $fecha, $hora, $exceptoCitasId)) {
            throw new InvalidArgumentException('El empleado ya tiene una cita agendada en ese horario.');
        }
    }

    /**
     * Normaliza la hora al formato de la columna TIME (H:i:s): el formulario y la
     * API envían H:i y la base de datos devuelve H:i:s.
     */
    private function normalizarHora(string $hora): string
    {
        return Carbon::parse($hora)->format('H:i:s');
    }
}
