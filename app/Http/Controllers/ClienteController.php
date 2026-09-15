<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->clienteService->getAll());
    }

    public function show(int $id): JsonResponse
    {
        $cliente = $this->clienteService->getById($id);

        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = $this->clienteService->create($request->validated());

        return response()->json($cliente, 201);
    }

    public function update(UpdateClienteRequest $request, int $id): JsonResponse
    {
        $updated = $this->clienteService->update($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json(['message' => 'Cliente actualizado correctamente']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->clienteService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    public function porNombre(string $nombre): JsonResponse
    {
        return response()->json($this->clienteService->getByNombre($nombre));
    }

    public function porApellido(string $apellido): JsonResponse
    {
        return response()->json($this->clienteService->getByApellido($apellido));
    }

    public function porDocumento(string $documento): JsonResponse
    {
        $cliente = $this->clienteService->getByDocumento($documento);

        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }
}