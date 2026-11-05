<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cita\StoreCitaRequest;
use App\Http\Requests\Cita\UpdateCitaRequest;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

/**
 * API de citas. Las rutas exigen auth:sanctum + cuenta activa; listar y crear
 * se autorizan en la ruta (CitaPolicy::viewAny / create) y cada cita concreta
 * aquí, con CitaPolicy. Una cita ajena responde 404.
 */
class CitaController extends Controller
{
    public function __construct(
        protected CitaService $citaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Toda la agenda con 'citas.ver_todas'; solo las propias con 'citas.propias'.
        return response()->json($this->citaService->listarPara($request->user()));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $cita = $this->citaService->buscarVisible($id, $request->user());

        if (! $cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        Gate::authorize('view', $cita);

        return response()->json($cita);
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        try {
            $cita = $this->citaService->crear($request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($cita, 201);
    }

    public function update(UpdateCitaRequest $request, int $id): JsonResponse
    {
        $cita = $this->citaService->buscarVisible($id, $request->user());

        if (! $cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        Gate::authorize('update', $cita);

        try {
            $this->citaService->actualizar($cita, $request->validated(), $request->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json(['message' => 'Cita actualizada correctamente']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $cita = $this->citaService->buscarVisible($id, $request->user());

        if (! $cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        Gate::authorize('delete', $cita);

        $this->citaService->eliminar($cita);

        return response()->json(['message' => 'Cita eliminada correctamente']);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos sin ruta registrada
    |--------------------------------------------------------------------------
    | Se conservan tal como estaban. Si en el futuro se les asigna una ruta,
    | deben filtrar por Cita::visiblePara() y autorizar con CitaPolicy.
    */

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
