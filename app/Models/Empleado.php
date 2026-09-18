<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; //agg a todos los modelos


class Empleado extends Model
{
    use HasFactory;

    use SoftDeletes; //agg a todos los modelos

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'empleados';

    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'empleado_id';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'correo',
        'telefono',
        'rol',
        'salario',
        'fecha_contratacion',
        'disponibilidad',
    ];

    /**
     * Conversión de tipos.
     */
    protected $casts = [
        'salario' => 'decimal:2',
        'fecha_contratacion' => 'date',
        'disponibilidad' => 'boolean',
    ];

    /**
     * Citas asignadas a este empleado.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'empleado_id', 'empleado_id');
    }
}