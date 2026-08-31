<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    // Conecta explícitamente con el nombre de tu tabla en la migración
    protected $table = 'roles';

    // Permite la asignación masiva de tus columnas de forma segura
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Convierte automáticamente el campo estado a tipo booleano (true/false) en PHP
    protected $casts = [
        'estado' => 'boolean',
    ];
}
