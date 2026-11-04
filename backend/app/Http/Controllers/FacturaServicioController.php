<?php

namespace App\Http\Controllers;

use App\Interfaces\FacturaServicioInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacturaServicioController extends Controller
{
    protected FacturaServicioInterface $facturaServicioRepository;

    public function __construct(FacturaServicioInterface $facturaServicioRepository)
    {
        $this->facturaServicioRepository = $facturaServicioRepository;
    }

    public function index(): JsonResponse
    {
        $registros = $this->facturaServicioRepository->all();
        return response()->json($registros, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'factura_id' => 'required|integer|exists:facturas,factura_id',
            'servicio_id' => 'required|integer|exists:servicios,servicio_id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $registro = $this->facturaServicioRepository->create($validated);
        return response()->json($registro, 201);
    }

    public function show(int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioRepository->getById([
            'factura_id' => $factura_id,
            'servicio_id' => $servicio_id,
        ]);

        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json($registro, 200);
    }

    public function update(Request $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $validated = $request->validate([
            'cantidad'        => 'sometimes|integer|min:1',
            'precio_unitario' => 'sometimes|numeric|min:0',
        ]);

        $registro = $this->facturaServicioRepository->update($validated, [
            'factura_id' => $factura_id,
            'servicio_id' => $servicio_id,
        ]);

        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json($registro, 200);
    }

    public function destroy(int $factura_id, int $servicio_id): JsonResponse
    {
        $deleted = $this->facturaServicioRepository->delete([
            'factura_id' => $factura_id,
            'servicio_id' => $servicio_id,
        ]);

        if (!$deleted) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json(['message' => 'Registro eliminado correctamente'], 200);
    }
}