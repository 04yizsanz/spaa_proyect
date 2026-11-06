# Referencia: modelos

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Las relaciones se describen según el código; no se ejecutaron consultas.

Este documento completa la [capa 7 de `backend/capas.md`](../capas.md#11-capa-7--modelo). `Cita` y los métodos de permisos de `Usuario` están explicados allí, junto con las relaciones de `Usuario` y el alcance de `Pago` (sección 11.5).

## Elementos comunes de un modelo

| Elemento | Significado en Asha Spa |
| --- | --- |
| `extends Model` | Clase Eloquent: cada objeto representa una fila de la tabla. |
| `use HasFactory;` | Permite crear registros de prueba con una *factory*. El proyecto solo tiene `UserFactory`, que apunta a un modelo inexistente; ningún modelo de negocio tiene factory propia. |
| `use SoftDeletes;` | `delete()` llena `deleted_at` y las consultas normales ocultan esa fila. |
| `$table` | Nombre real de la tabla. Necesario porque Laravel deduciría otro (por ejemplo, `rols` para `Rol`). |
| `$primaryKey` | Llave primaria. Necesario porque no se llama `id`. |
| `$fillable` | Columnas que acepta `create()`/`update()`/`fill()` desde un arreglo. Las demás se ignoran: protección contra **asignación masiva**. |
| `$casts` | Conversión automática al leer y guardar: `boolean`, `integer`, `date`, `datetime`, `decimal:2` (texto con dos decimales). |
| Relaciones | `belongsTo` (“pertenece a”: la llave está en esta tabla), `hasMany` (“tiene muchos”), `hasOne` (“tiene uno”: la llave está en la otra tabla). Los argumentos son la clase relacionada, la llave foránea y la llave local o del dueño. |

---

## `Usuario`: autenticación, propiedades y traits

```php
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'usuario_id';

    // Decirle a Laravel qué columna usar para autenticación
    public function getAuthIdentifierName(): string
    {
        return 'usuario_id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->usuario_id;
    }

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'documento',
        'email',
        'telefono',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
```

Fuente: `backend/app/Models/Usuario.php` L10–46.

| Elemento | Detalle |
| --- | --- |
| `extends Authenticatable` | `Illuminate\Foundation\Auth\User`, renombrado con `as Authenticatable` (L6). Da al modelo lo necesario para iniciar sesión (contraseña, “recordarme”). |
| `HasApiTokens` | Trait de Sanctum: aporta `createToken()`, `tokens()` y `currentAccessToken()`, usados en `ApiAuthController`. |
| `Notifiable` | Permite enviar notificaciones; ningún código lo usa (no hay envío de correos). |
| `$table`, `$primaryKey` | `usuarios`, `usuario_id`. |
| `getAuthIdentifierName(): string` (L18–21) | Devuelve `'usuario_id'`: le indica a Laravel qué columna identifica al usuario autenticado (por ejemplo, para guardar el identificador en la sesión). El comentario de L17 lo explica. |
| `getAuthIdentifier(): mixed` (L23–26) | Devuelve el valor de `usuario_id` del objeto. Laravel lo usa al iniciar sesión; `AppServiceProvider` lo usa en el limitador `api` (L143). |
| `$fillable` | Incluye `password` y `estado`. **No** incluye `administrador_unico`, que solo se calcula en `booted()` (sección 11.3 de `capas.md`). |
| `$hidden` | `password` y `remember_token` no aparecen al convertir el usuario a JSON (`UsuarioController`, `ApiAuthController::resumen` no los incluye de todos modos). |
| `$casts` | `estado` como booleano. |

Por qué sobrescribir los dos métodos de identificación si `$primaryKey` ya es `usuario_id`: el comentario del código (L17) solo indica la intención (“qué columna usar para autenticación”); la clase base de Laravel ya usaría la llave primaria. Junto con el comentario de `AuthController.php` L63 (“evita problemas con primary key personalizada”), sugiere que se agregaron como medida preventiva. No se verificó si son necesarios.

---

## `Rol`

```php
class Rol extends Model
{
    use HasFactory; // se encarga de crear datos de prueba

    protected $table = "roles"; // escribimos como se llama la tabla en la DB

    protected $primaryKey = "rol_id"; // definimos la clave primaria

    protected $fillable = [ // definimos los campos
        'nombre',
        'descripcion',
        'estado', // 1, 0
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function usuario()
    {
        return $this->hasMany(Usuario::class, 'rol_id', 'rol_id');
    }
}
```

Fuente: `backend/app/Models/Rol.php` L8–30.

| Elemento | Detalle |
| --- | --- |
| Responsabilidad | Representar un rol de acceso (`roles`). Sus permisos **no** están en la base de datos sino en `config/permisos.php`, por nombre. |
| `$fillable` | `nombre`, `descripcion`, `estado`. |
| `$casts` | `estado` booleano. |
| `usuario()` | `hasMany`: un rol tiene muchos usuarios (`usuarios.rol_id`). El nombre en singular no cambia su comportamiento. Ningún código la llama; `MakeAdministrator` usa `whereHas('rol', ...)` desde `Usuario`. |
| Sin `SoftDeletes` | Un rol eliminado se borra físicamente. |

## `Cliente`

```php
class Cliente extends Model
{
    use HasFactory; 
    use SoftDeletes;



    protected $table = "clientes"; // Especificamos el nombre de la tabla en la BD

    protected $primaryKey = "cliente_id"; // Definimos la clave primaria

    protected $fillable = [ // Definimos los campos
        "usuario_id",
        "fecha_nacimiento",
        "preferencias"
    ];

    protected $casts = [
        "fecha_nacimiento" => "date"
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "usuario_id", "usuario_id"); // Un cliente pertenece a un usuario
    }

    public function reserva()
    {
        return $this->hasMany(Reserva::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas reservas
    }

    public function venta()
    {
        return $this->hasMany(Venta::class, "cliente_id", "cliente_id"); // Un cliente tiene muchas ventas
    }

    /**
     * Clientes que el usuario puede consultar: todos con 'clientes.ver';
     * solo su propio perfil con 'clientes.propio'; ninguno en otro caso.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('clientes.ver')) {
            return $query;
        }

        if ($usuario->tienePermiso('clientes.propio')) {
            return $query->where($this->qualifyColumn('usuario_id'), $usuario->usuario_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
```

Fuente: `backend/app/Models/Cliente.php` L10–62.

| Elemento | Detalle |
| --- | --- |
| Responsabilidad | La ficha de cliente: datos que no están en `usuarios` (fecha de nacimiento y preferencias). |
| `$fillable` | `usuario_id`, `fecha_nacimiento`, `preferencias`. |
| `$casts` | `fecha_nacimiento` como fecha. |
| `usuario()` (L31–34) | `belongsTo`: cada ficha pertenece a un usuario. Se usa para mostrar nombre y apellido del cliente (`$cita->cliente->usuario->nombre`). |
| `reserva()`, `venta()` (L36–44) | `hasMany` hacia `Reserva` y `Venta`, clases que no existen. Ningún código las llama (CAP-06). |
| `scopeVisiblePara` (L50–61) | Con `clientes.ver`, todas; con `clientes.propio`, solo la ficha cuyo `usuario_id` es el del usuario (a diferencia de Cita y Factura, compara por usuario, no por `cliente_id`); en otro caso, ninguna. |
| `SoftDeletes` | Sí. |

## `Empleado`

```php
class Empleado extends Model
{
    use HasFactory;
    use SoftDeletes;


    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'empleados';

    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'empleado_id';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'correo',
        'telefono',
        'rol',
        'salario',
        'fecha_contratacion',
        'disponibilidad',
    ];

    /**
     * Conversión de tipos.
     */
    protected $casts = [
        'salario' => 'decimal:2',
        'fecha_contratacion' => 'date',
        'disponibilidad' => 'boolean',
    ];

    /**
     * Citas asignadas a este empleado.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'empleado_id', 'empleado_id');
    }
}
```

Fuente: `backend/app/Models/Empleado.php` L12–59.

| Elemento | Detalle |
| --- | --- |
| Responsabilidad | Ficha del personal del spa. No está vinculada a una cuenta de usuario. |
| `$fillable` | Datos personales, `rol` (cargo), `salario`, `fecha_contratacion`, `disponibilidad`. |
| `$casts` | `salario` con dos decimales, `fecha_contratacion` como fecha, `disponibilidad` booleana. |
| `citas()` | `hasMany` hacia `Cita` por `empleado_id`. Ningún código la llama. |
| `SoftDeletes` | Sí. |

## `Servicio`

```php
class Servicio extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'servicios';
    /**
     * Clave primaria personalizada.
     */
    protected $primaryKey = 'servicio_id';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'duracion_min',
        'precio',
        'descripcion',
        'activo',
    ];

    /**
     * Conversión de tipos.
     */
    protected $casts = [
        'duracion_min' => 'integer',
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Citas que solicitan este servicio.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'servicio_id', 'servicio_id');
    }
}
```

Fuente: `backend/app/Models/Servicio.php` L10–51.

| Elemento | Detalle |
| --- | --- |
| Responsabilidad | Un servicio del catálogo. |
| `$fillable` | `nombre`, `duracion_min`, `precio`, `descripcion`, `activo`. |
| `$casts` | `duracion_min` entero, `precio` con dos decimales, `activo` booleano. |
| `citas()` | `hasMany` hacia `Cita`. Ningún código la llama. |
| `SoftDeletes` | Sí. |

## `Pago`: propiedades y relación

```php
class Pago extends Model
{
    use HasFactory;

    protected $table = "pagos";
    protected $primaryKey = "pago_id";

    protected $fillable = [
        'monto',
        'metodo',
        'fecha_hora',
        'estado',
        'codigo_cita',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'codigo_cita', 'codigo_cita');
    }
```

Fuente: `backend/app/Models/Pago.php` L12–35.

| Elemento | Detalle |
| --- | --- |
| `$fillable` | `monto`, `metodo`, `fecha_hora`, `estado`, `codigo_cita`. |
| `$casts` | `fecha_hora` como fecha y hora; `monto` con dos decimales. |
| `cita()` | `belongsTo(Cita::class, 'codigo_cita', 'codigo_cita')`: la relación usa el **código** de la cita, no su llave primaria. Ningún código la llama. |
| Sin `SoftDeletes` | Comentario L10 (“Registro financiero”); la eliminación la impide `PagoPolicy`. |

## `Factura`

```php
class Factura extends Model
{
    use HasFactory;

    protected $table = "facturas";
    protected $primaryKey = "factura_id";

    protected $fillable = [
        'fecha_hora',
        'subtotal',
        'impuestos',
        'total',
        'pdf_url',
        'cliente_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente_id');
    }

    public function servicios()
    {
        return $this->hasMany(FacturaServicio::class, 'factura_id', 'factura_id');
    }

    /**
     * Facturas que el usuario puede consultar: todas con 'facturas.ver_todas';
     * las propias con 'facturas.propias'; ninguna en otro caso.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('facturas.ver_todas')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('facturas.propias') && $clienteId !== null) {
            return $query->where($this->qualifyColumn('cliente_id'), $clienteId);
        }

        return $query->whereRaw('1 = 0');
    }
}
```

Fuente: `backend/app/Models/Factura.php` L13–64.

| Elemento | Detalle |
| --- | --- |
| `$fillable` | Incluye `subtotal` y `total`: el modelo los acepta; quien impide que el cliente los envíe es el request (`prohibited`) y el servicio, que construye los datos. |
| `$casts` | `fecha_hora`, y los tres importes con dos decimales. |
| `cliente()` | `belongsTo` hacia `Cliente`. Ningún código la llama. |
| `servicios()` | `hasMany` hacia `FacturaServicio`: las líneas del detalle. La usa `FacturaService::recalcularTotales`. El nombre dice “servicios”, pero devuelve líneas de detalle, no objetos `Servicio`. |
| `scopeVisiblePara` (L50–63) | `facturas.ver_todas`, o `facturas.propias` con `cliente_id` del usuario; si no, ninguna. |

## `FacturaServicio`

```php
class FacturaServicio extends Model
{
    use HasFactory;

    protected $table = 'factura_servicio';

    public $incrementing = false;
    protected $primaryKey = null;
    protected $fillable = [
        'factura_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id', 'factura_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }

    /**
     * Líneas de factura visibles: las mismas reglas que la factura.
     */
    public function scopeVisiblePara(Builder $query, Usuario $usuario): Builder
    {
        if ($usuario->tienePermiso('facturas.ver_todas')) {
            return $query;
        }

        $clienteId = $usuario->cliente?->cliente_id;

        if ($usuario->tienePermiso('facturas.propias') && $clienteId !== null) {
            return $query->whereIn(
                $this->qualifyColumn('factura_id'),
                Factura::query()->select('factura_id')->where('cliente_id', $clienteId)
            );
        }

        return $query->whereRaw('1 = 0');
    }
}
```

Fuente: `backend/app/Models/FacturaServicio.php` L9–59.

| Elemento | Detalle |
| --- | --- |
| Responsabilidad | Una línea de detalle: cuántas veces se cobra un servicio en una factura y a qué precio. |
| `$incrementing = false`, `$primaryKey = null` | La tabla no tiene una llave autoincremental de un campo. Por eso `FacturaServicioRepository` sobrescribe `getById`, `update` y `delete`. Métodos de Eloquent que dependen de la llave, como `fresh()` o `$linea->update()`, no son aplicables a este modelo; el código no los usa sobre líneas. |
| `$casts` | `cantidad` entero; `precio_unitario` con dos decimales. |
| `factura()`, `servicio()` | `belongsTo`. Ningún código las llama. |
| `scopeVisiblePara` (L42–58) | Mismas reglas que la factura: con `facturas.propias`, filtra las líneas cuya `factura_id` pertenece a una factura del cliente (subconsulta sobre `Factura`). |

## `Proveedor`, `Producto`, `MovimientoInventario`

```php
class Proveedor extends Model
{
     use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
    use SoftDeletes;
     protected $table="proveedor";
     protected $primaryKey = "proveedor_id";
     protected $fillable = [ 
        "nombre",
        "contacto",
        "email",
        "registro_tributario",

     ];

         public function producto(){
            return $this->hasMany(Producto::class, 'proveedor_id', 'proveedor_id');
         }

         
}
```

Fuente: `backend/app/Models/Proveedor.php` L9–28.

```php
    class Producto extends Model
    {
        use HasFactory; //Hasfactory se va a encargar de crear datos de prueba
       use SoftDeletes;
        protected $table="producto";
        protected $primaryKey = "producto_id";
        protected $fillable = [ 
            "nombre",
            "cantidad",
            "precio",
            "fecha_registro",
            "proveedor_id",

        ];


            public function proveedor(){
                return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
            }
            public function movimiento_inventario()
        {
            return $this->hasMany(MovimientoInventario::class, 'producto_id', 'producto_id');
        }
            
            // hasMany: muchos 
    // hasOne: relacion de 1 a 1 
    // belognsTo: tiene un 
            
    }
```

Fuente: `backend/app/Models/Producto.php` L9–37.

```php
class MovimientoInventario extends Model
{
    use HasFactory;
    use SoftDeletes;
   
    protected $table = 'movimientoinventario';   
       protected $primaryKey = "movimiento_id";

    protected $fillable = [
        'tipo',
        'cantidad',
        'fecha_hora',
        'motivo',
        'producto_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'cantidad' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
```

Fuente: `backend/app/Models/MovimientoInventario.php` L9–34.

| Modelo | Tabla y llave | `$fillable` | `$casts` | Relaciones | Uso de las relaciones |
| --- | --- | --- | --- | --- | --- |
| `Proveedor` | `proveedor`, `proveedor_id` | `nombre`, `contacto`, `email`, `registro_tributario` | Ninguno | `producto()`: `hasMany` hacia `Producto` | Ningún código la llama |
| `Producto` | `producto`, `producto_id` | `nombre`, `cantidad`, `precio`, `fecha_registro`, `proveedor_id` | **Ninguno**: `cantidad` y `precio` llegan como los entregue la base de datos | `proveedor()`: `belongsTo`; `movimiento_inventario()`: `hasMany` | Ningún código las llama |
| `MovimientoInventario` | `movimientoinventario`, `movimiento_id` | `tipo`, `cantidad`, `fecha_hora`, `motivo`, `producto_id` | `fecha_hora` como fecha y hora; `cantidad` entero | `producto()`: `belongsTo` | Ningún código la llama |

Los tres usan `SoftDeletes`. Los comentarios de `Producto.php` L33–35 son notas de estudio sobre los tipos de relación y no afectan el comportamiento.
