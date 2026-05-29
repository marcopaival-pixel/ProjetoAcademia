<?php

namespace App\Notifications;

use App\Models\ProfessionalAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ProfessionalAppointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Novo Agendamento: ' . $this->appointment->patient->name)
                    ->line('Você tem um novo agendamento marcado pelo portal do aluno.')
                    ->line('Paciente: ' . $this->appointment->patient->name)
                    ->line('Data/Hora: ' . $this->appointment->appointment_at->format('d/m/Y H:i'))
                    ->line('Tipo: ' . ucfirst($this->appointment->service_type))
                    ->action('Ver na Agenda', route('professional.dashboard'))
                    ->line('Obrigado por usar nossa plataforma!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->name,
            'appointment_at' => $this->appointment->appointment_at->format('Y-m-d H:i:s'),
            'service_type' => $this->appointment->service_type,
            'message' => 'Novo agendamento com ' . $this->appointment->patient->name,
        ];
    }
}
