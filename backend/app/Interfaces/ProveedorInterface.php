<?php

namespace App\Interfaces;

interface ProveedorInterface extends BaseInterface

{
    public function getByContacto(string $contacto);

    public function getByEmail(string $email);

    public function getByRegistro_tributario(string $registro_tributario);
    
}