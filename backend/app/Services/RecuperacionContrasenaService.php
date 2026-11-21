<?php

namespace App\Services;

use App\Interfaces\RecuperacionContrasenaInterface;
use App\Interfaces\UsuarioInterface;
use App\Models\Usuario;
use App\Notifications\RestablecerContrasenaNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Reglas de negocio de "olvidé mi contraseña".
 *
 * Seguridad:
 *  - Nunca se revela si un correo está registrado: la respuesta es la misma
 *    exista o no la cuenta (evita que alguien averigüe qué correos usan el spa).
 *  - Solo las cuentas ACTIVAS pueden recuperar la contraseña.
 *  - El token caduca y se elimina después de usarlo (un solo uso).
 *  - Al cambiar la contraseña se renueva el remember_token, lo que cierra las
 *    sesiones "recordadas" en otros dispositivos.
 */
class RecuperacionContrasenaService
{
    public const ENLACE_INVALIDO = 'enlace_invalido';
    public const RESTABLECIDA    = 'restablecida';

    public function __construct(
        protected RecuperacionContrasenaInterface $recuperacionRepository,
        protected UsuarioInterface $usuarioRepository
    ) {}

    /**
     * Envía el enlace de recuperación si el correo pertenece a una cuenta activa.
     * No devuelve nada a propósito: el controlador muestra siempre el mismo mensaje.
     */
    public function enviarEnlace(string $email): void
    {
        $usuario = $this->buscarCuentaActiva($email);

        if (! $usuario || $this->recuperacionRepository->enviadoRecientemente($usuario)) {
            return;
        }

        $token        = $this->recuperacionRepository->crearToken($usuario);
        $notificacion = new RestablecerContrasenaNotification($token);

        $usuario->notify($notificacion);

        // Solo en desarrollo con MAIL_MAILER=log: deja el enlace en una línea
        // fácil de encontrar en storage/logs/laravel.log para poder probar.
        if (app()->environment('local') && config('mail.default') === 'log') {
            Log::info('[Recuperación de contraseña] Enlace para ' . $usuario->email . ': ' . $notificacion->enlace($usuario));
        }
    }

    /**
     * Cambia la contraseña si el token es válido.
     *
     * @return string self::RESTABLECIDA o self::ENLACE_INVALIDO
     */
    public function restablecer(string $email, string $token, string $nuevaContrasena): string
    {
        $usuario = $this->buscarCuentaActiva($email);

        if (! $usuario || ! $this->recuperacionRepository->tokenValido($usuario, $token)) {
            return self::ENLACE_INVALIDO;
        }

        // UsuarioRepository::update cifra la contraseña.
        $this->usuarioRepository->update([
            'password'       => $nuevaContrasena,
        ], $usuario->usuario_id);

        $usuario->forceFill(['remember_token' => Str::random(60)])->save();

        $this->recuperacionRepository->eliminarToken($usuario);

        event(new PasswordReset($usuario));

        return self::RESTABLECIDA;
    }

    private function buscarCuentaActiva(string $email): ?Usuario
    {
        return $this->usuarioRepository->getByEmail(trim($email));
    }
}
