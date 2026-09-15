<?php

namespace App\Http\Controllers;

use App\Interfaces\PagoRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PagoController extends Controller
{
    protected PagoRepositoryInterface $pagoRepository;

    public function __construct(PagoRepositoryInterface $pagoRepository)
    {
        $this->pagoRepository = $pagoRepository;
    }

    public function index(): JsonResponse
    {
        $pagos = $this->pagoRepository->all();
        return response()->json($pagos, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_factura'   => 'required|exists:facturas,codigo_factura',
            'monto'            => 'required|numeric|min:0',
            'metodo_pago'      => 'required|string',
            'fecha_pago'       => 'required|date',
        ]);

        $pago = $this->pagoRepository->create($validated);
        return response()->json($pago, 201);
    }

    public function show(string $codigo_pago): JsonResponse
    {
        $pago = $this->pagoRepository->find($codigo_pago);

        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json($pago, 200);
    }

    public function update(Request $request, string $codigo_pago): JsonResponse
    {
        $validated = $request->validate([
            'codigo_factura'   => 'sometimes|exists:facturas,codigo_factura',
            'monto'            => 'sometimes|numeric|min:0',
            'metodo_pago'      => 'sometimes|string',
            'fecha_pago'       => 'sometimes|date',
        ]);

        $pago = $this->pagoRepository->update($codigo_pago, $validated);

        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json($pago, 200);
    }

    public function destroy(string $codigo_pago): JsonResponse
    {
        $deleted = $this->pagoRepository->delete($codigo_pago);

        if (!$deleted) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json(['message' => 'Pago eliminado correctamente'], 200);
    }
}