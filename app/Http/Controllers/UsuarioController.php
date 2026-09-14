<?php

namespace App\Http\Controllers;

use App\Interfaces\UsuarioInterface;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    protected UsuarioInterface $usuarioRepository;

    public function __construct(UsuarioInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Mostrar todos los usuarios.
     */
    public function index()
    {
        $usuarios = $this->usuarioRepository->getAll();

        return response()->json($usuarios);
    }

    /**
     * Mostrar un usuario específico.
     */
    public function show(int $id)
    {
        $usuario = $this->usuarioRepository->getById($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($usuario);
    }

    /**
     * Crear un nuevo usuario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'rol_id' => 'required|integer|exists:roles,rol_id',
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'documento' => 'required|string|max:20|unique:usuarios,documento',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $usuario = $this->usuarioRepository->create($data);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => $usuario
        ], 201);
    }

    /**
     * Actualizar un usuario.
     */
    public function update(Request $request, int $id)
    {
        $usuario = $this->usuarioRepository->getById($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $data = $request->validate([
            'rol_id' => 'sometimes|required|integer|exists:roles,rol_id',
            'nombre' => 'sometimes|required|string|max:80',
            'apellido' => 'sometimes|required|string|max:80',
            'documento' => 'sometimes|required|string|max:20|unique:usuarios,documento,' . $id . ',usuario_id',
            'email' => 'sometimes|required|email|max:150|unique:usuarios,email,' . $id . ',usuario_id',
            'telefono' => 'nullable|string|max:20',
            'password' => 'sometimes|required|string|max:255',
            'estado' => 'sometimes|required|boolean',
        ]);

        $usuarioActualizado = $this->usuarioRepository->update($data, $id);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuarioActualizado
        ]);
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(int $id)
    {
        $usuario = $this->usuarioRepository->getById($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $this->usuarioRepository->delete($id);

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    /**
     * Buscar usuarios por rol.
     */
    public function buscarPorRol(int $idRol)
    {
        $usuarios = $this->usuarioRepository->getByRol($idRol);

        return response()->json($usuarios);
    }

    /**
     * Obtener usuarios por estado.
     */
    public function buscarPorEstado(bool $status)
    {
        $usuarios = $this->usuarioRepository->getByEstatus($status);

        return response()->json($usuarios);
    }

    /**
     * Buscar usuarios por nombre.
     */
    public function buscarPorNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
        ]);

        $usuarios = $this->usuarioRepository->getByName(
            $request->nombre
        );

        return response()->json($usuarios);
    }
}