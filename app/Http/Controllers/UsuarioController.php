<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class UsuarioController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->usuarioService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $usuario = $this->usuarioService->getById($id);

        if (! $usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        try {
            $usuario = $this->usuarioService->create($request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($usuario, 201);
    }

    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->usuarioService->update($id, $request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        if (! $updated) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->usuarioService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function porRol(int $rolId): JsonResponse
    {
        return response()->json($this->usuarioService->getByRol($rolId));
    }

    public function cambiarEstado(int $id): JsonResponse
    {
        $cambiado = $this->usuarioService->cambiarEstado($id, request()->input('estado'));

        if (! $cambiado) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}