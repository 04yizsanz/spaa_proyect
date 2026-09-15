<?php

namespace App\Http\Controllers;

use App\Interfaces\FacturaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacturaController extends Controller
{
    protected FacturaRepositoryInterface $facturaRepository;

    public function __construct(FacturaRepositoryInterface $facturaRepository)
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
            'cliente_id'        => 'required|integer|exists:clientes,cliente_id',
            'empleado_id'       => 'required|integer|exists:empleados,empleado_id',
            'fecha_emision'     => 'required|date',
            'subtotal'          => 'required|numeric|min:0',
            'total'             => 'required|numeric|min:0',
            'estado'            => 'required|string',
        ]);

        $factura = $this->facturaRepository->create($validated);
        return response()->json($factura, 201);
    }

    public function show(string $codigo_factura): JsonResponse
    {
        $factura = $this->facturaRepository->find($codigo_factura);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura, 200);
    }

    public function update(Request $request, string $codigo_factura): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id'        => 'sometimes|integer|exists:clientes,cliente_id',
            'empleado_id'       => 'sometimes|integer|exists:empleados,empleado_id',
            'fecha_emision'     => 'sometimes|date',
            'subtotal'          => 'sometimes|numeric|min:0',
            'total'             => 'sometimes|numeric|min:0',
            'estado'            => 'sometimes|string',
        ]);

        $factura = $this->facturaRepository->update($codigo_factura, $validated);

        if (!$factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura, 200);
    }

    public function destroy(string $codigo_factura): JsonResponse
    {
        $deleted = $this->facturaRepository->delete($codigo_factura);

        if (!$deleted) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        return response()->json(['message' => 'Factura eliminada correctamente'], 200);
    }
}