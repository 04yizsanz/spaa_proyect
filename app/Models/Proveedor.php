<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Proveedor extends Model
{
     use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
    
     protected $table="proveedores";
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