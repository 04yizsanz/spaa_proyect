<?php

namespace App\Http\Controllers;

use App\Http\Requests\Factura\StoreFacturaRequest;
use App\Http\Requests\Factura\UpdateFacturaRequest;
use App\Services\FacturaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * API de facturas.
 *  - Listar y crear se autorizan en la ruta (FacturaPolicy::viewAny / create).
 *  - Cada factura se autoriza aquí; una factura ajena responde 404.
 *  - subtotal y total los calcula el servidor (FacturaService).
 *  - Las facturas no se eliminan: destroy siempre responde 403.
 */
class FacturaController extends Controller
{
    public function __construct(
        protected FacturaService $facturaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->facturaService->listarPara($request->user()), 200);
    }

    public function store(StoreFacturaRequest $request): JsonResponse
    {
        $factura = $this->facturaService->store($request->validated(), $request->user());

        return response()->json($factura, 201);
    }

    public function show(Request $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        Gate::authorize('view', $factura);

        return response()->json($factura, 200);
    }

    public function update(UpdateFacturaRequest $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        Gate::authorize('update', $factura);

        $factura = $this->facturaService->update($factura, $request->validated(), $request->user());

        return response()->json($factura, 200);
    }

    public function destroy(Request $request, int $factura_id): JsonResponse
    {
        $factura = $this->facturaService->buscarVisible($factura_id, $request->user());

        if (! $factura) {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }

        // FacturaPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $factura);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
}
