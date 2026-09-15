<?php

namespace App\Services;

use App\Interfaces\CitaInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Cita;
use InvalidArgumentException;

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

    public function create(array $data): Cita
    {
        if ($this->existeConflictoHorario($data['empleado_id'], $data['fecha'], $data['hora'])) {
            throw new InvalidArgumentException('El empleado ya tiene una cita agendada en ese horario.');
        }

        return $this->citaRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['empleado_id'], $data['fecha'], $data['hora'])) {
            if ($this->existeConflictoHorario($data['empleado_id'], $data['fecha'], $data['hora'], $id)) {
                throw new InvalidArgumentException('El empleado ya tiene una cita agendada en ese horario.');
            }
        }

        return $this->citaRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->citaRepository->delete($id);
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
     * Verifica si un empleado ya tiene una cita agendada en la misma fecha y hora.
     * Excluye el propio registro cuando se está actualizando (vía $ignorarId).
     */
    protected function existeConflictoHorario(
        int $empleadoId,
        string $fecha,
        string $hora,
        ?int $ignorarId = null
    ): bool {
        $citas = $this->citaRepository->getByEmpleado($empleadoId);

        return $citas->contains(function (Cita $cita) use ($fecha, $hora, $ignorarId) {
            if ($ignorarId !== null && $cita->codigo_cita === $ignorarId) {
                return false;
            }

            return $cita->fecha->format('Y-m-d') === $fecha && $cita->hora === $hora;
        });
    }
}