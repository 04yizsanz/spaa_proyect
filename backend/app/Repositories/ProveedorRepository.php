<?php

namespace App\Repositories;

use App\Interfaces\ProveedorInterface;
use App\Models\Proveedor;

class ProveedorRepository extends BaseRepository implements ProveedorInterface
{
    public function __construct(Proveedor $proveedor)
    {
        parent::__construct($proveedor);
    }

    public function getByContacto(string $contacto)
    {
        $proveedores = $this->model->where('contacto', $contacto)
                                    ->get();

        if ($proveedores->isEmpty()) {
            return null;
        }

        return $proveedores;
    }

    public function getByEmail(string $email)
    {
        $proveedores = $this->model->where('email', $email)
                                    ->get();

        if ($proveedores->isEmpty()) {
            return null;
        }

        return $proveedores;
    }

    public function getByRegistro_tributario(string $registro_tributario)
    {
        $proveedor = $this->model->where('registro_tributario', $registro_tributario)
                                  ->first();

        if (!$proveedor) {
            return null;
        }

        return $proveedor;
    }
}