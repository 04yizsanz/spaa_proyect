# Referencia: controladores web de autenticación

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Nada se ejecutó.

Este documento completa la sección 13 de [`backend/capas.md`](../capas.md#13-flujo-de-la-interfaz-web), donde están `CitaWebController`, `AuthController::register`, `HomeController` y los listados web. Aquí se documentan los demás métodos de `AuthController`.

## `AuthController`

**Archivo:** `backend/app/Http/Controllers/Web/AuthController.php` · **Rutas:** `backend/routes/web.php` L29–39.

**Responsabilidad.** Mostrar y procesar los formularios de inicio de sesión y registro, y cerrar la sesión. Trabaja con la **sesión** de Laravel, no con tokens.

### Constructor

```php
class AuthController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}
```

Fuente: `backend/app/Http/Controllers/Web/AuthController.php` L16–20.

Recibe `UsuarioService` por inyección de dependencias. Es el único controlador web que usa un servicio: lo necesita para buscar usuarios activos (`getByEmail`) y para crear la cuenta en el registro (`create`, que cifra la contraseña en el repositorio).

### `showLogin()`

```php
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }
```

Fuente: `backend/app/Http/Controllers/Web/AuthController.php` L25–31.

| Aspecto | Detalle |
| --- | --- |
| Nombre | “Mostrar login”. |
| Ruta | `GET /login`, con middleware `guest`. |
| Parámetros | Ninguno. |
| L27–29 | `Auth::check()` es `true` si ya hay una sesión; entonces redirige al inicio. Con el middleware `guest` esta comprobación es redundante, pero no cambia el resultado. |
| L30 | Devuelve la vista `frontend/resources/views/auth/login.blade.php`. |
| Devuelve | Una vista o una redirección. |

### `login(Request $request)`

```php
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar usuario activo por email usando el servicio
        $usuario = $this->usuarioService->getByEmail($request->email);

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.']);
        }

        // Rol inactivo, desconocido o ausente: la cuenta no puede operar (denegación por defecto).
        if (! $usuario->estaHabilitado()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta no está habilitada. Contacta al administrador.']);
        }

        // Login manual — evita problemas con primary key personalizada
        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        // Solo el identificador: el correo es un dato personal y no se registra en los logs.
        Log::info('Inicio de sesión web', ['usuario_id' => $usuario->usuario_id]);

        return redirect()->intended(route('home'))
            ->with('success', "Bienvenid@, {$usuario->nombre} {$usuario->apellido}.");
    }
```

Fuente: `backend/app/Http/Controllers/Web/AuthController.php` L36–72.

| Aspecto | Detalle |
| --- | --- |
| Ruta | `POST /login`, con `guest` y `throttle:login` (5 intentos por minuto por correo e IP; 20 por IP). |
| Parámetro | `$request`: correo, contraseña y, opcionalmente, la casilla `remember`. |
| L38–45 | Valida correo con formato válido y contraseña. Si falla, redirige al formulario con los mensajes en español. |
| L48 | `$usuario`: resultado de `UsuarioService::getByEmail` → `UsuarioRepository::getByEmail`, que **solo devuelve usuarios activos**. Es la única capa del flujo web de login que pasa por servicio, interfaz y repositorio. |
| L50–54 | Si no hay usuario o `Hash::check` no confirma la contraseña, vuelve al formulario conservando solo el correo (`only('email')`) y con un error genérico: no indica si falló el correo o la contraseña. |
| L57–61 | Si la cuenta no está habilitada (`estaHabilitado`: rol inexistente, inactivo o fuera de `config/permisos.php`), vuelve con otro mensaje. |
| L64 | `Auth::login($usuario, $request->boolean('remember'))` inicia sesión; el segundo argumento activa “recordarme” si la casilla vino marcada. El comentario L63 explica que se usa el inicio manual por la llave primaria personalizada. |
| L65 | `session()->regenerate()` cambia el identificador de sesión para que no se pueda reutilizar uno anterior (protección contra la fijación de sesión). |
| L68 | Registra el inicio de sesión solo con el identificador; el comentario L67 indica que el correo es un dato personal. |
| L70–71 | `redirect()->intended(route('home'))` lleva a la página que el usuario intentaba abrir antes de que le pidieran iniciar sesión, o al inicio si no había ninguna. |
| Devuelve | Redirección. |
| Diferencia con la API | `ApiAuthController::login` responde 401/403 en JSON y emite un token; aquí se redirige con errores en la sesión. |

### `showRegister()`

```php
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }
```

Fuente: `backend/app/Http/Controllers/Web/AuthController.php` L77–84.

Igual que `showLogin`, con la vista `auth.register`. Ruta `GET /register` con `guest`.

### `logout(Request $request)`

```php
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
```

Fuente: `backend/app/Http/Controllers/Web/AuthController.php` L170–178.

| Aspecto | Detalle |
| --- | --- |
| Ruta | `POST /logout`, solo con `auth` (sin `cuenta.activa`). |
| L172 | `Auth::logout()` saca al usuario de la sesión. |
| L173 | `invalidate()` borra los datos de la sesión. |
| L174 | `regenerateToken()` genera un nuevo token CSRF, para que los formularios abiertos antes del cierre ya no sirvan. |
| L176–177 | Redirige al login con un mensaje. |
| Por qué es `POST` | Un `GET` podría dispararse desde un enlace o una imagen de otro sitio; con `POST` el formulario debe incluir el token CSRF. |
