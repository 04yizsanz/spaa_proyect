<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

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

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'estado' => 'boolean',
    ];

    /**
     * Relación: El usuario pertenece a un Rol (Muchos a Uno)
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación Nueva: El usuario tiene un perfil de Cliente (Uno a Uno)
     * Permite usar: $usuario->cliente->preferencias
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }
}
