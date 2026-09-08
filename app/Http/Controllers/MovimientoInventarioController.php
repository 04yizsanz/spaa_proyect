<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MovimientoInventarioService;
use App\Http\Requests\MovimientoInventario\StoreMovimientoInventarioRequest;
use App\Http\Requests\MovimientoInventario\UpdateMovimientoInventarioRequest;

class MovimientoInventarioController extends Controller
{
    public function __construct(private MovimientoInventarioService $movimientoInventarioService)
    {
    }

    public function index()
    {
        return response()->json([
            'success' => 'se listaron correctamente',
            'data' => $this->movimientoInventarioService->list()
        ]);
    }

    public function store(StoreMovimientoInventarioRequest $datos)
    {
        $registroInsertado = $this->movimientoInventarioService->store($datos->validated());

        return response()->json([
            'success' => 'movimiento se creó correctamente',
            'data' => $registroInsertado
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => 'se encontró el movimiento',
            'data' => $this->movimientoInventarioService->show($id)
        ]);
    }

    public function update(UpdateMovimientoInventarioRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->movimientoInventarioService->update($id, $datosActualizar->validated());

        return response()->json([
            'success' => 'movimiento se actualizó correctamente',
            'data' => $registroActualizado
        ]);
    }

    public function destroy(int $id)
    {
        $this->movimientoInventarioService->destroy($id);

        return response()->json([
            'success' => 'movimiento se eliminó correctamente'
        ]);
    }
}