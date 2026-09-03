<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;

    protected $table="pagos";
    protected $fillable =[
        'monto',
        'metodo',
        'fecha_hora',
        'estado',
        'codigo_cita'
    ];

    public function citas(){
        return $this->hasMany(Citas::class);
    }
    
}
