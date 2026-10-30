<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Proveedor extends Model
{
     use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
    use SoftDeletes;
     protected $table="proveedor";
     protected $primaryKey = "proveedor_id";
     protected $fillable = [ 
        "nombre",
        "contacto",
        "email",
        "registro_tributario",

     ];

         public function producto(){
            return $this->hasMany(Producto::class);
         }

         
}