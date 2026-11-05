# Referencia: comandos de consola y configuración de permisos

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. El comando no se ejecutó.

## 1. `MakeAdministrator` — comando `app:make-administrator`

**Archivo:** `backend/app/Console/Commands/MakeAdministrator.php` · **Tipo:** clase que extiende `Illuminate\Console\Command`.

**Responsabilidad.** Asignar el rol Administrador a **una cuenta ya registrada**, garantizando que solo exista un administrador. Resuelve un problema concreto: el registro público solo crea Clientes, y sin este comando no habría forma de crear el primer administrador.

**Flujo.** Es una operación sin ruta, sin request, sin controlador y sin servicio: el comando trabaja directamente con los modelos `Usuario` y `Rol` (sección 1.1 de `capas.md`). Se ejecuta desde la consola del contenedor, como indica `README.md` L13: `docker compose exec app php artisan app:make-administrator correo@ejemplo.com`.

```php
class MakeAdministrator extends Command
{
    protected $signature = 'app:make-administrator {email : Correo del usuario registrado que será administrador}';

    protected $description = 'Asigna el único rol de administrador a un usuario existente';

    public function handle(): int
    {
        try {
            $usuario = DB::transaction(function (): Usuario {
                if (Usuario::whereHas('rol', fn ($query) => $query->where('nombre', 'Administrador'))
                    ->lockForUpdate()
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'administrador' => 'Ya existe una cuenta de administrador.',
                    ]);
                }

                $usuario = Usuario::where('email', $this->argument('email'))->lockForUpdate()->first();

                if (! $usuario) {
                    throw ValidationException::withMessages([
                        'email' => 'No existe un usuario registrado con ese correo.',
                    ]);
                }

                $rol = Rol::firstOrCreate(
                    ['nombre' => 'Administrador'],
                    ['descripcion' => 'Administrador general del sistema.', 'estado' => true]
                );

                $usuario->rol_id = $rol->rol_id;
                $usuario->save();

                return $usuario;
            });
        } catch (ValidationException $exception) {
            $this->error($exception->errors()[array_key_first($exception->errors())][0]);

            return self::FAILURE;
        }

        $this->info("{$usuario->email} ahora es el administrador del sistema.");

        return self::SUCCESS;
    }
```

Fuente: `backend/app/Console/Commands/MakeAdministrator.php` L11–56.

| Elemento | Tipo | Significado |
| --- | --- | --- |
| `$signature` (L13) | Propiedad | Define el nombre del comando (`app:make-administrator`) y un argumento obligatorio `{email}` con su descripción. |
| `$description` (L15) | Propiedad | Texto que aparece en `php artisan list`. |
| `handle(): int` (L17) | Método | Laravel lo ejecuta al correr el comando. Devuelve un código de salida: `self::SUCCESS` (0) o `self::FAILURE` (1). |

| Bloque | Qué hace |
| --- | --- |
| L20 | Abre una transacción. La función anónima declara que devuelve un `Usuario`. |
| L21–27 | `Usuario::whereHas('rol', fn ($query) => ...)` busca usuarios cuyo rol se llame `Administrador`; `lockForUpdate()` bloquea esas filas durante la transacción; `exists()` responde si hay alguno. Si ya existe, lanza `ValidationException` con el mensaje “Ya existe una cuenta de administrador.” |
| L29 | `$this->argument('email')` lee el correo escrito en la consola. Busca el usuario con ese correo, también con bloqueo. **No** filtra por usuarios activos. |
| L31–35 | Si no existe, lanza `ValidationException` (“No existe un usuario registrado con ese correo.”). |
| L37–40 | `Rol::firstOrCreate` busca el rol `Administrador` o lo crea activo con una descripción. |
| L42–43 | Asigna el rol y guarda. Al guardar se ejecuta el evento `saving` de `Usuario::booted`, que pone `administrador_unico = 1`; el índice único de esa columna es una barrera adicional en la base de datos. |
| L47–51 | `catch (ValidationException $exception)`: toma el primer mensaje del primer campo con error (`array_key_first`), lo muestra en rojo con `$this->error(...)` y termina con `FAILURE`. Si falla, la transacción se deshace. |
| L53–55 | Muestra en verde que el usuario ahora es administrador y termina con `SUCCESS`. |

| Errores contemplados | Resultado |
| --- | --- |
| Ya hay un administrador | Mensaje de error y código 1 |
| El correo no existe | Mensaje de error y código 1 |
| Otros errores (por ejemplo, de base de datos) | No se capturan: Laravel mostraría la excepción (no verificado) |

**Particularidades.** `ValidationException` es la excepción de validación de Laravel; aquí se usa como forma de transportar un mensaje, no porque haya un formulario. El comando no comprueba que la cuenta esté activa: si la cuenta asignada estuviera inactiva, no podría iniciar sesión hasta activarla (`UsuarioRepository::getByEmail` solo devuelve activos).

## 2. `routes/console.php`

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

Fuente: `backend/routes/console.php` L1–8.

Define el comando `inspire`, que viene con la plantilla de Laravel y muestra una cita aleatoria. No tiene relación con el negocio del spa. `MakeAdministrator` **no** se registra aquí: según el comportamiento predeterminado de Laravel, las clases de `app/Console/Commands` se descubren automáticamente. No se ejecutó `php artisan list` para confirmarlo.

## 3. `config/permisos.php` — estructura

**Archivo:** `backend/config/permisos.php`. Archivo de configuración de PHP: devuelve un arreglo que Laravel carga al arrancar y que se lee con `config('permisos.roles')`.

```php
return [

    'roles' => [

        'Administrador' => ['*'],

        // "Gestiona clientes y citas" (RolSeeder). Sin acceso financiero.
        'Recepcionista' => [
            'clientes.ver',
            'clientes.gestionar',
            'citas.ver_todas',
            'citas.gestionar',
            'servicios.ver',
            'empleados.ver',
            'empleados.ver_contacto',
        ],

        // Consultan la agenda sin modificarla. Sin salarios ni finanzas.
        'Estilista' => [
            'citas.ver_todas',
            'servicios.ver',
            'empleados.ver',
        ],

        'Esteticista' => [
            'citas.ver_todas',
            'servicios.ver',
            'empleados.ver',
        ],

        // Solo sus propios datos.
        'Cliente' => [
            'clientes.propio',
            'citas.propias',
            'pagos.propios',
            'facturas.propias',
            'servicios.ver',
            'empleados.ver',
        ],
    ],

];
```

Fuente: `backend/config/permisos.php` L49–90.

| Elemento | Significado |
| --- | --- |
| `'roles' => [...]` | Arreglo cuya llave es el **nombre exacto** del rol (columna `roles.nombre`) y cuyo valor es la lista de permisos. |
| `'Administrador' => ['*']` | El comodín `*` concede todos los permisos; lo interpreta `Usuario::tienePermiso` (`in_array('*', ...)`). |
| Lista de cada rol | Cadenas con forma `recurso.accion`. El catálogo completo, con la descripción de cada permiso, está en el comentario del archivo (L19–42). |
| Comentario L3–47 | Documenta la regla de denegación por defecto: un rol que no figure aquí no tiene permisos y su cuenta no puede operar; para habilitar un rol nuevo hay que agregarlo de forma explícita. También indica que la imposibilidad de eliminar registros financieros la aplican las policies, no este archivo. |

| Quién lee este archivo | Para qué |
| --- | --- |
| `Usuario::estaHabilitado` | Comprobar que el nombre del rol esté definido |
| `Usuario::permisos` | Obtener la lista de permisos del rol |

Este archivo **no** está en la base de datos: cambiar permisos exige editar el código y, si la configuración está en caché, limpiarla. La matriz rol × permiso y su comparación con lo que permite la web se documentarán en `seguridad/matriz-de-permisos.md`.
