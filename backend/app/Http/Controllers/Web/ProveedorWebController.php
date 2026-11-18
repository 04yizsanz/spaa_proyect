<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Proveedor\StoreProveedorRequest;
use App\Http\Requests\Proveedor\UpdateProveedorRequest;
use App\Models\Proveedor;
use App\Services\ProveedorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Vistas Blade del módulo de Proveedores.
 *
 * Usa las mismas capas que la API (ProveedorService → ProveedorRepository)
 * y los mismos Form Requests; la diferencia es que responde con vistas y
 * redirecciones en lugar de JSON. Permisos: ProveedorPolicy
 * (inventario.ver / inventario.gestionar).
 */
class ProveedorWebController extends Controller
{
    public function __construct(
        protected ProveedorService $proveedorService
    ) {}

    /** GET /proveedores */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Proveedor::class);

        $proveedores = $this->proveedorService->paginar([
            'buscar' => $request->query('buscar'),
            'orden'  => ['nombre', 'asc'],
        ]);

        return view('proveedores.index', compact('proveedores'));
    }

    /** GET /proveedores/nuevo */
    public function create()
    {
        Gate::authorize('create', Proveedor::class);

        return view('proveedores.create', ['proveedor' => new Proveedor()]);
    }

    /** POST /proveedores */
    public function store(StoreProveedorRequest $request)
    {
        Gate::authorize('create', Proveedor::class);

        $proveedor = $this->proveedorService->store($request->validated());

        return redirect()->route('proveedores.show', $proveedor->proveedor_id)
            ->with('success', 'Proveedor registrado.');
    }

    /** GET /proveedores/{id} */
    public function show(int $proveedore)
    {
        $proveedor = $this->buscar($proveedore);
        Gate::authorize('view', $proveedor);

        $proveedor->load(['producto' => fn ($q) => $q->orderBy('nombre')]);

        return view('proveedores.show', compact('proveedor'));
    }

    /** GET /proveedores/{id}/editar */
    public function edit(int $proveedore)
    {
        $proveedor = $this->buscar($proveedore);
        Gate::authorize('update', $proveedor);

        return view('proveedores.edit', compact('proveedor'));
    }

    /** PUT /proveedores/{id} */
    public function update(UpdateProveedorRequest $request, int $proveedore)
    {
        $proveedor = $this->buscar($proveedore);
        Gate::authorize('update', $proveedor);

        $this->proveedorService->update($proveedor->proveedor_id, $request->validated());

        return redirect()->route('proveedores.show', $proveedor->proveedor_id)
            ->with('success', 'Proveedor actualizado.');
    }

    /** DELETE /proveedores/{id} */
    public function destroy(int $proveedore)
    {
        $proveedor = $this->buscar($proveedore);
        Gate::authorize('delete', $proveedor);

        // Un proveedor con productos asociados no se elimina: se perdería su trazabilidad.
        if ($proveedor->producto()->exists()) {
            return back()->with('error', 'No se puede eliminar: el proveedor tiene productos asociados.');
        }

        $this->proveedorService->destroy($proveedor->proveedor_id);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    private function buscar(int $id): Proveedor
    {
        return $this->proveedorService->show($id) ?? abort(404);
    }
}
