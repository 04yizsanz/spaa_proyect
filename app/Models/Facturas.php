<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    use HasFactory;

    protected $table="facturas";
    protected $fillable =[
        'fecha_hora',
        'subtotal',
        'impuestos',
        'total',
        'pdf_url',
        'cliente_id'
    ];


    public function FacturaServicio(){
        return $this->belongsTo(FacturaServicio::class);
    }  
    
}
