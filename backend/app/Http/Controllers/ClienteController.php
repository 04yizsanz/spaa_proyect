<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * API de clientes. Listar exige 'clientes.ver' (en la ruta); un Cliente solo
 * puede consultar su propio perfil y cualquier perfil ajeno responde 404.
 */
class ClienteController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->clienteService->listarPara($request->user()));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $cliente = $this->clienteService->buscarVisible($id, $request->user());

        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Gate::authorize('view', $cliente);

        return response()->json($cliente);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = $this->clienteService->create($request->validated());

        return response()->json($cliente, 201);
    }

    public function update(UpdateClienteRequest $request, int $cliente): JsonResponse
    {
    $registro = $this->clienteService->buscarVisible($cliente, $request->user());

    if (! $registro) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    Gate::authorize('update', $registro);

    $updated = $this->clienteService->update($cliente, $request->validated());

    if (! $updated) {
        return response()->json(['message' => 'Cliente no encontrado'], 404);
    }

    return response()->json(['message' => 'Cliente actualizado correctamente']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $registro = $this->clienteService->buscarVisible($id, $request->user());

        if (! $registro) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Gate::authorize('delete', $registro);

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