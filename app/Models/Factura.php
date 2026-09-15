<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = "facturas";
    protected $primaryKey = "factura_id";

    protected $fillable = [
        'monto',
        'metodo',
        'fecha_hora',
        'estado',
        'codigo_cita',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'codigo_cita', 'codigo_cita');
    }
}