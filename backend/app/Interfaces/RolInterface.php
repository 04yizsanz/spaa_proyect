<?php

namespace App\Interfaces;

interface RolInterface extends BaseInterface
{
    public function findActivos();

    public function getByName(string $nombre);

    public function updateEstado(int $id, bool $estado);
}