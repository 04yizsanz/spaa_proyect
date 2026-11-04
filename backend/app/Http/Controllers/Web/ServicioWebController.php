<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Throwable;

/**
 * Controlador de vistas Blade para el módulo de Servicios.
 */
class ServicioWebController extends Controller
{
    /**
     * Listado de servicios.
     * GET /servicios
     */
    public function index()
    {
        try {
            $servicios = Servicio::orderBy('nombre')->paginate(20);
            $modoDemo  = false;
        } catch (Throwable $e) {
            $servicios = collect([]);
            $modoDemo  = true;
        }

        return view('services.index', compact('servicios', 'modoDemo'));
    }
}
