<?php

namespace App\Http\Controllers;

use App\Http\Requests\Servicio\StoreServicioRequest;
use App\Http\Requests\Servicio\UpdateServicioRequest;
use App\Services\ServicioService;
use Illuminate\Http\JsonResponse;

class ServicioController extends Controller
{
    public function __construct(
        protected ServicioService $servicioService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->servicioService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $servicio = $this->servicioService->getById($id);

        if (! $servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json($servicio);
    }

    public function store(StoreServicioRequest $request): JsonResponse
    {
        $servicio = $this->servicioService->create($request->validated());

        return response()->json($servicio, 201);
    }

    public function update(UpdateServicioRequest $request, int $id): JsonResponse
    {
        $updated = $this->servicioService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(['message' => 'Servicio actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->servicioService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(['message' => 'Servicio eliminado correctamente']);
    }

    public function activos(): JsonResponse
    {
        return response()->json($this->servicioService->getActivos());
    }

    public function cambiarEstado(int $id): JsonResponse
    {
        $cambiado = $this->servicioService->cambiarEstado(
            $id,
            request()->boolean('activo')
        );

        if (! $cambiado) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}