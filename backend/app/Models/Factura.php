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
        'fecha_hora',
        'subtotal',
        'impuestos',
        'total',
        'pdf_url',
        'cliente_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }

    public function servicios()
    {
        return $this->hasMany(FacturaServicio::class, 'factura_id', 'factura_id');
    }
}