<?php

namespace App\Repositories;

use App\Interfaces\ProductoInterface;
use App\Models\Producto;

class ProductoRepository extends BaseRepository implements ProductoInterface
{
    public function __construct(Producto $producto)
    {
        parent::__construct($producto);
    }

}