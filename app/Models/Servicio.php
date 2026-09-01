<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'servicio';

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