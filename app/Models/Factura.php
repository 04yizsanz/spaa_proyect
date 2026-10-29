<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent que representa una factura emitida en el sistema.
 *
 * Una factura consolida la información económica de los servicios
 * prestados a un cliente en un momento determinado: subtotal antes de
 * impuestos, monto de impuestos aplicados, total final y la URL del
 * documento PDF generado para su descarga o impresión.
 *
 * Relaciones definidas:
 *  - Pertenece a un {@see Cliente} (belongsTo).
 *  - Posee múltiples líneas de servicio {@see FacturaServicio} (hasMany).
 *
 * @package App\Models
 *
 * @property int                 $factura_id  Clave primaria autoincremental de la factura.
 * @property \Carbon\Carbon      $fecha_hora  Fecha y hora de emisión de la factura.
 * @property float               $subtotal    Monto antes de la aplicación de impuestos.
 * @property float               $impuestos   Monto total de impuestos calculados.
 * @property float               $total       Monto final tras sumar impuestos al subtotal.
 * @property string|null         $pdf_url     URL del archivo PDF asociado a esta factura.
 * @property int                 $cliente_id  Clave foránea que vincula la factura con el cliente.
 * @property \Carbon\Carbon|null $created_at  Marca temporal de creación gestionada por Eloquent.
 * @property \Carbon\Carbon|null $updated_at  Marca temporal de última actualización.
 */
class Factura extends Model
{
    /**
     * HasFactory habilita el uso del factory asociado a este modelo,
     * permitiendo la generación de instancias sintéticas durante las
     * pruebas automatizadas y la siembra de la base de datos mediante
     * Database\Factories\FacturaFactory.
     */
    use HasFactory;

    // Nombre explícito de la tabla en la base de datos; evita que Eloquent
    // infiera el nombre plural en inglés ("facturas" coincide por
    // convención, pero se declara explícitamente para mayor claridad).
    protected $table = "facturas";

    // Clave primaria personalizada; sobreescribe el valor por defecto "id"
    // que Eloquent asumiría de no indicarse esta propiedad.
    protected $primaryKey = "factura_id";

    // $fillable lista los atributos que pueden asignarse de forma masiva
    // (mass assignment) mediante métodos como create() o fill(). Todos los
    // campos no incluidos aquí serán ignorados en asignaciones masivas,
    // protegiendo el modelo contra vulnerabilidades de tipo mass-assignment.
    protected $fillable = [
        'fecha_hora',  // Momento de emisión del documento fiscal.
        'subtotal',    // Base imponible antes de impuestos.
        'impuestos',   // Valor calculado de los impuestos aplicables.
        'total',       // Importe total a pagar por el cliente.
        'pdf_url',     // Ruta o URL al archivo PDF de la factura generada.
        'cliente_id',  // Referencia al cliente al que se emite la factura.
    ];

    // $casts convierte automáticamente los valores de las columnas al tipo
    // PHP especificado al acceder o persistir el atributo, garantizando
    // coherencia de tipos sin necesidad de conversiones manuales.
    protected $casts = [
        // fecha_hora se deserializa como instancia de \Carbon\Carbon,
        // habilitando el manejo y formateo de fechas con dicha biblioteca.
        'fecha_hora' => 'datetime',

        // Los campos monetarios se convierten a decimal con 2 cifras
        // decimales para garantizar precisión en los cálculos financieros.
        'subtotal'   => 'decimal:2',
        'impuestos'  => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    /**
     * Obtiene el cliente al que pertenece esta factura.
     *
     * Define una relación de cardinalidad N:1: múltiples facturas pueden
     * estar asociadas a un mismo cliente. La clave foránea en la tabla
     * `facturas` es `cliente_id`, que referencia la columna `cliente_id`
     * de la tabla `clientes`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }

    /**
     * Obtiene las líneas de servicio detalladas de esta factura.
     *
     * Define una relación de cardinalidad 1:N: una factura puede contener
     * múltiples registros de {@see FacturaServicio}, donde cada uno
     * representa un servicio individual incluido en el documento. La clave
     * foránea en la tabla de detalles es `factura_id`, que referencia
     * `factura_id` de la tabla `facturas`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function servicios()
    {
        return $this->hasMany(FacturaServicio::class, 'factura_id', 'factura_id');
    }
}
