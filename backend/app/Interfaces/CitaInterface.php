<?php

namespace App\Interfaces;

use App\Models\Cita;
use App\Models\Usuario;
use App\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Collection;

interface CitaInterface extends BaseInterface
{
    /**
     * Obtener las citas de un cliente específico.
     */
    public function getByCliente(int $clienteId): Collection;

    /**
     * Obtener las citas asignadas a un empleado específico.
     */
    public function getByEmpleado(int $empleadoId): Collection;

    /**
     * Obtener citas filtradas por estado (ej. pendiente, confirmada, cancelada).
     */
    public function getByEstado(string $estado): Collection;

    /**
     * Obtener citas dentro de un rango de fechas.
     */
    public function getByRangoFechas(string $fechaInicio, string $fechaFin): Collection;

    /**
     * Cambiar el estado de una cita.
     */
    public function cambiarEstado(int $codigoCita, string $estado): bool;

    /**
     * Citas que el usuario puede consultar (scope Cita::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca una cita por id solo entre las que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Cita;

    /**
     * Indica si el empleado ya tiene una cita no cancelada en esa fecha y hora.
     */
    public function existeCitaEnHorario(int $empleadoId, string $fecha, string $hora, ?int $exceptoCitasId = null): bool;

    /**
     * Siguiente código de cita disponible (incluye citas eliminadas lógicamente).
     */
    public function siguienteCodigo(): int;
}
