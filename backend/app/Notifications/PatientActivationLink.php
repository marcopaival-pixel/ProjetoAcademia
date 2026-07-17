<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientActivationLink extends Notification
{
    use Queueable;

    protected $activationUrl;
    protected $patientName;

    /**
     * Create a new notification instance.
     */
    public function __construct($url, $name)
    {
        $this->activationUrl = $url;
        $this->patientName = $name;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Ative seu acesso ao Portal do Paciente')
                    ->greeting('Olá, ' . $this->patientName . '!')
                    ->line('Seu profissional de saúde cadastrou você na plataforma NexShape.')
                    ->line('Para acessar seu painel, treinos e acompanhamento, você precisa ativar sua conta e criar uma senha.')
                    ->action('Ativar Minha Conta', $this->activationUrl)
                    ->line('Este link é válido por 24 horas.')
                    ->line('Se você não solicitou este acesso, ignore este e-mail.');
    }
}
