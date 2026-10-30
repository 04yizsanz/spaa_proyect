<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\SoftDeletes;
   
    class Producto extends Model
    {
        use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
       use SoftDeletes;
        protected $table="producto";
        protected $primaryKey = "producto_id";
        protected $fillable = [ 
            "nombre",
            "cantidad",
            "precio",
            "fecha_registro",
            "proveedor_id",

        ];


            public function proveedor(){
                return $this->belongsTo(Proveedor::class);
            }
            public function movimiento_inventario()
        {
            return $this->hasMany(MovimientoInventario::class,);
        }
            
            // hasMany: muchos 
    // hasOne: relacion de 1 a 1 
    // belognsTo: tiene un 
            
    }
