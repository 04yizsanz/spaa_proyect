<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovimientoInventario\StoreMovimientoInventarioRequest;
use App\Http\Requests\MovimientoInventario\UpdateMovimientoInventarioRequest;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\MovimientoInventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Vistas Blade de los movimientos de inventario (entradas y salidas).
 * Capas: MovimientoInventarioService → MovimientoInventarioRepository.
 * Permisos: MovimientoInventarioPolicy.
 */
class MovimientoInventarioWebController extends Controller
{
    public function __construct(
        protected MovimientoInventarioService $movimientoService
    ) {}

    /** GET /movimientos */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MovimientoInventario::class);

        $tipo = in_array($request->query('tipo'), ['entrada', 'salida'], true) ? $request->query('tipo') : null;

        $movimientos = $this->movimientoService->paginar([
            'buscar'  => $request->query('buscar'),
            'filtros' => ['tipo' => $tipo, 'producto_id' => $request->query('producto')],
            'con'     => ['producto'],
            'orden'   => ['fecha_hora', 'desc'],
        ]);

        return view('movimientos.index', [
            'movimientos' => $movimientos,
            'productos'   => $this->opcionesProducto(),
        ]);
    }

    /** GET /movimientos/nuevo  (acepta ?producto=ID para preseleccionarlo) */
    public function create(Request $request)
    {
        Gate::authorize('create', MovimientoInventario::class);

        return view('movimientos.create', [
            'movimiento' => new MovimientoInventario([
                'fecha_hora'  => now(),
                'tipo'        => 'entrada',
                'producto_id' => $request->integer('producto') ?: null,
            ]),
            'productos' => $this->opcionesProducto(),
        ]);
    }

    /** POST /movimientos */
    public function store(StoreMovimientoInventarioRequest $request)
    {
        Gate::authorize('create', MovimientoInventario::class);

        $movimiento = $this->movimientoService->store($request->validated());

        return redirect()->route('movimientos.show', $movimiento->movimiento_id)
            ->with('success', 'Movimiento registrado.');
    }

    /** GET /movimientos/{id} */
    public function show(int $movimiento)
    {
        $registro = $this->buscar($movimiento);
        Gate::authorize('view', $registro);

        return view('movimientos.show', ['movimiento' => $registro->load('producto')]);
    }

    /** GET /movimientos/{id}/editar */
    public function edit(int $movimiento)
    {
        $registro = $this->buscar($movimiento);
        Gate::authorize('update', $registro);

        return view('movimientos.edit', [
            'movimiento' => $registro,
            'productos'  => $this->opcionesProducto(),
        ]);
    }

    /** PUT /movimientos/{id} */
    public function update(UpdateMovimientoInventarioRequest $request, int $movimiento)
    {
        $registro = $this->buscar($movimiento);
        Gate::authorize('update', $registro);

        $this->movimientoService->update($registro->movimiento_id, $request->validated());

        return redirect()->route('movimientos.show', $registro->movimiento_id)
            ->with('success', 'Movimiento actualizado.');
    }

    /** DELETE /movimientos/{id} */
    public function destroy(int $movimiento)
    {
        $registro = $this->buscar($movimiento);
        Gate::authorize('delete', $registro);

        $this->movimientoService->destroy($registro->movimiento_id);

        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado.');
    }

    private function buscar(int $id): MovimientoInventario
    {
        return $this->movimientoService->show($id) ?? abort(404);
    }

    /** @return array<int, string> */
    private function opcionesProducto(): array
    {
        return Producto::orderBy('nombre')->pluck('nombre', 'producto_id')->all();
    }
}
