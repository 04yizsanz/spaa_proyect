<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Rol;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // MOSTRAR LOGIN
    // ─────────────────────────────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROCESAR LOGIN
    // ─────────────────────────────────────────────────────────────────────────
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

        // Login manual — evita problemas con primary key personalizada
        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        Log::info("Login exitoso: {$usuario->email} (ID: {$usuario->usuario_id})");

        return redirect()->intended(route('home'))
            ->with('success', "Bienvenid@, {$usuario->nombre} {$usuario->apellido}.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MOSTRAR REGISTRO
    // ─────────────────────────────────────────────────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROCESAR REGISTRO
    // ─────────────────────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:80'],
            'apellido'  => ['required', 'string', 'max:80'],
            'documento' => ['required', 'string', 'max:20', 'unique:usuarios,documento'],
            'email'     => ['required', 'email', 'max:150', 'unique:usuarios,email'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'apellido.required'  => 'El apellido es obligatorio.',
            'documento.required' => 'El número de documento es obligatorio.',
            'documento.unique'   => 'Este documento ya está registrado.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingresa un correo electrónico válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            // El registro público SIEMPRE crea una cuenta de cliente.
            // Los demás roles los asigna un administrador, nunca el visitante.
            $usuario = DB::transaction(function () use ($data) {
                $rol = Rol::firstOrCreate(
                    ['nombre' => 'Cliente'],
                    ['descripcion' => 'Cliente registrado que puede gestionar sus citas.', 'estado' => true]
                );

                // El UsuarioRepository cifra el password automáticamente
                $usuario = $this->usuarioService->create([
                    'rol_id'    => $rol->rol_id,
                    'nombre'    => $data['nombre'],
                    'apellido'  => $data['apellido'],
                    'documento' => $data['documento'],
                    'email'     => $data['email'],
                    'telefono'  => $data['telefono'] ?? null,
                    'password'  => $data['password'],
                    'estado'    => true,
                ]);

                // Ficha de cliente ligada al usuario
                Cliente::create(['usuario_id' => $usuario->usuario_id]);

                return $usuario;
            });

            Log::info("Registro exitoso: {$usuario->email} (ID: {$usuario->usuario_id})");

            // Loguear automáticamente tras el registro
            Auth::login($usuario);
            $request->session()->regenerate();

            return redirect()->route('home')
                ->with('success', "Bienvenid@, {$usuario->nombre}. Tu cuenta fue creada correctamente.");

        } catch (\Throwable $e) {
            Log::error('Error en registro: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Ocurrió un error al crear la cuenta. Por favor intenta de nuevo.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
