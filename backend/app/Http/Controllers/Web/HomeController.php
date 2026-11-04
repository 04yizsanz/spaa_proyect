<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Controlador para la página de inicio del sistema.
 *
 * Intenta obtener datos reales de la base de datos.
 * Si la base de datos no está disponible o las tablas aún no existen,
 * usa un array de demostración claramente identificado.
 */
class HomeController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        try {
            // Intentamos consultar datos reales
            $citasHoy = Cita::whereDate('fecha', $hoy)
                ->with(['cliente.usuario', 'servicio', 'empleado'])
                ->orderBy('hora')
                ->get();

            $totalCitasHoy  = $citasHoy->count();
            $proximasCitas  = $citasHoy->where('estado', '!=', 'cancelada')->take(5);
            $totalClientes  = Cliente::count();
            $totalEmpleados = Empleado::where('disponibilidad', true)->count();
            $totalServicios = Servicio::where('activo', true)->count();

            $modoDemo = false;

        } catch (Throwable $e) {
            // La base de datos no está lista o hay un error de conexión.
            // Usamos datos ficticios claramente marcados como demostración.
            $totalCitasHoy  = 0;
            $proximasCitas  = collect([]);
            $totalClientes  = 0;
            $totalEmpleados = 0;
            $totalServicios = 0;
            $modoDemo       = true;
            $citasHoy       = collect([]);
        }

        return view('home', compact(
            'totalCitasHoy',
            'proximasCitas',
            'totalClientes',
            'totalEmpleados',
            'totalServicios',
            'modoDemo',
            'citasHoy',
            'hoy'
        ));
    }
}
