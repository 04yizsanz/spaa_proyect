<?php
namespace App\Http\Controllers;

use App\Services\ProveedorService;
use App\Http\Requests\Proveedor\StoreProveedorRequest;
use App\Http\Requests\Proveedor\UpdateProveedorRequest;

class ProveedorController extends Controller
{
    public function __construct(private ProveedorService $proveedorService) {}

    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->proveedorService->list()
        ], 200);
    }

    public function store(StoreProveedorRequest $datos)
    {
        $registroInsertado = $this->proveedorService->store($datos->validated());
        return response()->json([
            'success' => 'El proveedor se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $this->proveedorService->show($id)
        ], 200);
    }

    public function update(UpdateProveedorRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->proveedorService->update($id, $datosActualizar->validated());
        return response()->json([
            'success' => 'El proveedor se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }

    public function destroy(int $id)
    {
        $this->proveedorService->destroy($id);
        return response()->json([
            'success' => 'El proveedor se eliminó correctamente'
        ], 200);
    }

    public function getByContacto(string $contacto)
    {
        return response()->json([
            'success' => 'Proveedores encontrados',
            'data' => $this->proveedorService->getByContacto($contacto)
        ], 200);
    }

    public function getByEmail(string $email)
    {
        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $this->proveedorService->getByEmail($email)
        ], 200);
    }

    public function getByRegistroTributario(string $registro_tributario)
    {
        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $this->proveedorService->getByRegistroTributario($registro_tributario)
        ], 200);
    }
}
