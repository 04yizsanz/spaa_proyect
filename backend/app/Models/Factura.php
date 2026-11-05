<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro financiero: no usa SoftDeletes y no se elimina (ver FacturaPolicy).
 * subtotal y total los calcula el servidor (FacturaService::recalcularTotales).
 */
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

    /**
     * Facturas que el usuario puede consultar: todas con 'facturas.ver_todas';
     * las propias con 'facturas.propias'; ninguna en otro caso.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('facturas.ver_todas')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('facturas.propias') && $clienteId !== null) {
            return $query->where($this->qualifyColumn('cliente_id'), $clienteId);
        }

        return $query->whereRaw('1 = 0');
    }
}