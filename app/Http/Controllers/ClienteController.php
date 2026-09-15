<?php

namespace App\Http\Controllers;

use App\Interfaces\ClienteInterface;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected ClienteInterface $clienteRepository;

    public function __construct(ClienteInterface $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }

    /**
     * Mostrar todos los clientes.
     */
    public function index()
    {
        $clientes = $this->clienteRepository->getAll();

        return response()->json($clientes);
    }

    /**
     * Mostrar un cliente específico.
     */
    public function show(int $id)
    {
        $cliente = $this->clienteRepository->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json($cliente);
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,usuario_id|unique:clientes,usuario_id',
            'fecha_nacimiento' => 'nullable|date',
            'preferencias' => 'nullable|string',
        ]);

        $cliente = $this->clienteRepository->create($data);

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'cliente' => $cliente
        ], 201);
    }

    /**
     * Actualizar un cliente.
     */
    public function update(Request $request, int $id)
    {
        $cliente = $this->clienteRepository->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $data = $request->validate([
            'usuario_id' => 'sometimes|required|integer|exists:usuarios,usuario_id|unique:clientes,usuario_id,' . $id . ',cliente_id',
            'fecha_nacimiento' => 'nullable|date',
            'preferencias' => 'nullable|string',
        ]);

        $clienteActualizado = $this->clienteRepository->update($data, $id);

        return response()->json([
            'message' => 'Cliente actualizado correctamente',
            'cliente' => $clienteActualizado
        ]);
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy(int $id)
    {
        $cliente = $this->clienteRepository->getById($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $this->clienteRepository->delete($id);

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ]);
    }

    /**
     * Buscar clientes por nombre.
     */
    public function buscarPorNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
        ]);

        $clientes = $this->clienteRepository->getByName(
            $request->nombre
        );

        return response()->json($clientes);
    }

    /**
     * Buscar clientes por apellido.
     */
    public function buscarPorApellido(Request $request)
    {
        $request->validate([
            'apellido' => 'required|string|max:80',
        ]);

        $clientes = $this->clienteRepository->getByLastname(
            $request->apellido
        );

        return response()->json($clientes);
    }

    /**
     * Buscar un cliente por documento.
     */
    public function buscarPorDocumento(Request $request)
    {
        $request->validate([
            'documento' => 'required|string|max:20',
        ]);

        $cliente = $this->clienteRepository->getByDocument(
            $request->documento
        );

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json($cliente);
    }
}