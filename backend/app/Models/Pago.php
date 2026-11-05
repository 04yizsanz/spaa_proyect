<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro financiero: no usa SoftDeletes y no se elimina (ver PagoPolicy).
 */
class Pago extends Model
{
    use HasFactory;

    protected $table = "pagos";
    protected $primaryKey = "pago_id";

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

    /**
     * Pagos que el usuario puede consultar: todos con 'pagos.ver_todos';
     * los de sus propias citas con 'pagos.propios'; ninguno en otro caso.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('pagos.ver_todos')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('pagos.propios') && $clienteId !== null) {
            return $query->whereIn(
                $this->qualifyColumn('codigo_cita'),
                Cita::withTrashed()->select('codigo_cita')->where('cliente_id', $clienteId)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}