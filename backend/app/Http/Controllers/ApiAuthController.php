<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Autenticación de la API con tokens personales de Laravel Sanctum.
 *
 *  POST /api/login   (pública, con límite de intentos) → emite un token Bearer.
 *  POST /api/logout  → revoca el token usado en la petición.
 *  GET  /api/me      → datos básicos de la cuenta y sus permisos.
 */
class ApiAuthController extends Controller
{
    public function __construct(
        protected UsuarioService $usuarioService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'dispositivo' => ['nullable', 'string', 'max:100'],
        ]);

        // getByEmail solo devuelve usuarios activos.
        $usuario = $this->usuarioService->getByEmail($credenciales['email']);

        if (! $usuario || ! Hash::check($credenciales['password'], $usuario->password)) {
            return response()->json(['message' => 'Las credenciales no son válidas.'], 401);
        }

        if (! $usuario->estaHabilitado()) {
            return response()->json(['message' => 'La cuenta o el rol no están habilitados.'], 403);
        }

        $token = $usuario->createToken($credenciales['dispositivo'] ?? 'api');

        Log::info('Inicio de sesión en la API', ['usuario_id' => $usuario->usuario_id]);

        return response()->json([
            'token' => $token->plainTextToken,
            'tipo' => 'Bearer',
            'usuario' => $this->resumen($usuario),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->resumen($request->user()));
    }

    private function resumen(Usuario $usuario): array
    {
        return [
            'usuario_id' => $usuario->usuario_id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'rol' => $usuario->rol?->nombre,
            'cliente_id' => $usuario->cliente?->cliente_id,
            'permisos' => $usuario->permisos(),
        ];
    }
}
