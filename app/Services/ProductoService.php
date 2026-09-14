<?php

namespace App\Services;

use App\Interfaces\ProductoInterface;

class ProductoService
{
    public function __construct(
        private ProductoInterface $productoRepository
    ) {}

    public function list()
    {
        return $this->productoRepository->All();
    }

    public function show(int $id)
    {
        return $this->productoRepository->find($id);
    }

    public function store(array $data)
    {
        return $this->productoRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->productoRepository->update($data, $id);
    }

    public function destroy(int $id)
    {
        return $this->productoRepository->delete($id);
    }

    public function getByNombre(string $nombre)
    {
        return $this->productoRepository->getByNombre($nombre);
    }

    public function getByFechaRegistro(string $fecha_registro)
    {
        return $this->productoRepository->getByFecha_registro($fecha_registro);
    }

    public function getByProveedor(int $proveedor_id)
    {
        return $this->productoRepository->getByProveedor($proveedor_id);
    }
}