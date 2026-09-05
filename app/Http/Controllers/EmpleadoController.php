<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Services\EmpleadoService;
use Illuminate\Http\JsonResponse;

class EmpleadoController extends Controller
{
    public function __construct(
        protected EmpleadoService $empleadoService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->empleadoService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $empleado = $this->empleadoService->getById($id);

        if (! $empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json($empleado);
    }

    public function store(StoreEmpleadoRequest $request): JsonResponse
    {
        $empleado = $this->empleadoService->create($request->validated());

        return response()->json($empleado, 201);
    }

    public function update(UpdateEmpleadoRequest $request, int $id): JsonResponse
    {
        $updated = $this->empleadoService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json(['message' => 'Empleado actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->empleadoService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }

    public function disponibles(): JsonResponse
    {
        return response()->json($this->empleadoService->getDisponibles());
    }

    public function porRol(string $rol): JsonResponse
    {
        return response()->json($this->empleadoService->getByRol($rol));
    }

    public function cambiarDisponibilidad(int $id): JsonResponse
    {
        $cambiado = $this->empleadoService->cambiarDisponibilidad(
            $id,
            request()->boolean('disponibilidad')
        );

        if (! $cambiado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json(['message' => 'Disponibilidad actualizada correctamente']);
    }
}