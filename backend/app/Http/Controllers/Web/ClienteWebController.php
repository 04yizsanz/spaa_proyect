<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Throwable;

/**
 * Controlador de vistas Blade para el módulo de Clientes.
 * Renderiza el listado de clientes con su información de usuario asociado.
 */
class ClienteWebController extends Controller
{
    /**
     * Listado de clientes.
     * GET /clientes
     */
    public function index()
    {
        try {
            // El modelo Cliente se relaciona con Usuario para obtener nombre/apellido/email
            $clientes = Cliente::with('usuario')
                ->orderBy('cliente_id', 'desc')
                ->paginate(20);
            $modoDemo = false;
        } catch (Throwable $e) {
            $clientes = collect([]);
            $modoDemo = true;
        }

        return view('clients.index', compact('clientes', 'modoDemo'));
    }
}
