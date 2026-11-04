<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cita\StoreCitaRequest;
use App\Http\Requests\Cita\UpdateCitaRequest;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class CitaController extends Controller
{
    public function __construct(
        protected CitaService $citaService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->citaService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $cita = $this->citaService->getById($id);

        if (! $cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita);
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        try {
            $cita = $this->citaService->create($request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($cita, 201);
    }

    public function update(UpdateCitaRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->citaService->update($id, $request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        if (! $updated) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json(['message' => 'Cita actualizada correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->citaService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json(['message' => 'Cita eliminada correctamente']);
    }

    public function porCliente(int $clienteId): JsonResponse
    {
        return response()->json($this->citaService->getByCliente($clienteId));
    }

    public function porEmpleado(int $empleadoId): JsonResponse
    {
        return response()->json($this->citaService->getByEmpleado($empleadoId));
    }

    public function porEstado(string $estado): JsonResponse
    {
        return response()->json($this->citaService->getByEstado($estado));
    }

    public function porRangoFechas(): JsonResponse
    {
        $fechaInicio = request()->query('fecha_inicio');
        $fechaFin = request()->query('fecha_fin');

        return response()->json(
            $this->citaService->getByRangoFechas($fechaInicio, $fechaFin)
        );
    }

    public function cambiarEstado(int $id): JsonResponse
    {
        $cambiado = $this->citaService->cambiarEstado($id, request()->input('estado'));

        if (! $cambiado) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}