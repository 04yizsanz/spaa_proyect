<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent que representa un servicio ofrecido por el establecimiento en el sistema SPAA.
 *
 * Un servicio define la naturaleza del trabajo que puede ser agendado en una cita,
 * incluyendo su duración estimada, precio y estado de disponibilidad. El modelo
 * implementa eliminación lógica (soft deletes) para desactivar servicios sin
 * eliminar físicamente el historial de citas o facturas relacionadas.
 *
 * @property int             $servicio_id  Identificador primario autoincremental del servicio.
 * @property string          $nombre       Nombre descriptivo del servicio ofrecido.
 * @property int             $duracion_min Duración estimada del servicio expresada en minutos.
 * @property float           $precio       Precio base del servicio (precisión de dos decimales).
 * @property string|null     $descripcion  Descripción detallada del servicio; puede ser nula.
 * @property bool            $activo       Indica si el servicio está habilitado para ser agendado.
 * @property \Carbon\Carbon|null $deleted_at Marca temporal de eliminación lógica; nula si el registro está activo.
 *
 * @package App\Models
 */
class Servicio extends Model
{
    // HasFactory habilita el uso de fábricas de modelos (ModelFactory) para
    // generar instancias de prueba mediante Eloquent Factory.
    use HasFactory;

    // SoftDeletes reemplaza las eliminaciones físicas por una marca temporal
    // en la columna `deleted_at`, preservando la integridad referencial con
    // citas y facturas que ya hayan utilizado el servicio.
    use SoftDeletes;

    /**
     * Nombre de la tabla de base de datos asociada a este modelo.
     *
     * Se declara explícitamente para desvincular el nombre del modelo del
     * nombre de la tabla y reflejar la convención de nomenclatura del proyecto.
     *
     * @var string
     */
    protected $table = 'servicios';

    /**
     * Nombre de la columna que actúa como clave primaria de la tabla.
     *
     * Se sobreescribe el valor predeterminado de Eloquent ('id') para usar
     * `servicio_id`, siguiendo la convención de nomenclatura del proyecto SPAA.
     * Este valor es también referenciado como clave propietaria en los modelos
     * relacionados (p. ej. Cita y FacturaServicio).
     *
     * @var string
     */
    protected $primaryKey = 'servicio_id';

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
        'nombre',       // Nombre del servicio tal como se muestra al cliente
        'duracion_min', // Tiempo estimado de ejecución del servicio en minutos
        'precio',       // Precio base cobrado al cliente por el servicio
        'descripcion',  // Descripción ampliada del servicio (opcional)
        'activo',       // Bandera que habilita o deshabilita el servicio en el catálogo
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
        // `duracion_min` se convierte a entero para evitar cálculos incorrectos
        // al operar con tiempos de agenda (suma, comparación de minutos).
        'duracion_min' => 'integer',

        // `precio` se convierte a decimal con dos posiciones para mantener
        // la precisión monetaria requerida en facturas y reportes financieros.
        'precio'       => 'decimal:2',

        // `activo` se convierte a booleano para representar claramente el estado
        // habilitado/deshabilitado sin ambigüedad numérica (0/1).
        'activo'       => 'boolean',
    ];

    /**
     * Obtiene todas las citas que incluyen este servicio.
     *
     * Establece una relación de tipo "tiene muchos" (HasMany) con el modelo
     * Cita. Un servicio puede aparecer en múltiples citas agendadas por distintos
     * clientes. Se especifican tanto la clave foránea en la tabla `citas`
     * (`servicio_id`) como la clave local de este modelo (`servicio_id`), dado
     * que la clave primaria personalizada difiere del valor predeterminado de Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas(): HasMany
    {
        // La clave foránea `servicio_id` en la tabla `citas` apunta a la clave
        // primaria `servicio_id` de este modelo (no al valor por defecto `id`).
        return $this->hasMany(Cita::class, 'servicio_id', 'servicio_id');
    }
}
