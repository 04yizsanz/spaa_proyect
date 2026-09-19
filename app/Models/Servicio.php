<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'servicios';
    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'servicio_id';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'duracion_min',
        'precio',
        'descripcion',
        'activo',
    ];

    /**
     * Conversión de tipos.
     */
    protected $casts = [
        'duracion_min' => 'integer',
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Citas que solicitan este servicio.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'servicio_id', 'servicio_id');
    }
}