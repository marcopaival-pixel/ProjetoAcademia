<?php

namespace App\Listeners;

use App\Models\LogEnvioEmail;
use App\Models\User;
use App\Notifications\ResetPasswordCustom;
use App\Support\MailSendType;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;

class MailNotificationAuditListener
{
    public function handleSent(NotificationSent $event): void
    {
        if ($event->channel !== 'mail' || ! $event->notifiable instanceof User) {
            return;
        }

        $user = $event->notifiable;
        $email = trim((string) $user->email);
        $tipo = $this->resolveTipo($event->notification);

        LogEnvioEmail::create([
            'empresa_id' => $user->academy_company_id,
            'usuario_id' => $user->id,
            'tipo_envio' => $tipo,
            'email_destino' => $email !== '' ? $email : '-',
            'assunto' => $this->guessSubject($event->notification),
            'mensagem' => 'Canal notificação: '.class_basename($event->notification),
            'status' => LogEnvioEmail::STATUS_ENVIADO,
            'erro' => null,
            'ip' => request()?->ip(),
            'data_envio' => now(),
        ]);
    }

    public function handleFailed(NotificationFailed $event): void
    {
        if ($event->channel !== 'mail' || ! $event->notifiable instanceof User) {
            return;
        }

        $user = $event->notifiable;
        $email = trim((string) $user->email);
        $tipo = $this->resolveTipo($event->notification);
        $err = isset($event->data['exception']) && $event->data['exception'] instanceof \Throwable
            ? mb_substr($event->data['exception']->getMessage(), 0, 2000)
            : (isset($event->data['message']) ? (string) $event->data['message'] : 'Falha no canal mail');

        LogEnvioEmail::create([
            'empresa_id' => $user->academy_company_id,
            'usuario_id' => $user->id,
            'tipo_envio' => $tipo,
            'email_destino' => $email !== '' ? $email : '-',
            'assunto' => $this->guessSubject($event->notification),
            'mensagem' => 'Canal notificação: '.class_basename($event->notification),
            'status' => LogEnvioEmail::STATUS_FALHA,
            'erro' => mb_substr($err, 0, 2000),
            'ip' => request()?->ip(),
            'data_envio' => now(),
        ]);
    }

    private function resolveTipo(object $notification): string
    {
        return $notification instanceof ResetPasswordCustom
            ? MailSendType::PASSWORD_RESET
            : MailSendType::NOTIFICATION;
    }

    private function guessSubject(object $notification): string
    {
        return class_basename($notification);
    }
}
