<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige que el usuario autenticado siga habilitado en cada petición:
 * usuario activo, con rol, rol activo y rol definido en config/permisos.php.
 *
 * Así, desactivar un usuario o un rol corta el acceso de inmediato, también
 * para sesiones abiertas y tokens ya emitidos.
 */
class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario === null || $usuario->estaHabilitado()) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'La cuenta o el rol no están habilitados.',
            ], 403);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Tu cuenta no está habilitada. Contacta al administrador.',
        ]);
    }
}
