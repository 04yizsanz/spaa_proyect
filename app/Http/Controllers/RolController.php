<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rol\StoreRolRequest;
use App\Http\Requests\Rol\UpdateRolRequest;
use App\Services\RolService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class RolController extends Controller
{
    public function __construct(
        protected RolService $rolService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->rolService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $rol = $this->rolService->getById($id);

        if (! $rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json($rol);
    }

    public function store(StoreRolRequest $request): JsonResponse
    {
        $rol = $this->rolService->create($request->validated());

        return response()->json($rol, 201);
    }

    public function update(UpdateRolRequest $request, int $id): JsonResponse
    {
        $updated = $this->rolService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['message' => 'Rol actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->rolService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['message' => 'Rol eliminado correctamente']);
    }

    public function activos(): JsonResponse
    {
        return response()->json($this->rolService->getActivos());
    }

    public function porNombre(string $nombre): JsonResponse
    {
        try {
            $rol = $this->rolService->getByNombre($nombre);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json($rol);
    }

    public function cambiarEstado(int $id): JsonResponse
    {
        $cambiado = $this->rolService->cambiarEstado($id, request()->input('estado'));

        if (! $cambiado) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}