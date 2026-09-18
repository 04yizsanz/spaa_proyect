<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory; 

    use SoftDeletes;

    protected $table = "clientes"; // Especificamos el nombre de la tabla en la BD

    protected $primaryKey = "cliente_id"; // Definimos la clave primaria

    protected $fillable = [ // Definimos los campos
        "usuario_id",
        "fecha_nacimiento",
        "preferencias"
    ];

    protected $casts = [
        "fecha_nacimiento" => "date"
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "usuario_id", "usuario_id"); // Un cliente pertenece a un usuario
    }

    public function reserva()
    {
        return $this->hasMany(Reserva::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas reservas
    }

    public function venta()
    {
        return $this->hasMany(Venta::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas ventas
    }
}