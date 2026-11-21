<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestablecerContrasenaRequest;
use App\Http\Requests\Auth\SolicitarRecuperacionRequest;
use App\Services\RecuperacionContrasenaService;
use Illuminate\Http\Request;

/**
 * Pantallas de "olvidé mi contraseña" (solo para personas sin sesión).
 *
 *  1. GET  /olvide-contrasena                → formulario para escribir el correo
 *  2. POST /olvide-contrasena                → envía el enlace por correo
 *  3. GET  /restablecer-contrasena/{token}   → formulario de nueva contraseña
 *  4. POST /restablecer-contrasena           → guarda la nueva contraseña
 */
class RecuperacionContrasenaController extends Controller
{
    public function __construct(
        protected RecuperacionContrasenaService $recuperacionService
    ) {}

    public function mostrarSolicitud()
    {
        return view('auth.forgot-password');
    }

    public function enviarEnlace(SolicitarRecuperacionRequest $request)
    {
        $this->recuperacionService->enviarEnlace($request->validated('email'));

        // Mismo mensaje exista o no la cuenta: no se revela qué correos están registrados.
        return back()->with('enlace_enviado', true)->withInput($request->only('email'));
    }

    public function mostrarRestablecer(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function restablecer(RestablecerContrasenaRequest $request)
    {
        $datos = $request->validated();

        $resultado = $this->recuperacionService->restablecer($datos['email'], $datos['token'], $datos['password']);

        if ($resultado !== RecuperacionContrasenaService::RESTABLECIDA) {
            return back()->withInput($request->only('email'))
                ->with('error', 'El enlace no es válido o ya venció. Solicita uno nuevo.');
        }

        return redirect()->route('login')
            ->with('success', 'Tu contraseña se cambió. Ya puedes iniciar sesión con la nueva.');
    }
}
