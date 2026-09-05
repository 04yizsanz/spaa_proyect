<?php

namespace App\Repositories;

use App\Interfaces\ProductoInterface;
use App\Models\Producto;

class ProductoRepository extends BaseRepository implements ProductoInterface
{
    public function __construct(Producto $producto)
    {
        parent::__construct($producto);
    }

    public function getByNombre(string $nombre)
    {
        $productos = $this->model->where('nombre', $nombre)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }

    public function getByFecha_registro(string $fecha_registro)
    {
        $productos = $this->model->where('fecha_registro', $fecha_registro)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }

    public function getByProveedor(int $proveedor_id)
    {
        $productos = $this->model->where('proveedor_id', $proveedor_id)
                                  ->get();

        if ($productos->isEmpty()) {
            return null;
        }

        return $productos;
    }
}