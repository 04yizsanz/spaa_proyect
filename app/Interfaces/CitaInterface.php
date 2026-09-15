<?php

namespace App\Interfaces;

use App\Models\Cita;
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
}