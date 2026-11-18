<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\ProductoWebController;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Servicio;
use Illuminate\Http\Request;

/**
 * Página de inicio. Muestra solo la información que el usuario puede ver:
 *  - Cliente: sus próximas citas y accesos rápidos.
 *  - Personal: la agenda del día y los indicadores que su rol permite.
 */
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $hoy     = now()->toDateString();

        if ($usuario->esCliente()) {
            $proximas = Cita::visiblePara($usuario)
                ->with(['servicio', 'empleado'])
                ->whereDate('fecha', '>=', $hoy)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->orderBy('fecha')->orderBy('hora')
                ->limit(5)->get();

            $servicios = Servicio::where('activo', true)->inRandomOrder()->limit(3)->get();

            return view('home', ['esCliente' => true, 'proximas' => $proximas, 'servicios' => $servicios]);
        }

        $citasHoy = Cita::visiblePara($usuario)
            ->with(['cliente.usuario', 'servicio', 'empleado'])
            ->whereDate('fecha', $hoy)
            ->orderBy('hora')
            ->get();

        // Cada indicador se calcula solo si el rol tiene permiso para verlo.
        $indicadores = [
            ['etiqueta' => 'Citas hoy', 'valor' => $citasHoy->where('estado', '!=', 'cancelada')->count(), 'ruta' => route('citas.index', ['fecha' => $hoy])],
        ];

        if ($usuario->can('viewAny', Cliente::class)) {
            $indicadores[] = ['etiqueta' => 'Clientes', 'valor' => Cliente::count(), 'ruta' => route('clientes.index')];
        }
        if ($usuario->tienePermiso('pagos.ver_todos')) {
            $indicadores[] = ['etiqueta' => 'Pagos por revisar', 'valor' => Pago::where('estado', 'pendiente')->count(), 'ruta' => route('pagos.index', ['estado' => 'pendiente'])];
        }
        if ($usuario->can('viewAny', Producto::class)) {
            $indicadores[] = ['etiqueta' => 'Productos con stock bajo', 'valor' => Producto::where('cantidad', '<=', ProductoWebController::STOCK_MINIMO)->count(), 'ruta' => route('productos.index')];
        }

        return view('home', ['esCliente' => false, 'citasHoy' => $citasHoy, 'indicadores' => $indicadores]);
    }
}
