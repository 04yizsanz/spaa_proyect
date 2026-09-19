<?php
namespace App\Http\Controllers;

use App\Services\MovimientoInventarioService;
use App\Http\Requests\MovimientoInventario\StoreMovimientoInventarioRequest;
use App\Http\Requests\MovimientoInventario\UpdateMovimientoInventarioRequest;

class MovimientoInventarioController extends Controller
{
    public function __construct(private MovimientoInventarioService $movimientoInventarioService) {}

    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->movimientoInventarioService->list()
        ], 200);
    }

    public function store(StoreMovimientoInventarioRequest $datos)
    {
        $registroInsertado = $this->movimientoInventarioService->store($datos->validated());
        return response()->json([
            'success' => 'El movimiento se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }

    public function show(int $id)
    {
        $dato = $this->movimientoInventarioService->show($id);

        if (!$dato) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }


        return response()->json([
            'success' => 'MovimientoInventario encontrado',
            'data' => $dato
        ], 200);
    }	
    

    public function update(UpdateMovimientoInventarioRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->movimientoInventarioService->update($id, $datosActualizar->validated());
        return response()->json([
            'success' => 'El movimiento se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }

    public function destroy(int $id)
    {
        $this->movimientoInventarioService->destroy($id);
        return response()->json([
            'success' => 'El movimiento se eliminó correctamente'
        ], 200);
    }

    public function getByFechaHora(string $fecha_hora)
    {
        return response()->json([
            'success' => 'Movimientos encontrados',
            'data' => $this->movimientoInventarioService->getByFechaHora($fecha_hora)
        ], 200);
    }

    public function getByProducto(int $producto_id)
    {
        return response()->json([
            'success' => 'Movimientos encontrados',
            'data' => $this->movimientoInventarioService->getByProducto($producto_id)
        ], 200);
    }
}

