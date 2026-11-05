<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe la ruta a cuentas con el permiso 'sistema.administrar'
 * (Gate 'administrar-sistema'). No compara nombres de rol directamente:
 * el usuario y su rol deben estar activos y el rol debe tener el permiso
 * en config/permisos.php.
 */
class EnsureAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->can('administrar-sistema')) {
            return response()->json(['message' => 'Se requiere el rol de administrador.'], 403);
        }

        return $next($request);
    }
}
