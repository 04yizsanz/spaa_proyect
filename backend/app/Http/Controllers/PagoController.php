<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pago\StorePagoRequest;
use App\Http\Requests\Pago\UpdatePagoRequest;
use App\Services\PagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * API de pagos.
 *  - Listar y registrar se autorizan en la ruta (PagoPolicy::viewAny / create).
 *  - Un pago ajeno responde 404.
 *  - Solo los pagos 'pendiente' se modifican; aprobado o rechazado → 409.
 *  - Los pagos no se eliminan: destroy siempre responde 403.
 */
class PagoController extends Controller
{
    public function __construct(
        protected PagoService $pagoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->pagoService->listarPara($request->user()), 200);
    }

    public function store(StorePagoRequest $request): JsonResponse
    {
        $pago = $this->pagoService->store($request->validated(), $request->user());

        return response()->json($pago, 201);
    }

    public function show(Request $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        Gate::authorize('view', $pago);

        return response()->json($pago, 200);
    }

    public function update(UpdatePagoRequest $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        Gate::authorize('update', $pago);

        $pago = $this->pagoService->update($pago, $request->validated(), $request->user());

        return response()->json($pago, 200);
    }

    public function destroy(Request $request, int $pago_id): JsonResponse
    {
        $pago = $this->pagoService->buscarVisible($pago_id, $request->user());

        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        // PagoPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $pago);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
}
