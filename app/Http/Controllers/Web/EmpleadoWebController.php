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
        try {
            $empleados = Empleado::orderBy('nombre')->paginate(20);
            $modoDemo  = false;
        } catch (Throwable $e) {
            $empleados = collect([]);
            $modoDemo  = true;
        }

        return view('staff.index', compact('empleados', 'modoDemo'));
    }
}
