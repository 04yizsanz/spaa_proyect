<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\ProductoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Vistas Blade del módulo de Productos (inventario).
 * Capas: ProductoService → ProductoRepository. Permisos: ProductoPolicy.
 */
class ProductoWebController extends Controller
{
    /** Por debajo de esta cantidad el producto se marca como "stock bajo". */
    public const STOCK_MINIMO = 5;

    public function __construct(
        protected ProductoService $productoService
    ) {}

    /** GET /productos */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Producto::class);

        $productos = $this->productoService->paginar([
            'buscar'  => $request->query('buscar'),
            'filtros' => ['proveedor_id' => $request->query('proveedor')],
            'con'     => ['proveedor'],
            'orden'   => ['nombre', 'asc'],
        ]);

        return view('productos.index', [
            'productos'   => $productos,
            'proveedores' => $this->opcionesProveedor(),
            'stockMinimo' => self::STOCK_MINIMO,
        ]);
    }

    /** GET /productos/nuevo */
    public function create()
    {
        Gate::authorize('create', Producto::class);

        return view('productos.create', [
            'producto'    => new Producto(['fecha_registro' => now()->toDateString(), 'cantidad' => 0]),
            'proveedores' => $this->opcionesProveedor(),
        ]);
    }

    /** POST /productos */
    public function store(StoreProductoRequest $request)
    {
        Gate::authorize('create', Producto::class);

        $producto = $this->productoService->store($request->validated());

        return redirect()->route('productos.show', $producto->producto_id)
            ->with('success', 'Producto registrado.');
    }

    /** GET /productos/{id} */
    public function show(int $producto)
    {
        $registro = $this->buscar($producto);
        Gate::authorize('view', $registro);

        $registro->load('proveedor');
        $movimientos = $registro->movimiento_inventario()->orderByDesc('fecha_hora')->limit(10)->get();

        return view('productos.show', [
            'producto'    => $registro,
            'movimientos' => $movimientos,
            'stockMinimo' => self::STOCK_MINIMO,
        ]);
    }

    /** GET /productos/{id}/editar */
    public function edit(int $producto)
    {
        $registro = $this->buscar($producto);
        Gate::authorize('update', $registro);

        return view('productos.edit', [
            'producto'    => $registro,
            'proveedores' => $this->opcionesProveedor(),
        ]);
    }

    /** PUT /productos/{id} */
    public function update(UpdateProductoRequest $request, int $producto)
    {
        $registro = $this->buscar($producto);
        Gate::authorize('update', $registro);

        $this->productoService->update($registro->producto_id, $request->validated());

        return redirect()->route('productos.show', $registro->producto_id)
            ->with('success', 'Producto actualizado.');
    }

    /** DELETE /productos/{id} */
    public function destroy(int $producto)
    {
        $registro = $this->buscar($producto);
        Gate::authorize('delete', $registro);

        $this->productoService->destroy($registro->producto_id);

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    private function buscar(int $id): Producto
    {
        return $this->productoService->show($id) ?? abort(404);
    }

    /** @return array<int, string> [proveedor_id => nombre] */
    private function opcionesProveedor(): array
    {
        return Proveedor::orderBy('nombre')->pluck('nombre', 'proveedor_id')->all();
    }
}
