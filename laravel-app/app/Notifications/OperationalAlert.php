<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperationalAlert extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $level = $this->data['level'] ?? 'info';
        $title = $this->data['title'] ?? 'Alerta Operacional';
        
        $mail = (new MailMessage)
            ->subject("[NexShape] {$title}")
            ->greeting("Olá, Administrador.")
            ->line("Um evento operacional crítico foi detectado no sistema.")
            ->line("**Evento:** " . ($this->data['message'] ?? 'N/A'))
            ->line("**Componente:** " . ($this->data['component'] ?? 'Geral'))
            ->line("**Data/Hora:** " . now()->format('d/m/Y H:i:s'));

        if (isset($this->data['action_url'])) {
            $mail->action('Ver Detalhes no Dashboard', $this->data['action_url']);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Alerta Operacional',
            'message' => $this->data['message'] ?? '',
            'component' => $this->data['component'] ?? 'Geral',
            'level' => $this->data['level'] ?? 'info',
        ];
    }
}
