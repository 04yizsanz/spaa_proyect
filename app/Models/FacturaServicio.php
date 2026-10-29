<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent que representa la tabla pivot entre facturas y servicios en el sistema SPAA.
 *
 * La tabla `factura_servicio` implementa una relación muchos-a-muchos entre los modelos
 * Factura y Servicio, extendida con atributos adicionales (cantidad y precio unitario).
 * Al no existir una sola columna de clave primaria autoincremental, el modelo desactiva
 * el mecanismo estándar de Eloquent para clave primaria mediante `$incrementing = false`
 * y `$primaryKey = null`. La unicidad de cada registro queda garantizada por la
 * combinación compuesta de las columnas `factura_id` y `servicio_id`.
 *
 * @property int   $factura_id      Clave foránea que referencia a la factura asociada.
 * @property int   $servicio_id     Clave foránea que referencia al servicio incluido en la factura.
 * @property int   $cantidad        Número de unidades del servicio facturadas.
 * @property float $precio_unitario Precio cobrado por unidad del servicio al momento de la facturación.
 *
 * @package App\Models
 */
class FacturaServicio extends Model
{
    // HasFactory habilita el uso de fábricas de modelos (ModelFactory) para
    // generar instancias de prueba mediante Eloquent Factory.
    use HasFactory;

    /**
     * Nombre de la tabla de base de datos asociada a este modelo.
     *
     * Se declara explícitamente porque el nombre que Laravel inferiría
     * automáticamente ('factura_servicios') no coincide con el nombre real
     * de la tabla (`factura_servicio`) definido en las migraciones.
     *
     * @var string
     */
    protected $table = 'factura_servicio';

    /**
     * Indica que la clave primaria de esta tabla no es autoincremental.
     *
     * Se establece en `false` porque la tabla utiliza una clave primaria
     * compuesta (factura_id + servicio_id) en lugar de una columna entera
     * autoincremental convencional. Desactivar el incremento automático
     * evita que Eloquent intente asignar o recuperar un valor de `id`
     * al insertar nuevos registros.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Nombre de la columna que actúa como clave primaria.
     *
     * Se establece en `null` porque esta tabla no posee una columna única
     * de clave primaria; en su lugar, la integridad del registro está garantizada
     * por la combinación compuesta de `factura_id` y `servicio_id`. Asignar
     * `null` informa a Eloquent que no existe una clave primaria simple,
     * impidiendo que el framework intente consultas o inserciones basadas
     * en una columna `id` inexistente.
     *
     * @var string|null
     */
    protected $primaryKey = null;

    /**
     * Atributos que pueden asignarse de forma masiva (mass assignment).
     *
     * Solo los campos listados aquí son aceptados por métodos como
     * `create()` o `fill()`, protegiendo el modelo contra la asignación
     * masiva de columnas no autorizadas.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'factura_id',      // Clave foránea que vincula el registro con la factura correspondiente
        'servicio_id',     // Clave foránea que vincula el registro con el servicio incluido
        'cantidad',        // Número de unidades del servicio facturadas
        'precio_unitario', // Precio por unidad vigente en el momento de la facturación
    ];

    /**
     * Conversiones (casts) automáticas de atributos a tipos nativos de PHP.
     *
     * Eloquent aplicará estas transformaciones de forma transparente al leer
     * o escribir los atributos indicados, garantizando consistencia de tipos
     * a lo largo de la aplicación.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // `cantidad` se convierte a entero para garantizar operaciones aritméticas
        // correctas al calcular totales o validar el número de unidades.
        'cantidad'        => 'integer',

        // `precio_unitario` se convierte a decimal con dos posiciones para mantener
        // la precisión monetaria requerida al calcular subtotales en la factura.
        'precio_unitario' => 'decimal:2',
    ];

    /**
     * Obtiene la factura a la que pertenece este detalle de servicio.
     *
     * Establece una relación de tipo "pertenece a" (BelongsTo) con el modelo
     * Factura. Cada registro de esta tabla está asociado a una única factura,
     * identificada mediante la clave foránea `factura_id`. Se indica también
     * la clave propietaria del modelo Factura (`factura_id`) al no usar el
     * valor predeterminado de Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        // La clave foránea local `factura_id` apunta a la clave primaria
        // personalizada `factura_id` del modelo Factura.
        return $this->belongsTo(Factura::class, 'factura_id', 'factura_id');
    }

    /**
     * Obtiene el servicio incluido en este detalle de factura.
     *
     * Establece una relación de tipo "pertenece a" (BelongsTo) con el modelo
     * Servicio. Cada registro de esta tabla referencia a un único servicio,
     * identificado mediante la clave foránea `servicio_id`. Se indica también
     * la clave propietaria del modelo Servicio (`servicio_id`) al no usar el
     * valor predeterminado de Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function servicio()
    {
        // La clave foránea local `servicio_id` apunta a la clave primaria
        // personalizada `servicio_id` del modelo Servicio.
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }
}
