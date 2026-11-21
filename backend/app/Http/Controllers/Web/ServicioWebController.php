<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Servicio\StoreServicioRequest;
use App\Http\Requests\Servicio\UpdateServicioRequest;
use App\Models\Servicio;
use App\Services\ServicioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Vistas Blade del catálogo de Servicios.
 * Capas: ServicioService → ServicioRepository. Permisos: ServicioPolicy.
 * Los clientes ven solo los servicios activos.
 */
class ServicioWebController extends Controller
{
    public function __construct(
        protected ServicioService $servicioService
    ) {}

    /** GET /servicios */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Servicio::class);

        $puedeGestionar = $request->user()->can('create', Servicio::class);

        $servicios = $this->servicioService->paginar([
            'buscar'  => $request->query('buscar'),
            'filtros' => ['activo' => $puedeGestionar ? $request->query('activo') : true],
            'orden'   => ['nombre', 'asc'],
            'porPagina' => 18,
        ]);

        return view('services.index', compact('servicios', 'puedeGestionar'));
    }

    /** GET /servicios/nuevo */
    public function create()
    {
        Gate::authorize('create', Servicio::class);

        return view('services.create', ['servicio' => new Servicio(['activo' => true])]);
    }

    /** POST /servicios */
    public function store(StoreServicioRequest $request)
    {
        Gate::authorize('create', Servicio::class);

        $datos = $request->validated() + ['activo' => $request->boolean('activo')];
        $servicio = $this->servicioService->create($datos);

        return redirect()->route('servicios.show', $servicio->servicio_id)->with('success', 'Servicio creado.');
    }

    /** GET /servicios/{id} */
    public function show(Request $request, int $servicio)
    {
        $registro = $this->buscar($servicio);
        Gate::authorize('view', $registro);

        // Un servicio inactivo solo lo ve el personal que gestiona el catálogo.
        if (! $registro->activo && ! $request->user()->can('update', $registro)) {
            abort(404);
        }

        return view('services.show', ['servicio' => $registro]);
    }

    /** GET /servicios/{id}/editar */
    public function edit(int $servicio)
    {
        $registro = $this->buscar($servicio);
        Gate::authorize('update', $registro);

        return view('services.edit', ['servicio' => $registro]);
    }

    /** PUT /servicios/{id} */
    public function update(UpdateServicioRequest $request, int $servicio)
    {
        $registro = $this->buscar($servicio);
        Gate::authorize('update', $registro);

        $datos = $request->validated() + ['activo' => $request->boolean('activo')];
        $this->servicioService->update($registro->servicio_id, $datos);

        return redirect()->route('servicios.show', $registro->servicio_id)->with('success', 'Servicio actualizado.');
    }

    /** DELETE /servicios/{id} */
    public function destroy(int $servicio)
    {
        $registro = $this->buscar($servicio);
        Gate::authorize('delete', $registro);

        $this->servicioService->delete($registro->servicio_id);

        return redirect()->route('servicios.index')->with('success', 'Servicio eliminado.');
    }

    private function buscar(int $id): Servicio
    {
        return $this->servicioService->getById($id) ?? abort(404);
    }
}
