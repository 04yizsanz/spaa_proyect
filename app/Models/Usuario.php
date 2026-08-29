<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id',
        'nombre',
        'email',
        'password',
        'telefono',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
