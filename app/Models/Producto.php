<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Producto extends Model
{
     use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
    
     protected $table="producto";
     protected $fillable = [ 
        "nombre",
        "cantidad",
        "precio",
        "fecha_registro",
        "proveedor_id",

     ];


         public function proveedor(){
            return $this->belongsTo(proveedor::class);
         }
        public function movimiento_inventario()
    {
        return $this->hasMany(MovimientoInventario::class,);
    }
         
         // hasMany: muchos 
// hasOne: relacion de 1 a 1 
// belognsTo: tiene un 
         
}
