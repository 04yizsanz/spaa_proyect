<?php
namespace App\Http\Controllers;

use App\Services\ProveedorService;
use App\Http\Requests\Proveedor\StoreProveedorRequest;
use App\Http\Requests\Proveedor\UpdateProveedorRequest;

class ProveedorController extends Controller
{
    public function __construct(private ProveedorService $proveedorService)
    {
    }
//index, es para listar todos los registros 
    public function index()
    {
        return response()->json([
            'success' => 'Se listaron correctamente',
            'data' => $this->proveedorService->list()
        ], 200);
    }
//el store es para incertar o crear un nuevo registro 
    public function store(StoreProveedorRequest $datos)
    {
        $registroInsertado = $this->proveedorService->store($datos->validated());
        return response()->json([
            'success' => 'El proveedor se creó correctamente',
            'data' => $registroInsertado
        ], 201);
    }
//es para trer un solo registro por medio del id 
    public function show(int $id)
    {

        $dato = $this->proveedorService->show($id);

        if (!$dato) {
            return response()->json([
                'not_found' => 'Not found',
                'message' => 'No se encontró el registro'
            ], 404);
        }


        return response()->json([
            'success' => 'Proveedor encontrado',
            'data' => $dato
        ], 200);
    }
//el update sirve para eliminar un registro 
    public function update(UpdateProveedorRequest $datosActualizar, int $id)
    {
        $registroActualizado = $this->proveedorService->update($id, $datosActualizar->validated());
        return response()->json([
            'success' => 'El proveedor se actualizó correctamente',
            'data' => $registroActualizado
        ], 200);
    }
//destroy sirve para eliminar un registro
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
