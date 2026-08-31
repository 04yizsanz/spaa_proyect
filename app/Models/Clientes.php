<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // Vincula explícitamente con tu tabla de la migración
    protected $table = 'usuarios';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'documento',
        'email',
        'telefono',
        'password',
        'estado',
    ];

    // Oculta la contraseña en las respuestas JSON o arreglos
    protected $hidden = [
        'password',
    ];

    // Encripta la contraseña automáticamente y pasa el estado a booleano
    protected $casts = [
        'password' => 'hashed',
        'estado' => 'boolean',
    ];

    /**
     * Relación: El usuario pertenece a un Rol (Muchos a Uno)
     * Permite usar: $usuario->rol->nombre
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
