<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Factura\StoreFacturaRequest;
use App\Http\Requests\Factura\UpdateFacturaRequest;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Servicio;
use App\Services\FacturaService;
use App\Services\FacturaServicioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * Vistas Blade del módulo de Facturas y su detalle (servicios facturados).
 *
 * Reglas de negocio (FacturaService / FacturaServicioService):
 *  - subtotal y total los calcula SIEMPRE el servidor a partir del detalle.
 *  - El precio unitario se toma del servicio al momento de facturar.
 *  - Las facturas y sus líneas no se eliminan.
 *  - Un Cliente solo ve sus propias facturas.
 */
class FacturaWebController extends Controller
{
    public function __construct(
        protected FacturaService $facturaService,
        protected FacturaServicioService $detalleService
    ) {}

    /** GET /facturas */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Factura::class);

        $facturas = $this->facturaService->paginar([
            'visiblePara' => $request->user(),
            'filtros'     => ['cliente_id' => $request->query('cliente')],
            'con'         => ['cliente.usuario'],
            'orden'       => ['fecha_hora', 'desc'],
        ]);

        return view('facturas.index', [
            'facturas' => $facturas,
            'clientes' => $request->user()->can('create', Factura::class) ? $this->opcionesCliente() : [],
        ]);
    }

    /** GET /facturas/nuevo */
    public function create()
    {
        Gate::authorize('create', Factura::class);

        return view('facturas.create', [
            'factura'  => new Factura(['fecha_hora' => now(), 'impuestos' => 0]),
            'clientes' => $this->opcionesCliente(),
        ]);
    }

    /** POST /facturas — se crea vacía y luego se le agregan servicios */
    public function store(StoreFacturaRequest $request)
    {
        Gate::authorize('create', Factura::class);

        $factura = $this->facturaService->store($request->validated(), $request->user());

        return redirect()->route('facturas.show', $factura->factura_id)
            ->with('success', 'Factura creada. Ahora agrega los servicios facturados.');
    }

    /** GET /facturas/{id} */
    public function show(Request $request, int $factura)
    {
        $registro = $this->buscar($factura, $request);
        $registro->load(['cliente.usuario', 'servicios.servicio']);

        $puedeEditar = Gate::allows('update', $registro);
        $yaIncluidos = $registro->servicios->pluck('servicio_id')->all();

        return view('facturas.show', [
            'factura'     => $registro,
            'puedeEditar' => $puedeEditar,
            'servicios'   => $puedeEditar
                ? Servicio::where('activo', true)->whereNotIn('servicio_id', $yaIncluidos)->orderBy('nombre')->get()
                : collect(),
        ]);
    }

    /** GET /facturas/{id}/editar */
    public function edit(Request $request, int $factura)
    {
        $registro = $this->buscar($factura, $request);
        Gate::authorize('update', $registro);

        return view('facturas.edit', ['factura' => $registro, 'clientes' => $this->opcionesCliente()]);
    }

    /** PUT /facturas/{id} */
    public function update(UpdateFacturaRequest $request, int $factura)
    {
        $registro = $this->buscar($factura, $request);
        Gate::authorize('update', $registro);

        $this->facturaService->update($registro, $request->validated(), $request->user());

        return redirect()->route('facturas.show', $registro->factura_id)->with('success', 'Factura actualizada.');
    }

    /** POST /facturas/{id}/servicios — agrega una línea al detalle */
    public function agregarServicio(Request $request, int $factura)
    {
        $registro = $this->buscar($factura, $request);
        Gate::authorize('update', $registro);

        $datos = $request->validate([
            'servicio_id' => ['required', 'integer', 'exists:servicios,servicio_id'],
            'cantidad'    => ['required', 'integer', 'min:1', 'max:99'],
        ], [
            'servicio_id.required' => 'Selecciona un servicio.',
            'cantidad.min'         => 'La cantidad debe ser al menos 1.',
        ]);

        try {
            $this->detalleService->store($datos + ['factura_id' => $registro->factura_id], $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('facturas.show', $registro->factura_id)->with('success', 'Servicio agregado a la factura.');
    }

    /** PUT /facturas/{id}/servicios/{servicio} — cambia la cantidad de una línea */
    public function actualizarServicio(Request $request, int $factura, int $servicio)
    {
        $registro = $this->buscar($factura, $request);
        Gate::authorize('update', $registro);

        $linea = $this->detalleService->buscarVisible($registro->factura_id, $servicio, $request->user()) ?? abort(404);

        $datos = $request->validate(['cantidad' => ['required', 'integer', 'min:1', 'max:99']]);
        $this->detalleService->update($linea, $datos, $request->user());

        return redirect()->route('facturas.show', $registro->factura_id)->with('success', 'Cantidad actualizada.');
    }

    /** Busca la factura solo entre las que el usuario puede ver (si no, 404). */
    private function buscar(int $id, Request $request): Factura
    {
        $factura = $this->facturaService->buscarVisible($id, $request->user()) ?? abort(404);
        Gate::authorize('view', $factura);

        return $factura;
    }

    /** @return array<int, string> [cliente_id => nombre completo] */
    private function opcionesCliente(): array
    {
        return Cliente::with('usuario')->get()
            ->mapWithKeys(fn (Cliente $c) => [
                $c->cliente_id => trim(($c->usuario?->nombre ?? 'Cliente') . ' ' . ($c->usuario?->apellido ?? ''))
                    . ($c->usuario?->documento ? " · {$c->usuario->documento}" : ''),
            ])
            ->sort()
            ->all();
    }
}
