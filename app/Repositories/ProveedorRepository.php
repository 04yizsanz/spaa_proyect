<?php

namespace App\Repositories;

use App\Interfaces\ProveedorInterface;
use App\Models\Proveedor;

class ProveedorRepository extends BaseRepository implements ProveedorInterface
{
    public function __construct(Proveedor $proveedor)
    {
        parent::__construct($proveedor);
    }
}