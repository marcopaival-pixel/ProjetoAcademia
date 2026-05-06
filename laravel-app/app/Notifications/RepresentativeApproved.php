<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepresentativeApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu acesso como Representante foi Aprovado! - NexShape Pro')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Temos o prazer de informar que seu cadastro como Representante Comercial na NexShape Pro foi analisado e aprovado com sucesso.')
            ->line('A partir de agora, você já pode acessar seu painel exclusivo para gerenciar suas indicações, acompanhar comissões e solicitar saques.')
            ->action('Acessar Meu Painel', route('login'))
            ->line('Estamos ansiosos para crescer juntos nesta parceria!')
            ->salutation('Atenciosamente, Equipe NexShape Pro');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
