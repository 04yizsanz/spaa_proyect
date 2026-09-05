<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'cita';

    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'codigo_cita';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
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
}