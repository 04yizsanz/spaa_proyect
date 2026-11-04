<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Throwable;

/**
 * Controlador de vistas Blade para el módulo de Empleados.
 */
class EmpleadoWebController extends Controller
{
    /**
     * Listado de empleados.
     * GET /empleados
     */
    public function index()
    {
        $esCliente = auth()->user()->esCliente();

        try {
            $consulta = Empleado::query();

            if ($esCliente) {
                $consulta->select([
                    'empleado_id',
                    'nombre',
                    'apellido',
                    'rol',
                    'disponibilidad',
                ]);
            }

            $empleados = $consulta->orderBy('nombre')->paginate(20);
            $modoDemo  = false;
        } catch (Throwable $e) {
            $empleados = collect([]);
            $modoDemo  = true;
        }

        return view('staff.index', compact('empleados', 'modoDemo', 'esCliente'));
    }
}
