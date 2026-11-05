<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\FacturaServicio;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

/**
 * Detalle de facturas: se consulta con los mismos permisos que la factura y
 * se edita con 'facturas.gestionar'. Las líneas no se eliminan (trazabilidad).
 */
class FacturaServicioPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.ver_todas') || $usuario->tienePermiso('facturas.propias');
    }

    public function view(Usuario $usuario, FacturaServicio $linea): Response
    {
        return $this->puedeVer($usuario, $linea)
            ? Response::allow()
            : Response::denyAsNotFound('Registro no encontrado');
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->tienePermiso('facturas.gestionar');
    }

    public function update(Usuario $usuario, FacturaServicio $linea): Response
    {
        if ($usuario->tienePermiso('facturas.gestionar')) {
            return Response::allow();
        }

        return $this->puedeVer($usuario, $linea)
            ? Response::deny('No tienes permiso para modificar facturas.')
            : Response::denyAsNotFound('Registro no encontrado');
    }

    public function delete(Usuario $usuario, FacturaServicio $linea): Response
    {
        return $this->puedeVer($usuario, $linea)
            ? Response::deny('Los registros financieros no se eliminan.')
            : Response::denyAsNotFound('Registro no encontrado');
    }

    private function puedeVer(Usuario $usuario, FacturaServicio $linea): bool
    {
        $factura = Factura::find($linea->factura_id);

        return $factura !== null && (new FacturaPolicy)->puedeVer($usuario, $factura);
    }
}
