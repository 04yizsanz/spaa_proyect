<?php

/**
 * ARCHIVO: app/Models/Usuario.php
 *
 * PROPÓSITO: Define el modelo Eloquent del recurso Usuario, que representa
 * a los usuarios del sistema Asha Spa registrados en la tabla 'usuarios'.
 * Este modelo extiende Authenticatable en lugar del modelo base de Eloquent,
 * lo que lo habilita como entidad de autenticación reconocida por el guard
 * de sesión de Laravel.
 *
 * PATRÓN DE DISEÑO: Modelo activo (Active Record) implementado mediante
 * el ORM Eloquent de Laravel. Cada instancia de esta clase corresponde a
 * un registro de la tabla 'usuarios' en la base de datos.
 */

namespace App\Models;

// Importa el trait HasFactory que permite generar instancias del modelo
// mediante factories para pruebas y seeders.
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Importa la clase base Authenticatable del framework Laravel.
// Esta clase extiende el Model base de Eloquent e implementa los contratos
// Authenticatable y CanResetPassword, habilitando el modelo para ser
// utilizado como entidad de autenticación en el guard de sesión de Laravel.
// Se importa con el alias 'User' para seguir la convención de Laravel,
// pero representa a los usuarios del dominio del negocio bajo el nombre 'Usuario'.
use Illuminate\Foundation\Auth\User as Authenticatable;

// Importa el trait Notifiable que permite al modelo enviar notificaciones
// de Laravel a través de diferentes canales (correo, SMS, base de datos, etc.).
use Illuminate\Notifications\Notifiable;

/**
 * Clase Usuario
 *
 * Modelo Eloquent que representa un usuario del sistema de gestión de Asha Spa.
 * Extiende la clase Authenticatable para integrarse con el sistema de
 * autenticación de Laravel, permitiendo el inicio y cierre de sesión,
 * la verificación de credenciales y la gestión del token de recuerdo.
 *
 * Relaciones definidas:
 *   - Un usuario pertenece a un Rol (relación muchos a uno).
 *   - Un usuario puede tener un Empleado asociado (relación uno a uno).
 *
 * @property int         $usuario_id    Clave primaria del usuario.
 * @property int         $rol_id        Clave foránea que referencia la tabla 'roles'.
 * @property string      $nombre        Nombre del usuario.
 * @property string      $apellido      Apellido del usuario.
 * @property string      $documento     Número de documento de identidad (único).
 * @property string      $email         Correo electrónico (único, usado para autenticación).
 * @property string|null $telefono      Número de teléfono (opcional).
 * @property string      $password      Contraseña almacenada como hash bcrypt.
 * @property bool        $estado        Estado activo (true) o inactivo (false).
 * @property string|null $remember_token Token de sesión persistente para "Recordarme".
 */
class Usuario extends Authenticatable
{
    // El trait HasFactory permite utilizar fábricas de modelos para generar
    // datos de prueba mediante el método estático Usuario::factory().
    // El trait Notifiable habilita el envío de notificaciones mediante el
    // método notify() sobre instancias de este modelo.
    use HasFactory, Notifiable;

    /**
     * @var string $table
     *
     * Propiedad protegida que especifica el nombre de la tabla en la base
     * de datos asociada a este modelo. Laravel inferiría el nombre 'usuarios'
     * automáticamente, pero se declara explícitamente para mayor claridad.
     * El modificador de acceso 'protected' restringe la visibilidad al modelo
     * y sus subclases.
     */
    protected $table      = 'usuarios';

    /**
     * @var string $primaryKey
     *
     * Propiedad protegida que sobrescribe la clave primaria predeterminada
     * de Eloquent. Por convención, Laravel asume que la clave primaria
     * se denomina 'id'; al declarar 'usuario_id' se indica al ORM que
     * utilice esta columna en su lugar para todas las operaciones de
     * búsqueda, actualización y eliminación por clave primaria.
     */
    protected $primaryKey = 'usuario_id';

    /**
     * Retorna el nombre del campo que actúa como identificador de autenticación.
     *
     * PROPÓSITO: Sobrescribe el método heredado de Authenticatable, que por
     * defecto retorna 'id'. Al retornar 'usuario_id', se indica al sistema
     * de autenticación de Laravel que debe usar esta columna para almacenar
     * y recuperar el identificador de sesión del usuario autenticado.
     * Sin esta sobrescritura, Auth::login() fallaría al intentar buscar
     * la columna 'id', que no existe en la tabla 'usuarios'.
     *
     * @return string Nombre de la columna que identifica al usuario: 'usuario_id'.
     */
    public function getAuthIdentifierName(): string
    {
        // Retorna el nombre de la columna como cadena de texto.
        // El tipo de retorno declarado 'string' garantiza que el método
        // siempre devuelve un valor de tipo cadena.
        return 'usuario_id';
    }

    /**
     * Retorna el valor del identificador de autenticación del usuario actual.
     *
     * PROPÓSITO: Sobrescribe el método heredado de Authenticatable para
     * retornar el valor de la columna 'usuario_id' en lugar de 'id'.
     * Laravel utiliza este valor para almacenar el identificador del usuario
     * en la sesión activa y para recuperar el usuario autenticado en
     * peticiones posteriores.
     *
     * @return mixed Valor entero de la clave primaria 'usuario_id'.
     */
    public function getAuthIdentifier(): mixed
    {
        // $this hace referencia a la instancia actual del modelo Usuario.
        // ->usuario_id accede dinámicamente al atributo 'usuario_id' del modelo,
        // que corresponde al valor del campo homónimo en la base de datos.
        return $this->usuario_id;
    }

    /**
     * @var array $fillable
     *
     * Arreglo de cadenas que declara los atributos que pueden ser asignados
     * masivamente mediante los métodos create() o fill() de Eloquent.
     * Este mecanismo protege contra ataques de asignación masiva, donde un
     * atacante podría enviar campos no autorizados en una petición HTTP.
     * Solo los campos listados aquí pueden ser establecidos mediante
     * asignación masiva; los demás son ignorados silenciosamente.
     */
    protected $fillable = [
        'rol_id',       // Identificador del rol asignado al usuario.
        'nombre',       // Nombre de pila del usuario.
        'apellido',     // Apellido del usuario.
        'documento',    // Número de documento de identidad.
        'email',        // Correo electrónico para autenticación.
        'telefono',     // Número de teléfono de contacto (puede ser nulo).
        'password',     // Contraseña; debe almacenarse siempre como hash.
        'estado',       // Estado del usuario: true = activo, false = inactivo.
    ];

    /**
     * @var array $hidden
     *
     * Arreglo de atributos que serán excluidos cuando el modelo se serialice
     * a un arreglo o a JSON (por ejemplo, en respuestas de API mediante
     * response()->json($usuario)). Esto impide que el hash de la contraseña
     * y el token de recuerdo sean expuestos en las respuestas HTTP.
     */
    protected $hidden = [
        'password',       // Hash bcrypt de la contraseña. Nunca debe exponerse.
        'remember_token', // Token generado por la funcionalidad "Recordarme".
    ];

    /**
     * @var array $casts
     *
     * Arreglo que define conversiones de tipo para los atributos del modelo.
     * Cuando Eloquent lee el atributo 'estado' desde la base de datos
     * (donde se almacena como un entero 0 o 1), lo convierte automáticamente
     * al tipo booleano de PHP (false o true). Esto permite comparaciones
     * directas como `if ($usuario->estado)` sin conversiones manuales.
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // RELACIONES ELOQUENT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Define la relación de pertenencia del usuario hacia un Rol.
     *
     * TIPO DE RELACIÓN: Muchos a uno (N:1). Múltiples usuarios pueden
     * tener el mismo rol, pero cada usuario pertenece a un único rol.
     *
     * PROPÓSITO: Permite acceder al objeto Rol asociado al usuario mediante
     * la propiedad dinámica `$usuario->rol`, que ejecuta la consulta
     * SELECT sobre la tabla 'roles' de forma diferida (lazy loading).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *         Instancia de la relación que permite cargar el Rol asociado.
     */
    public function rol()
    {
        // belongsTo() declara que este modelo es el lado "hijo" de la relación.
        // Primer argumento: clase del modelo padre (Rol).
        // Segundo argumento: nombre de la clave foránea en la tabla 'usuarios'.
        // Tercer argumento: nombre de la clave primaria en la tabla 'roles'.
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    /**
     * Define la relación de posesión del usuario hacia un Empleado.
     *
     * TIPO DE RELACIÓN: Uno a uno (1:1). Un usuario puede tener como máximo
     * un registro de empleado asociado, y un empleado está vinculado a un
     * único usuario del sistema.
     *
     * PROPÓSITO: Permite acceder al objeto Empleado vinculado al usuario
     * mediante la propiedad dinámica `$usuario->empleado`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     *         Instancia de la relación que permite cargar el Empleado asociado.
     */
    public function empleado()
    {
        // hasOne() declara que este modelo es el lado "padre" de la relación.
        // Primer argumento: clase del modelo hijo (Empleado).
        // Segundo argumento: nombre de la clave foránea en la tabla 'empleados'.
        // Tercer argumento: nombre de la clave local en la tabla 'usuarios'.
        return $this->hasOne(Empleado::class, 'usuario_id', 'usuario_id');
    }
}
