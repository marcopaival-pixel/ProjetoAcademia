<?php

namespace App\Services;

use App\Models\AppointmentWaitlist;
use App\Models\ProfessionalAppointment;
use Illuminate\Support\Facades\Log;

class AgendaNotificationService
{
    public function notifyAppointmentScheduled(ProfessionalAppointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'professional']);

        $when = $appointment->appointment_at->format('d/m/Y \à\s H:i');

        try {
            if ($appointment->patient_id) {
                MessagingService::sendSystemMessage(
                    $appointment->patient_id,
                    'Agendamento confirmado',
                    "Seu agendamento foi confirmado para {$when}."
                );
            }

            if ($appointment->professional_id) {
                $patientName = $appointment->patient?->name ?? 'paciente';
                MessagingService::sendSystemMessage(
                    $appointment->professional_id,
                    'Novo agendamento',
                    "Novo agendamento com {$patientName} em {$when}."
                );
            }
        } catch (\Throwable $e) {
            Log::warning('AgendaNotification: falha ao notificar agendamento.', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function notifyWaitlistOnCancellation(ProfessionalAppointment $appointment): void
    {
        $waitlist = AppointmentWaitlist::query()
            ->where('status', 'waiting')
            ->where('professional_id', $appointment->professional_id)
            ->whereDate('requested_date', $appointment->appointment_at->toDateString())
            ->get();

        if ($waitlist->isEmpty()) {
            return;
        }

        $when = $appointment->appointment_at->format('d/m/Y H:i');

        foreach ($waitlist as $entry) {
            try {
                MessagingService::sendSystemMessage(
                    $entry->patient_id,
                    'Vaga disponível na agenda',
                    "Um horário foi liberado para {$when}. Acesse a agenda para agendar."
                );

                $entry->update(['status' => 'notified']);
            } catch (\Throwable $e) {
                Log::warning('AgendaNotification: falha ao notificar lista de espera.', [
                    'waitlist_id' => $entry->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
