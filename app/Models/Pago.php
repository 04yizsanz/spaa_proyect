<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent que representa un pago realizado en el sistema.
 *
 * Registra la transacción económica vinculada a una cita, incluyendo
 * el monto cobrado, el método de pago utilizado, la fecha y hora de
 * la operación y el estado actual del pago (por ejemplo: pendiente,
 * completado, cancelado).
 *
 * Relaciones definidas:
 *  - Pertenece a una {@see Cita} (belongsTo).
 *
 * @package App\Models
 *
 * @property int                 $pago_id     Clave primaria autoincremental del pago.
 * @property float               $monto       Importe del pago (cast a decimal con 2 decimales).
 * @property string              $metodo      Método de pago utilizado (p. ej. «efectivo», «tarjeta»).
 * @property \Carbon\Carbon      $fecha_hora  Fecha y hora exacta en que se efectuó el pago.
 * @property string              $estado      Estado del pago (p. ej. «pendiente», «completado»).
 * @property int                 $codigo_cita Clave foránea que vincula el pago con su cita.
 * @property \Carbon\Carbon|null $created_at  Marca temporal de creación gestionada por Eloquent.
 * @property \Carbon\Carbon|null $updated_at  Marca temporal de última actualización.
 */
class Pago extends Model
{
    /**
     * HasFactory habilita el uso del factory asociado a este modelo,
     * permitiendo la generación de instancias sintéticas durante las
     * pruebas automatizadas y la siembra de la base de datos mediante
     * Database\Factories\PagoFactory.
     */
    use HasFactory;

    // Nombre explícito de la tabla en la base de datos; evita que Eloquent
    // infiera el nombre plural en inglés ("pagos" coincide en este caso,
    // pero se declara de forma explícita para mayor claridad y seguridad
    // ante futuros cambios de convención).
    protected $table = "pagos";

    // Clave primaria personalizada; sobreescribe el valor por defecto "id"
    // que Eloquent asumiría de no indicarse esta propiedad.
    protected $primaryKey = "pago_id";

    // $fillable lista los atributos que pueden asignarse de forma masiva
    // (mass assignment) mediante métodos como create() o fill(). Protege
    // el modelo contra vulnerabilidades de tipo mass-assignment al rechazar
    // cualquier campo no declarado en este arreglo.
    protected $fillable = [
        'monto',       // Importe económico de la transacción.
        'metodo',      // Canal o forma de pago empleada.
        'fecha_hora',  // Momento exacto en que se procesó el pago.
        'estado',      // Estado actual del ciclo de vida del pago.
        'codigo_cita', // Referencia a la cita que origina este pago.
    ];

    // $casts convierte automáticamente los valores de las columnas al tipo
    // PHP especificado al acceder o persistir el atributo, eliminando la
    // necesidad de conversiones manuales en la lógica de la aplicación.
    protected $casts = [
        // fecha_hora se deserializa como instancia de \Carbon\Carbon,
        // facilitando el manejo y formateo de fechas y horas.
        'fecha_hora' => 'datetime',

        // monto se convierte a tipo decimal con precisión de 2 cifras
        // decimales, lo que garantiza representaciones monetarias correctas.
        'monto' => 'decimal:2',
    ];

    /**
     * Obtiene la cita asociada a este pago.
     *
     * Define una relación de cardinalidad N:1 (muchos pagos pueden
     * pertenecer a una misma cita en caso de pagos parciales). La clave
     * foránea en la tabla `pagos` es `codigo_cita`, que referencia la
     * columna `codigo_cita` de la tabla de citas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'codigo_cita', 'codigo_cita');
    }
}
