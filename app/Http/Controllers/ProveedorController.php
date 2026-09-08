<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProveedorService;
use App\Http\Requests\Proveedor\StoreProveedorRequest;
use App\Http\Requests\Proveedor\UpdateProveedorRequest;

class ProveedorController extends Controller
{
    public function __construct(private ProveedorService $proveedorService)
    {
    }

    public function index()
    {
        return response()->json([
            'success' => 'se listaron correctamente',
            'data' => $this->proveedorService->list()
        ]);
    }

    public function store(StoreProveedorRequest $datos)
    {
        $registroInsertado = $this->proveedorService->store($datos->validated());

        return response()->json([
            'success' => 'proveedor se creó correctamente',
            'data' => $registroInsertado
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => 'se encontró el proveedor',
            'data' => $this->proveedorService->show($id)
        ]);
    }

    public function update(UpdateProveedorRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->proveedorService->update($id, $datosActualizar->validated());

        return response()->json([
            'success' => 'proveedor se actualizó correctamente',
            'data' => $registroActualizado
        ]);
    }

    public function destroy(int $id)
    {
        $this->proveedorService->destroy($id);

        return response()->json([
            'success' => 'proveedor se eliminó correctamente'
        ]);
    }
}