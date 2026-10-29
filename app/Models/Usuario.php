<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'usuario_id';

    // Decirle a Laravel qué columna usar para autenticación
    public function getAuthIdentifierName(): string
    {
        return 'usuario_id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->usuario_id;
    }

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
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'usuario_id', 'usuario_id');
    }
}
