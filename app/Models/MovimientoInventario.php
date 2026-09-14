<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientoinventarios';    

    protected $fillable = [
        'tipo',
        'cantidad',
        'fecha_hora',
        'motivo',
        'producto_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'cantidad' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class,);
    }
}