<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaServicio extends Model
{
    use HasFactory;

    protected $table = 'factura_servicio';

    public $incrementing = false;
    protected $primaryKey = null;
    protected $fillable = [
        'factura_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'factura_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }
}