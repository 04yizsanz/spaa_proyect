<?php

namespace App\Repositories;

use App\Interfaces\RecuperacionContrasenaInterface;
use App\Models\Usuario;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\Facades\Password;

/**
 * Guarda y valida los tokens de recuperación usando el repositorio de tokens
 * de Laravel, que ya resuelve lo delicado:
 *  - el token se guarda cifrado (hash), nunca en texto plano;
 *  - caduca a los 60 minutos y no se puede pedir otro antes de 60 segundos
 *    (config/auth.php → passwords.users.expire / throttle).
 */
class RecuperacionContrasenaRepository implements RecuperacionContrasenaInterface
{
    private function tokens(): TokenRepositoryInterface
    {
        return Password::broker()->getRepository();
    }

    public function crearToken(Usuario $usuario): string
    {
        return $this->tokens()->create($usuario);
    }

    public function tokenValido(Usuario $usuario, string $token): bool
    {
        return $this->tokens()->exists($usuario, $token);
    }

    public function enviadoRecientemente(Usuario $usuario): bool
    {
        return $this->tokens()->recentlyCreatedToken($usuario);
    }

    public function eliminarToken(Usuario $usuario): void
    {
        $this->tokens()->delete($usuario);
    }
}
