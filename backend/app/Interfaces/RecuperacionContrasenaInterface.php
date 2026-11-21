<?php

namespace App\Interfaces;

use App\Models\Usuario;

/**
 * Acceso a los tokens de recuperación de contraseña (tabla password_reset_tokens).
 */
interface RecuperacionContrasenaInterface
{
    /** Crea (o reemplaza) el token de la cuenta y devuelve el token en texto plano. */
    public function crearToken(Usuario $usuario): string;

    /** Indica si el token es correcto y no ha caducado. */
    public function tokenValido(Usuario $usuario, string $token): bool;

    /** Indica si ya se envió un enlace hace muy poco (evita envíos repetidos). */
    public function enviadoRecientemente(Usuario $usuario): bool;

    /** Elimina el token de la cuenta (después de usarlo). */
    public function eliminarToken(Usuario $usuario): void;
}
