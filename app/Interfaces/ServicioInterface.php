<?php

namespace App\Interfaces;

use App\Models\Servicio;
use Illuminate\Database\Eloquent\Collection;

interface ServicioInterface extends BaseInterface
{
    /**
     * Obtener todos los servicios activos.
     */
    public function getActivos(): Collection;

    /**
     * Buscar un servicio por nombre.
     */
    public function getByNombre(string $nombre): ?Servicio;

    /**
     * Cambiar el estado (activo/inactivo) de un servicio.
     */
    public function cambiarEstado(int $id, bool $activo): bool;
}