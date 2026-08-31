<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaServicio extends Model
{
    use HasFactory;

    protected $table = "factura_servicio";
    protected $fillable = [
        'factura_id',
        'servicio_id',
        'cantidad',
        'precio_unitario'
    ];
}
