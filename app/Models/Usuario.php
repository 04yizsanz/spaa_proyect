<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory; // HasFactory se encarga de crear datos de prueba para la tabla usuarios

    protected $table = "usuarios"; // especificamos el nombre de la tabla que va a usar este modelo

    protected $primaryKey = "usuario_id"; // definimos la clave primaria

    protected $fillable = [ // definimos los campos
        "rol_id",
        "nombre",
        "apellido",
        "documento",
        "email",
        "telefono",
        "password",
        "estado" // 1, 0
    ];

    protected $casts = [
        "estado" => "boolean" // true, false
    ];

    protected $hidden = [
        "password"
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, "rol_id", "rol_id"); // un usuario pertenece a un rol
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, "usuario_id", "usuario_id"); // un usuario tiene un empleado
    }
}
