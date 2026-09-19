<?php

namespace App\Interfaces;

interface ProductoInterface extends BaseInterface

{
    public function getByNombre(string $nombre);

    public function getByFecha_registro(string $fecha_registro);

   public function getByProveedor(int $proveedor_id);
    
}
