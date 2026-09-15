<?php

namespace App\Http\Controllers;

use App\Interfaces\FacturaServicioRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacturaServicioController extends Controller
{
    protected FacturaServicioRepositoryInterface $facturaServicioRepository;

    public function __construct(FacturaServicioRepositoryInterface $facturaServicioRepository)
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
            'codigo_factura'  => 'required|exists:facturas,codigo_factura',
            'servicio_id'     => 'required|integer|exists:servicios,servicio_id',
            'cantidad'        => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $registro = $this->facturaServicioRepository->create($validated);
        return response()->json($registro, 201);
    }

    public function show(string $codigo_factura_servicio): JsonResponse
    {
        $registro = $this->facturaServicioRepository->find($codigo_factura_servicio);

        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json($registro, 200);
    }

    public function update(Request $request, string $codigo_factura_servicio): JsonResponse
    {
        $validated = $request->validate([
            'cantidad'        => 'sometimes|integer|min:1',
            'precio_unitario' => 'sometimes|numeric|min:0',
        ]);

        $registro = $this->facturaServicioRepository->update($codigo_factura_servicio, $validated);

        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json($registro, 200);
    }

    public function destroy(string $codigo_factura_servicio): JsonResponse
    {
        $deleted = $this->facturaServicioRepository->delete($codigo_factura_servicio);

        if (!$deleted) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        return response()->json(['message' => 'Registro eliminado correctamente'], 200);
    }
}