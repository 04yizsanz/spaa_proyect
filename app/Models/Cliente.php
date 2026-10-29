<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent que representa a un cliente del sistema.
 *
 * Un cliente es la extensión del perfil de un usuario registrado que
 * contiene información personal adicional (fecha de nacimiento y
 * preferencias) requerida para la gestión de citas y ventas.
 *
 * Relaciones definidas:
 *  - Pertenece a un {@see Usuario} (belongsTo).
 *  - Posee múltiples reservas {@see Reserva} (hasMany).
 *  - Posee múltiples ventas {@see Venta} (hasMany).
 *
 * @package App\Models
 *
 * @property int         $cliente_id       Clave primaria autoincremental del cliente.
 * @property int         $usuario_id       Clave foránea que vincula al usuario propietario.
 * @property \Carbon\Carbon $fecha_nacimiento Fecha de nacimiento del cliente (cast a date).
 * @property string|null $preferencias     Texto libre con las preferencias del cliente.
 * @property \Carbon\Carbon|null $created_at  Marca temporal de creación gestionada por Eloquent.
 * @property \Carbon\Carbon|null $updated_at  Marca temporal de última actualización.
 * @property \Carbon\Carbon|null $deleted_at  Marca temporal de borrado lógico (SoftDeletes).
 */
class Cliente extends Model
{
    /**
     * HasFactory habilita el uso del factory asociado a este modelo,
     * permitiendo la generación de instancias de prueba mediante
     * Database\Factories\ClienteFactory durante las pruebas automatizadas
     * y la siembra (seeding) de la base de datos.
     */
    use HasFactory; 

    /**
     * SoftDeletes implementa el borrado lógico («soft delete»): en lugar
     * de eliminar físicamente el registro de la tabla, Eloquent establece
     * la columna `deleted_at` con la marca temporal actual. Los registros
     * marcados son excluidos automáticamente de todas las consultas
     * ordinarias, pero pueden recuperarse con withTrashed() o restore().
     */
    use SoftDeletes;


    // Nombre explícito de la tabla en la base de datos; evita que Eloquent
    // infiera el nombre plural en inglés ("clients") por convención.
    protected $table = "clientes"; // Especificamos el nombre de la tabla en la BD

    // Clave primaria personalizada; sobreescribe el valor por defecto "id"
    // que Eloquent asumiría de no indicarse esta propiedad.
    protected $primaryKey = "cliente_id"; // Definimos la clave primaria

    // $fillable lista los atributos que pueden asignarse de forma masiva
    // (mass assignment) mediante métodos como create() o fill(). Todos los
    // campos no incluidos aquí serán ignorados en asignaciones masivas,
    // protegiendo el modelo contra vulnerabilidades de tipo mass-assignment.
    protected $fillable = [ // Definimos los campos
        "usuario_id",
        "fecha_nacimiento",
        "preferencias"
    ];

    // $casts convierte automáticamente los valores de las columnas al tipo
    // PHP especificado al leer o escribir el atributo, eliminando la
    // necesidad de conversiones manuales en el código de la aplicación.
    protected $casts = [
        // La columna fecha_nacimiento se deserializa como instancia de
        // \Carbon\Carbon con la parte horaria descartada (tipo "date").
        "fecha_nacimiento" => "date"
    ];

    /**
     * Obtiene el usuario al que pertenece este cliente.
     *
     * Define una relación de cardinalidad N:1 (muchos clientes pueden
     * corresponder a un mismo usuario, aunque en la práctica la relación
     * es 1:1). La clave foránea en la tabla `clientes` es `usuario_id`
     * y referencia la columna `usuario_id` de la tabla `usuarios`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "usuario_id", "usuario_id"); // Un cliente pertenece a un usuario
    }

    /**
     * Obtiene las reservas asociadas a este cliente.
     *
     * Define una relación de cardinalidad 1:N: un cliente puede tener
     * múltiples reservas. La clave foránea en la tabla de reservas es
     * `cliente_id`, que referencia `cliente_id` de la tabla `clientes`.
     *
     * LIMITACIÓN: El modelo {@see Reserva} no se encuentra definido en
     * el proyecto actualmente. Esta relación generará un error en tiempo
     * de ejecución hasta que dicho modelo sea creado e incorporado al
     * directorio App\Models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reserva()
    {
        return $this->hasMany(Reserva::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas reservas
    }

    /**
     * Obtiene las ventas asociadas a este cliente.
     *
     * Define una relación de cardinalidad 1:N: un cliente puede tener
     * múltiples ventas. La clave foránea en la tabla de ventas es
     * `cliente_id`, que referencia `cliente_id` de la tabla `clientes`.
     *
     * LIMITACIÓN: El modelo {@see Venta} no se encuentra definido en
     * el proyecto actualmente. Esta relación generará un error en tiempo
     * de ejecución hasta que dicho modelo sea creado e incorporado al
     * directorio App\Models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function venta()
    {
        return $this->hasMany(Venta::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas ventas
    }
}
