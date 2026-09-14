<?php

namespace App\Http\Controllers;

use App\Interfaces\RolInterface;
use Illuminate\Http\Request;

class RolController extends Controller
{
    protected RolInterface $rolRepository;

    public function __construct(RolInterface $rolRepository)
    {
        $this->rolRepository = $rolRepository;
    }

    /**
     * Mostrar todos los roles.
     */
    public function index()
    {
        $roles = $this->rolRepository->getAll();

        return response()->json($roles);
    }

    /**
     * Mostrar un rol específico.
     */
    public function show(int $id)
    {
        $rol = $this->rolRepository->getById($id);

        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json($rol);
    }

    /**
     * Crear un nuevo rol.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $rol = $this->rolRepository->create($data);

        return response()->json([
            'message' => 'Rol creado correctamente',
            'rol' => $rol
        ], 201);
    }

    /**
     * Actualizar un rol.
     */
    public function update(Request $request, int $id)
    {
        $rol = $this->rolRepository->getById($id);

        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:50|unique:roles,nombre,' . $id . ',rol_id',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'sometimes|required|boolean',
        ]);

        $rolActualizado = $this->rolRepository->update($data, $id);

        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'rol' => $rolActualizado
        ]);
    }

    /**
     * Desactivar un rol.
     *
     * No se elimina físicamente porque la tabla
     * usuarios tiene una restricción ON DELETE RESTRICT.
     */
    public function destroy(int $id)
    {
        $rol = $this->rolRepository->getById($id);

        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $rol = $this->rolRepository->updateEstado($id, false);

        return response()->json([
            'message' => 'Rol desactivado correctamente',
            'rol' => $rol
        ]);
    }

    /**
     * Obtener solamente los roles activos.
     */
    public function activos()
    {
        $roles = $this->rolRepository->findActivos();

        return response()->json($roles);
    }

    /**
     * Buscar un rol por nombre.
     */
    public function buscarPorNombre(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $rol = $this->rolRepository->getByName($request->nombre);

        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json($rol);
    }

    /**
     * Activar o desactivar un rol.
     */
    public function actualizarEstado(Request $request, int $id)
    {
        $data = $request->validate([
            'estado' => 'required|boolean',
        ]);

        $rol = $this->rolRepository->updateEstado(
            $id,
            $data['estado']
        );

        if (!$rol) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Estado del rol actualizado correctamente',
            'rol' => $rol
        ]);
    }
}

