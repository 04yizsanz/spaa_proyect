<?php

namespace App\Services;

use App\Interfaces\Movimiento_InventarioInterface;

class MovimientoInventarioService
{
    public function __construct(
        private Movimiento_InventarioInterface $movimientoInventarioRepository
    ) {}

    public function list()
    {
        return $this->movimientoInventarioRepository->All();
    }

    public function show(int $id)
    {
        return $this->movimientoInventarioRepository->find($id);
    }

    public function store(array $data)
    {
        return $this->movimientoInventarioRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->movimientoInventarioRepository->update($data, $id);
    }

    public function destroy(int $id)
    {
        return $this->movimientoInventarioRepository->delete($id);
    }

    public function getByFechaHora(string $fecha_hora)
    {
        return $this->movimientoInventarioRepository->getByFecha_Hora($fecha_hora);
    }

    public function getByProducto(int $producto_id)
    {
        return $this->movimientoInventarioRepository->getByProducto($producto_id);
    }
}