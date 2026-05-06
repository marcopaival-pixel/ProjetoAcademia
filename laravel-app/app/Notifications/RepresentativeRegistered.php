<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepresentativeRegistered extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu cadastro de Representante foi recebido! - NexShape Pro')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Recebemos seu cadastro para se tornar um Representante Comercial da NexShape Pro.')
            ->line('Sua solicitação agora está em nossa fila de análise. Nossa equipe revisará seus dados e entrará em contato em breve.')
            ->line('Status atual: Aguardando aprovação')
            ->action('Acompanhar Cadastro', route('registration.track', ['search' => $notifiable->email]))
            ->line('Obrigado pelo seu interesse em nossa plataforma!')
            ->salutation('Atenciosamente, Equipe NexShape Pro');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
