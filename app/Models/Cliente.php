<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'cliente_id';

    protected $fillable = [
        'usuario_id',
        'fecha_nacimiento',
        'preferencias',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Un cliente pertenece a un usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }
}
