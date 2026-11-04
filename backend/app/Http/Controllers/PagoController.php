<?php

namespace App\Http\Controllers;


use App\Repositories\PagoRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PagoController extends Controller
{
    protected PagoRepository $pagoRepository;

    public function __construct(PagoRepository $pagoRepository)
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
            'codigo_cita'      => 'required|integer|exists:citas,codigo_cita',
            'monto'            => 'required|numeric|min:0',
            'metodo'           => 'required|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'       => 'required|date',
            'estado'           => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ]);

        $pago = $this->pagoRepository->create($validated);
        return response()->json($pago, 201);
    }

    public function show(int $pago_id): JsonResponse
    {
        $pago = $this->pagoRepository->find($pago_id);

        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json($pago, 200);
    }

    public function update(Request $request, int $pago_id): JsonResponse
    {
        $validated = $request->validate([
            'codigo_cita'      => 'sometimes|integer|exists:citas,codigo_cita',
            'monto'            => 'sometimes|numeric|min:0',
            'metodo'           => 'sometimes|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'       => 'sometimes|date',
            'estado'           => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ]);

        $pago = $this->pagoRepository->update($validated, $pago_id);

        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json($pago, 200);
    }

    public function destroy(int $pago_id): JsonResponse
    {
        $deleted = $this->pagoRepository->delete($pago_id);

        if (!$deleted) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        return response()->json(['message' => 'Pago eliminado correctamente'], 200);
    }
}