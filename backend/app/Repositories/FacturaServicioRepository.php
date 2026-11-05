<?php

namespace App\Repositories;

use App\Interfaces\FacturaServicioInterface;
use App\Models\FacturaServicio;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

class FacturaServicioRepository extends BaseRepository implements FacturaServicioInterface
{
    public function __construct(FacturaServicio $model)
    {
        parent::__construct($model);
    }

    public function getById(mixed $id)
    {
        return $this->model
            ->where('factura_id', $id['factura_id'])
            ->where('servicio_id', $id['servicio_id'])
            ->first();
    }

    public function update(array $data, mixed $id)
    {
        $registro = $this->getById($id);

        if (! $registro) {
            return null;
        }

        $this->model
            ->where('factura_id', $id['factura_id'])
            ->where('servicio_id', $id['servicio_id'])
            ->update($data);

        return $this->getById($id);
    }

    public function delete(mixed $id)
    {
        $registro = $this->getById($id);

        if (! $registro) {
            return null;
        }

        return $this->model
            ->where('factura_id', $id['factura_id'])
            ->where('servicio_id', $id['servicio_id'])
            ->delete();
    }

    public function getByFactura(int $factura_id)
    {
        $registros = $this->model->where('factura_id', $factura_id)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByServicio(int $servicio_id)
    {
        $registros = $this->model->where('servicio_id', $servicio_id)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByCantidad(int $cantidad)
    {
        $registros = $this->model->where('cantidad', $cantidad)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function getByPrecioUnitario(float $precio_unitario)
    {
        $registros = $this->model->where('precio_unitario', $precio_unitario)->get();

        if ($registros->isEmpty()) {
            return null;
        }

        return $registros;
    }

    public function visiblesPara(Usuario $usuario): Collection
    {
        return $this->model->newQuery()->visiblePara($usuario)->get();
    }

    public function buscarVisible(int $facturaId, int $servicioId, Usuario $usuario): ?FacturaServicio
    {
        return $this->model->newQuery()
            ->visiblePara($usuario)
            ->where('factura_id', $facturaId)
            ->where('servicio_id', $servicioId)
            ->first();
    }

    public function existeLinea(int $facturaId, int $servicioId): bool
    {
        return $this->model->newQuery()
            ->where('factura_id', $facturaId)
            ->where('servicio_id', $servicioId)
            ->exists();
    }
}