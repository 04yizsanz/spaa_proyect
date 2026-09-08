<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductoService;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService)
    {
    }

    public function index()
    {
        return response()->json([
            'success' => 'se listaron correctamente',
            'data' => $this->productoService->list()
        ]);
    }

    public function store(StoreProductoRequest $datos)
    {
        $registroInsertado = $this->productoService->store($datos->validated());

        return response()->json([
            'success' => 'producto se creó correctamente',
            'data' => $registroInsertado
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => 'se encontró el producto',
            'data' => $this->productoService->show($id)
        ]);
    }

    public function update(UpdateProductoRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->productoService->update($id, $datosActualizar->validated());

        return response()->json([
            'success' => 'producto se actualizó correctamente',
            'data' => $registroActualizado
        ]);
    }

    public function destroy(int $id)
    {
        $this->productoService->destroy($id);

        return response()->json([
            'success' => 'producto se eliminó correctamente'
        ]);
    }
}