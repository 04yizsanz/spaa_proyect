<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent que representa una cita agendada en el sistema SPAA.
 *
 * Una cita vincula a un cliente con un empleado y un servicio específico,
 * registrando la fecha, hora y estado actual del encuentro. El modelo
 * implementa eliminación lógica (soft deletes) para preservar el historial
 * de citas sin eliminar físicamente los registros de la base de datos.
 *
 * @property int         $citas_id      Identificador primario autoincremental de la cita.
 * @property string      $codigo_cita   Código alfanumérico único que identifica la cita.
 * @property \Carbon\Carbon $fecha      Fecha programada de la cita (casteada a objeto Carbon).
 * @property string      $hora          Hora programada de la cita en formato HH:MM:SS.
 * @property string      $estado        Estado actual de la cita (p. ej. pendiente, completada, cancelada).
 * @property int         $cliente_id    Clave foránea que referencia al cliente asociado.
 * @property int         $empleado_id   Clave foránea que referencia al empleado asignado.
 * @property int         $servicio_id   Clave foránea que referencia al servicio solicitado.
 * @property \Carbon\Carbon|null $deleted_at Marca temporal de eliminación lógica; nula si el registro está activo.
 *
 * @package App\Models
 */
class Cita extends Model
{
    // HasFactory habilita el uso de fábricas de modelos (ModelFactory) para
    // generar instancias de prueba mediante Eloquent Factory.
    use HasFactory;

    // SoftDeletes reemplaza las eliminaciones físicas por una marca temporal
    // en la columna `deleted_at`, permitiendo recuperar registros posteriormente.
    use SoftDeletes;

    /**
     * Nombre de la tabla de base de datos asociada a este modelo.
     *
     * Se declara explícitamente porque el nombre plural convencional que
     * Laravel inferiría ('citas') coincide, pero se especifica para mayor
     * claridad y mantenimiento del código.
     *
     * @var string
     */
    protected $table = 'citas';

    /**
     * Nombre de la columna que actúa como clave primaria de la tabla.
     *
     * Se sobreescribe el valor predeterminado de Eloquent ('id') para
     * reflejar la convención de nomenclatura propia del proyecto SPAA.
     *
     * @var string
     */
    protected $primaryKey = 'citas_id';

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
        'codigo_cita',  // Código único legible para identificar la cita
        'fecha',        // Fecha programada de la cita
        'hora',         // Hora programada de la cita
        'estado',       // Estado actual del ciclo de vida de la cita
        'cliente_id',   // Referencia al cliente que solicita la cita
        'empleado_id',  // Referencia al empleado que atenderá la cita
        'servicio_id',  // Referencia al servicio a realizar en la cita
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
        // El campo `fecha` se convierte automáticamente a un objeto Carbon,
        // lo que facilita operaciones de comparación y formato de fechas.
        'fecha' => 'date',
    ];

    /**
     * Obtiene el cliente asociado a esta cita.
     *
     * Establece una relación de tipo "pertenece a" (BelongsTo) con el modelo
     * Cliente. Cada cita es solicitada por exactamente un cliente, identificado
     * mediante la clave foránea `cliente_id` en la tabla `citas`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        // La clave foránea `cliente_id` en la tabla `citas` apunta al
        // identificador primario del modelo Cliente (por defecto `id`).
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Obtiene el empleado asignado a esta cita.
     *
     * Establece una relación de tipo "pertenece a" (BelongsTo) con el modelo
     * Empleado. Se especifican tanto la clave foránea local (`empleado_id`)
     * como la clave propietaria del modelo Empleado (`empleado_id`), dado que
     * dicho modelo utiliza una clave primaria personalizada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado(): BelongsTo
    {
        // Se indica la clave propietaria `empleado_id` explícitamente porque
        // el modelo Empleado no utiliza la columna `id` como clave primaria.
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }

    /**
     * Obtiene el servicio solicitado en esta cita.
     *
     * Establece una relación de tipo "pertenece a" (BelongsTo) con el modelo
     * Servicio. Se especifican la clave foránea local (`servicio_id`) y la
     * clave propietaria del modelo Servicio (`servicio_id`) por la misma razón
     * que en la relación con Empleado: clave primaria personalizada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function servicio(): BelongsTo
    {
        // Se indica la clave propietaria `servicio_id` explícitamente porque
        // el modelo Servicio no utiliza la columna `id` como clave primaria.
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }
}
