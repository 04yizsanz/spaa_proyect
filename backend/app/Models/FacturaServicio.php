<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Líneas de factura visibles: las mismas reglas que la factura.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('facturas.ver_todas')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('facturas.propias') && $clienteId !== null) {
            return $query->whereIn(
                $this->qualifyColumn('factura_id'),
                Factura::query()->select('factura_id')->where('cliente_id', $clienteId)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}