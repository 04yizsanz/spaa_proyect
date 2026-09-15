<?php

namespace App\Services\Rol;

use App\Interfaces\RolInterface;

class StoreRolService
{
    protected RolInterface $rolRepository;

    public function __construct(RolInterface $rolRepository)
    {
        $this->rolRepository = $rolRepository;
    }

    public function execute(array $data)
    {
        return $this->rolRepository->create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'estado' => $data['estado'],
        ]);
    }
}
