<?php

namespace App\Http\Controllers;

use App\Interfaces\FacturaInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacturaController extends Controller
{
    protected FacturaInterface $facturaRepository;

    public function __construct(FacturaInterface $facturaRepository)
    {
        $this->facturaRepository = $facturaRepository;
    }

    public function index(): JsonResponse
    {
        $facturas = $this->facturaRepository->all();
        return response()->json($facturas, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,cliente_id',
            'fecha_hora' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
        ]);

        $factura = $this->facturaRepository->create($validated);
        return response()->json($factura, 201);
    }

    public function show(int $factura_id): JsonResponse
    {
        $factura = $this->facturaRepository->find($factura_id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura, 200);
    }

    public function update(Request $request, int $factura_id): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'sometimes|integer|exists:clientes,cliente_id',
            'fecha_hora' => 'sometimes|date',
            'subtotal' => 'sometimes|numeric|min:0',
            'impuestos' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
        ]);

        $factura = $this->facturaRepository->update($validated, $factura_id);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura, 200);
    }

    public function destroy(int $factura_id): JsonResponse
    {
        $deleted = $this->facturaRepository->delete($factura_id);

        if (!$deleted) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json(['message' => 'Factura eliminada correctamente'], 200);
    }
}