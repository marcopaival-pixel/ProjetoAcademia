<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientProfessionalLinkNotification extends Notification
{
    use Queueable;

    protected $professionalName;
    protected $type; // 'new' or 'transfer'

    /**
     * Create a new notification instance.
     */
    public function __construct($professionalName, $type = 'new')
    {
        $this->professionalName = $professionalName;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $subject = $this->type === 'new' 
            ? 'Novo profissional vinculado ao seu perfil' 
            : 'Transferência de profissional concluída';

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Olá, ' . $notifiable->name . '!')
                    ->line('Gostaríamos de informar que você foi vinculado ao profissional: ' . $this->professionalName . '.')
                    ->line('Agora ele tem acesso ao seu prontuário e poderá acompanhar sua evolução.')
                    ->action('Acessar Meu Portal', route('patient.portal'))
                    ->line('Obrigado por utilizar o NexShape!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'professional_name' => $this->professionalName,
            'type' => $this->type,
            'message' => 'Novo vínculo estabelecido com ' . $this->professionalName
        ];
    }
}
