<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Cita extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'citas';
    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'citas_id';
    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'codigo_cita',
        'fecha',
        'hora',
        'estado',
        'cliente_id',
        'empleado_id',
        'servicio_id',
    ];

    /**
     * Conversión de tipos.
     */
    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Cliente que agenda la cita.
     * Nota: asume que el modelo Cliente usa 'id' como PK (según TCliente.ClienteId).
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Empleado asignado a la cita.
     */
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }

    /**
     * Servicio solicitado en la cita.
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }

    /**
     * Citas que el usuario puede consultar: toda la agenda con
     * 'citas.ver_todas'; solo las suyas con 'citas.propias'; ninguna en otro caso.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('citas.ver_todas')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('citas.propias') && $clienteId !== null) {
            return $query->where($this->qualifyColumn('cliente_id'), $clienteId);
        }

        return $query->whereRaw('1 = 0');
    }
}