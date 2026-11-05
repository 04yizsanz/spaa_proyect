<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacturaServicio\StoreFacturaServicioRequest;
use App\Http\Requests\FacturaServicio\UpdateFacturaServicioRequest;
use App\Services\FacturaServicioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * API del detalle de facturas (PK compuesta factura_id + servicio_id).
 *  - El precio unitario lo fija el servidor y los totales de la factura se recalculan.
 *  - Una línea de una factura ajena responde 404.
 *  - Las líneas no se eliminan: destroy siempre responde 403.
 */
class FacturaServicioController extends Controller
{
    public function __construct(
        protected FacturaServicioService $facturaServicioService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->facturaServicioService->listarPara($request->user()), 200);
    }

    public function store(StoreFacturaServicioRequest $request): JsonResponse
    {
        try {
            $registro = $this->facturaServicioService->store($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($registro, 201);
    }

    public function show(Request $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        Gate::authorize('view', $registro);

        return response()->json($registro, 200);
    }

    public function update(UpdateFacturaServicioRequest $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        Gate::authorize('update', $registro);

        $registro = $this->facturaServicioService->update($registro, $request->validated(), $request->user());

        return response()->json($registro, 200);
    }

    public function destroy(Request $request, int $factura_id, int $servicio_id): JsonResponse
    {
        $registro = $this->facturaServicioService->buscarVisible($factura_id, $servicio_id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        // FacturaServicioPolicy::delete deniega siempre: los registros financieros se conservan.
        Gate::authorize('delete', $registro);

        return response()->json(['message' => 'Los registros financieros no se eliminan.'], 403);
    }
}
