<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpVerificationNotification extends Notification
{
    public function __construct(
        private readonly string $code,
        private readonly string $purpose,
        private readonly int $expirationMinutes
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $operation = match ($this->purpose) {
            'EMAIL_VERIFICATION' =>
            'verificar tu correo electrónico',

            'PASSWORD_RESET' =>
            'recuperar tu contraseña',

            'PASSWORD_CHANGE' =>
            'cambiar tu contraseña',

            default =>
            'autorizar una operación de seguridad',
        };

        return (new MailMessage)
            ->subject('SIGAC | Código de seguridad')
            ->greeting('Hola,')
            ->line(
                "Has solicitado {$operation} en SIGAC."
            )
            ->line(
                "Tu código de seguridad es: {$this->code}"
            )
            ->line(
                "Este código tiene una vigencia de "
                    . "{$this->expirationMinutes} minutos."
            )
            ->line(
                'No compartas este código con ninguna persona.'
            )
            ->line(
                'Si no solicitaste esta operación, '
                    . 'ignora este mensaje.'
            )
            ->salutation('Equipo de seguridad de SIGAC');
    }
}
