<?php
namespace App\Http\Controllers;

use App\Services\ProductoService;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService) {}

    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->productoService->list()
        ], 200);
    }

    public function store(StoreProductoRequest $datos)
    {
        $registroInsertado = $this->productoService->store($datos->validated());
        return response()->json([
            'success' => 'El producto se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => 'Producto encontrado',
            'data' => $this->productoService->show($id)
        ], 200);
    }

    public function update(UpdateProductoRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->productoService->update($id, $datosActualizar->validated());
        return response()->json([
            'success' => 'El producto se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }

    public function destroy(int $id)
    {
        $this->productoService->destroy($id);
        return response()->json([
            'success' => 'El producto se eliminó correctamente'
        ], 200);
    }

    public function getByNombre(string $nombre)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByNombre($nombre)
        ], 200);
    }

    public function getByFechaRegistro(string $fecha_registro)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByFechaRegistro($fecha_registro)
        ], 200);
    }

    public function getByProveedor(int $proveedor_id)
    {
        return response()->json([
            'success' => 'Productos encontrados',
            'data' => $this->productoService->getByProveedor($proveedor_id)
        ], 200);
    }
}
