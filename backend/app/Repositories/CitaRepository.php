<?php

namespace App\Repositories;

use App\Models\Cita;
use App\Models\Usuario;
use App\Interfaces\CitaInterface;
use Illuminate\Database\Eloquent\Collection;

class CitaRepository extends BaseRepository implements CitaInterface
{
    /**
     * Inyecta el modelo Cita al BaseRepository.
     */
    public function __construct(Cita $model)
    {
        parent::__construct($model);
    }

    /**
     * Obtener las citas de un cliente específico.
     */
    public function getByCliente(int $clienteId): Collection
    {
        return $this->model->where('cliente_id', $clienteId)->get();
    }

    /**
     * Obtener las citas asignadas a un empleado específico.
     */
    public function getByEmpleado(int $empleadoId): Collection
    {
        return $this->model->where('empleado_id', $empleadoId)->get();
    }

    /**
     * Obtener citas filtradas por estado.
     */
    public function getByEstado(string $estado): Collection
    {
        return $this->model->where('estado', $estado)->get();
    }

    /**
     * Obtener citas dentro de un rango de fechas.
     */
    public function getByRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return $this->model->whereBetween('fecha', [$fechaInicio, $fechaFin])->get();
    }

    /**
     * Cambiar el estado de una cita.
     */
    public function cambiarEstado(int $codigoCita, string $estado): bool
    {
        $cita = $this->model->find($codigoCita);

        if (! $cita) {
            return false;
        }

        $cita->estado = $estado;

        return $cita->save();
    }

    public function visiblesPara(Usuario $usuario): Collection
    {
        return $this->model->newQuery()
            ->visiblePara($usuario)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora')
            ->get();
    }

    public function buscarVisible(int $id, Usuario $usuario): ?Cita
    {
        return $this->model->newQuery()->visiblePara($usuario)->find($id);
    }

    public function existeCitaEnHorario(int $empleadoId, string $fecha, string $hora, ?int $exceptoCitasId = null): bool
    {
        return $this->model->newQuery()
            ->where('empleado_id', $empleadoId)
            ->whereDate('fecha', $fecha)
            ->where('hora', $hora)
            ->where('estado', '!=', 'cancelada')
            ->when($exceptoCitasId !== null, fn ($query) => $query->whereKeyNot($exceptoCitasId))
            ->exists();
    }

    public function siguienteCodigo(): int
    {
        return ((int) $this->model->newQuery()->withTrashed()->max('codigo_cita')) + 1;
    }
}