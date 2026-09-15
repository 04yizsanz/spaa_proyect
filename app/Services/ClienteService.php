<?php

namespace App\Services;

use App\Interfaces\ClienteInterface;

class ClienteService
{
    public function __construct(
        protected ClienteInterface $clienteRepository
    ) {}

    public function getAll()
    {
        return $this->clienteRepository->all();
    }

    public function getById(int $id)
    {
        return $this->clienteRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->clienteRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $cliente = $this->clienteRepository->find($id);

        if (! $cliente) {
            return null;
        }

        return $this->clienteRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $cliente = $this->clienteRepository->find($id);

        if (! $cliente) {
            return null;
        }

        return $this->clienteRepository->delete($id);
    }

    public function getByNombre(string $nombre)
    {
        return $this->clienteRepository->getByName($nombre);
    }

    public function getByApellido(string $apellido)
    {
        return $this->clienteRepository->getByLastname($apellido);
    }

    public function getByDocumento(string $documento)
    {
        return $this->clienteRepository->getByDocument($documento);
    }
}