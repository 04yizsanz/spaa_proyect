<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Correo con el enlace para crear una nueva contraseña.
 * Con MAIL_MAILER=log el correo no se envía: queda en storage/logs/laravel.log.
 */
class RestablecerContrasenaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function enlace(object $notifiable): string
    {
        return route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutos = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Recupera tu contraseña — Asha Spa')
            ->greeting("Hola, {$notifiable->nombre}")
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en Asha Spa.')
            ->action('Crear nueva contraseña', $this->enlace($notifiable))
            ->line("Este enlace vence en {$minutos} minutos y solo se puede usar una vez.")
            ->line('Si no fuiste tú, ignora este correo: tu contraseña seguirá siendo la misma.')
            ->salutation('Equipo de Asha Spa');
    }
}
