<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaServicio extends Model
{
    use HasFactory;

    protected $table = "facturaServicio";

    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

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

    public function facturaServicio()
    {
        return $this->belongsTo(FacturaServicio::class, 'facturaServicio_id', 'factura_id');
    }
}