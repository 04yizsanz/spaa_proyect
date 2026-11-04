<?php

namespace App\Interfaces;

interface UsuarioInterface extends BaseInterface
{
    public function getByRol(int $idRol);
    public function getByEstatus(bool $status);
    public function getByName(string $name);
    public function getByEmail(string $email);
}
