<?php

namespace App\Http\Controllers;

use App\Http\Requests\Empleado\StoreEmpleadoRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoRequest;
use App\Models\Usuario;
use App\Services\EmpleadoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API de empleados. Consultar exige 'empleados.ver' y gestionar
 * 'empleados.gestionar' (ambos en la ruta, EmpleadoPolicy). Los datos de
 * contacto y el salario solo se incluyen con 'empleados.ver_contacto' y
 * 'empleados.ver_salario'.
 */
class EmpleadoController extends Controller
{
    public function __construct(
        protected EmpleadoService $empleadoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $empleados = $this->empleadoService->getAll();

        return response()->json($empleados->makeHidden($this->camposOcultos($request->user())));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $empleado = $this->empleadoService->getById($id);

        if (! $empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        return response()->json($empleado->makeHidden($this->camposOcultos($request->user())));
    }

    /**
     * Campos que el usuario no puede ver según sus permisos.
     *
     * @return array<int, string>
     */
    private function camposOcultos(Usuario $usuario): array
    {
        $ocultos = [];

        if (! $usuario->tienePermiso('empleados.ver_contacto')) {
            array_push($ocultos, 'documento', 'correo', 'telefono');
        }

        if (! $usuario->tienePermiso('empleados.ver_salario')) {
            $ocultos[] = 'salario';
        }

        return $ocultos;
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