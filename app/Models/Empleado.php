<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent que representa a un empleado del establecimiento en el sistema SPAA.
 *
 * Un empleado es el profesional encargado de atender las citas agendadas por
 * los clientes. El modelo almacena datos personales, laborales y de disponibilidad,
 * e implementa eliminación lógica (soft deletes) para conservar el historial sin
 * borrar físicamente los registros de la base de datos.
 *
 * @property int             $empleado_id        Identificador primario autoincremental del empleado.
 * @property string          $nombre             Nombre(s) del empleado.
 * @property string          $apellido           Apellido(s) del empleado.
 * @property string          $documento          Número de documento de identidad del empleado.
 * @property string          $correo             Dirección de correo electrónico del empleado.
 * @property string          $telefono           Número de teléfono de contacto del empleado.
 * @property string          $rol                Cargo o rol que desempeña el empleado en el establecimiento.
 * @property float           $salario            Salario mensual del empleado (precisión de dos decimales).
 * @property \Carbon\Carbon  $fecha_contratacion Fecha en que el empleado fue contratado (casteada a Carbon).
 * @property bool            $disponibilidad     Indica si el empleado está disponible para recibir citas.
 * @property \Carbon\Carbon|null $deleted_at     Marca temporal de eliminación lógica; nula si el registro está activo.
 *
 * @package App\Models
 */
class Empleado extends Model
{
    // HasFactory habilita el uso de fábricas de modelos (ModelFactory) para
    // generar instancias de prueba mediante Eloquent Factory.
    use HasFactory;

    // SoftDeletes reemplaza las eliminaciones físicas por una marca temporal
    // en la columna `deleted_at`, permitiendo restaurar registros si es necesario.
    use SoftDeletes;

    /**
     * Nombre de la tabla de base de datos asociada a este modelo.
     *
     * Se declara explícitamente para desvincular el nombre del modelo del
     * nombre de la tabla y reflejar la convención de nomenclatura del proyecto.
     *
     * @var string
     */
    protected $table = 'empleados';

    /**
     * Nombre de la columna que actúa como clave primaria de la tabla.
     *
     * Se sobreescribe el valor predeterminado de Eloquent ('id') para usar
     * `empleado_id`, siguiendo la convención de nomenclatura del proyecto SPAA.
     * Este valor es también referenciado como clave propietaria en los modelos
     * relacionados (p. ej. Cita).
     *
     * @var string
     */
    protected $primaryKey = 'empleado_id';

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
        'nombre',             // Nombre(s) del empleado
        'apellido',           // Apellido(s) del empleado
        'documento',          // Número de documento de identidad
        'correo',             // Dirección de correo electrónico
        'telefono',           // Número de teléfono de contacto
        'rol',                // Cargo o función del empleado en el establecimiento
        'salario',            // Remuneración mensual del empleado
        'fecha_contratacion', // Fecha de inicio de la relación laboral
        'disponibilidad',     // Indicador booleano de disponibilidad para citas
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
        // `salario` se convierte a decimal con dos posiciones para evitar
        // errores de precisión en cálculos de nómina o reportes financieros.
        'salario'            => 'decimal:2',

        // `fecha_contratacion` se convierte a un objeto Carbon para facilitar
        // operaciones de comparación, antigüedad y formato de fechas.
        'fecha_contratacion' => 'date',

        // `disponibilidad` se convierte a booleano para representar claramente
        // los estados activo/inactivo sin ambigüedad numérica (0/1).
        'disponibilidad'     => 'boolean',
    ];

    /**
     * Obtiene todas las citas asignadas a este empleado.
     *
     * Establece una relación de tipo "tiene muchos" (HasMany) con el modelo
     * Cita. Un empleado puede atender múltiples citas a lo largo del tiempo.
     * Se especifican tanto la clave foránea en la tabla `citas` (`empleado_id`)
     * como la clave local de este modelo (`empleado_id`), dado que la clave
     * primaria personalizada difiere del valor predeterminado de Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas(): HasMany
    {
        // La clave foránea `empleado_id` en la tabla `citas` apunta a la clave
        // primaria `empleado_id` de este modelo (no al valor por defecto `id`).
        return $this->hasMany(Cita::class, 'empleado_id', 'empleado_id');
    }
}
