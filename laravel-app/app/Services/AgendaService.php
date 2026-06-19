<?php

namespace App\Services;

use App\Models\ProfessionalAppointment;
use App\Models\AppointmentWaitlist;
use App\Models\ProfessionalAvailability;
use App\Models\AgendaSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AgendaService
{
    /**
     * Valida permissões e executa o agendamento de acordo com o Perfil e Plano.
     */
    public function scheduleAppointment(User $user, array $data)
    {
        $targetPatientId = $data['patient_id'] ?? $user->id;
        $professionalId = $data['professional_id'];
        $appointmentAt = Carbon::parse($data['appointment_at']);

        // Regra: Impedir agendamento no passado
        if ($appointmentAt->isPast()) {
            throw ValidationException::withMessages(['appointment_at' => 'Não é possível agendar no passado.']);
        }

        $this->enforceProfileCreationRules($user, $targetPatientId, $professionalId);
        $this->enforcePlanLimitations($user, $appointmentAt);
        $this->enforceAvailabilityAndConflict($professionalId, $appointmentAt);

        DB::beginTransaction();
        try {
            $appointment = ProfessionalAppointment::create([
                'professional_id' => $professionalId,
                'patient_id' => $targetPatientId,
                'appointment_at' => $appointmentAt,
                'service_type' => $data['service_type'] ?? 'Consulta',
                'status' => ProfessionalAppointment::STATUS_SCHEDULED,
                'notes' => $data['notes'] ?? null,
            ]);

            // Remover da lista de espera se houver
            AppointmentWaitlist::where('patient_id', $targetPatientId)
                ->where('status', 'waiting')
                ->whereDate('requested_date', $appointmentAt->toDateString())
                ->update(['status' => 'fulfilled']);

            $this->logAction($user, "Agendamento criado ID: {$appointment->id}");

            // TODO: Notificar aluno e profissional
            // $this->notifyAppointment($appointment);

            DB::commit();
            return $appointment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao agendar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancelar agendamento aplicando regras de antecedência do Plano.
     */
    public function cancelAppointment(User $user, ProfessionalAppointment $appointment)
    {
        $this->enforceProfileCancellationRules($user, $appointment);
        
        $plan = $user->plan ? strtolower($user->plan->name) : 'free';
        $hoursUntilAppointment = Carbon::now()->diffInHours(Carbon::parse($appointment->appointment_at), false);

        if ($user->profile->name === 'aluno' && $hoursUntilAppointment > 0) {
            if ($plan === 'free' && $hoursUntilAppointment < 24) {
                throw ValidationException::withMessages(['error' => 'Alunos Free só podem cancelar com 24h de antecedência.']);
            }
            if ($plan === 'premium' && $hoursUntilAppointment < 1) {
                throw ValidationException::withMessages(['error' => 'Alunos Premium só podem cancelar com 1h de antecedência.']);
            }
        }

        $appointment->update(['status' => ProfessionalAppointment::STATUS_CANCELLED]);
        $this->logAction($user, "Agendamento cancelado ID: {$appointment->id}");

        // TODO: Disparar evento para notificar lista de espera.

        return $appointment;
    }

    public function updateAppointmentStatus(User $user, ProfessionalAppointment $appointment, string $status)
    {
        // Validar se o usuário é o profissional ou tem permissão
        if ($appointment->professional_id != $user->id && !$user->isAdministrator()) {
            throw ValidationException::withMessages(['error' => 'Sem permissão para alterar este agendamento.']);
        }

        if (!array_key_exists($status, ProfessionalAppointment::getStatuses())) {
            throw ValidationException::withMessages(['error' => 'Status inválido.']);
        }

        $appointment->update(['status' => $status]);
        $this->logAction($user, "Agendamento ID: {$appointment->id} alterado para status: {$status}");

        return $appointment;
    }

    public function getAvailableSlots($professionalId, $date)
    {
        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek;
        
        $profile = \App\Models\ProfessionalProfile::where('user_id', $professionalId)->first();
        $duration = $profile->appointment_duration ?? 60;
        $interval = $profile->appointment_interval ?? 15;
        
        $availabilities = ProfessionalAvailability::where('professional_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->get();
            
        $bookedSlots = ProfessionalAppointment::where('professional_id', $professionalId)
            ->whereDate('appointment_at', $date->toDateString())
            ->whereIn('status', [
                ProfessionalAppointment::STATUS_SCHEDULED, 
                ProfessionalAppointment::STATUS_CONFIRMED, 
                ProfessionalAppointment::STATUS_IN_PROGRESS, 
                ProfessionalAppointment::STATUS_FINISHED
            ])
            ->pluck('appointment_at');
            
        $slots = [];
        
        foreach ($availabilities as $avail) {
            $current = Carbon::parse($date->toDateString() . ' ' . $avail->start_time);
            $end = Carbon::parse($date->toDateString() . ' ' . $avail->end_time);
            
            while ($current->copy()->addMinutes($duration)->lte($end)) {
                $isBooked = $bookedSlots->contains(function ($val) use ($current) {
                    return $val->eq($current);
                });
                
                $slots[] = [
                    'time' => $current->format('H:i'),
                    'available' => !$isBooked && ($date->isFuture() || ($date->isToday() && $current->isFuture()))
                ];
                
                $current->addMinutes($duration + $interval);
            }
        }
        
        return $slots;
    }

    public function addToWaitlist(User $user, $professionalId, $date)
    {
        // Enforce aluno rules 
        if ($user->profile->name === 'aluno' && $professionalId == null) {
            // Need a valid prof
        }

        $waitlist = AppointmentWaitlist::create([
            'patient_id' => $user->id,
            'professional_id' => $professionalId,
            'requested_date' => Carbon::parse($date),
            'status' => 'waiting'
        ]);

        $this->logAction($user, "Entrou na lista de espera para o dia {$date}");

        return $waitlist;
    }

    // --- INTERNAL ENFORCEMENT METHODS --- //

    private function enforceProfileCreationRules(User $user, $targetPatientId, $professionalId)
    {
        if ($user->hasRole(['aluno', 'paciente'])) {
            if ((int) $targetPatientId !== (int) $user->id) {
                throw ValidationException::withMessages(['patient_id' => 'Agendamento permitido apenas para o próprio aluno.']);
            }

            return true;
        }

        $profile = strtolower($user->profile?->name ?? $user->getRoleNames()->first() ?? '');

        if ($profile === 'professional' || $profile === 'instructor') {
            if ($professionalId != $user->id) {
                throw ValidationException::withMessages(['error' => 'Profissional só pode agendar em sua própria agenda.']);
            }
        } elseif ($profile === 'receptionist' || $profile === 'admin' || $profile === 'manager') {
            // Atendente e Admin podem agendar para qualquer um
            return true;
        } else {
            throw ValidationException::withMessages(['error' => 'Perfil sem permissão para agendamentos.']);
        }
    }

    private function enforceProfileCancellationRules(User $user, ProfessionalAppointment $appointment)
    {
        $profile = strtolower($user->profile->name ?? '');

        if ($profile === 'aluno' || $user->hasRole('paciente')) {
            throw ValidationException::withMessages(['error' => 'Paciente não possui permissão para cancelar agendamentos via portal.']);
        }

        if ($profile === 'professional' || $profile === 'instructor') {
            if ((int) $appointment->professional_id !== (int) $user->id) {
                throw ValidationException::withMessages(['error' => 'Profissional não pode cancelar agendamento de outros.']);
            }

            return;
        }

        if ((int) $appointment->patient_id === (int) $user->id) {
            return;
        }

        if ($user->hasRole(['manager', 'supervisor', 'receptionist']) || $user->isAdministrator()) {
            $professional = User::find($appointment->professional_id);
            if ($professional && (int) $professional->academy_company_id === (int) $user->academy_company_id) {
                return;
            }
        }

        throw ValidationException::withMessages(['error' => 'Você não tem permissão para cancelar este agendamento.']);
    }

    private function enforcePlanLimitations(User $user, Carbon $appointmentAt)
    {
        if (strtolower($user->profile->name ?? '') !== 'aluno' && ! $user->hasRole('aluno')) {
            return;
        }

        $plan = $user->plan ? strtolower($user->plan->name) : 'free';

        if ($plan === 'free') {
            $countToday = ProfessionalAppointment::where('patient_id', $user->id)
                ->whereDate('appointment_at', $appointmentAt->toDateString())
                ->whereIn('status', [ProfessionalAppointment::STATUS_SCHEDULED, ProfessionalAppointment::STATUS_FINISHED])
                ->count();

            if ($countToday >= 1) {
                throw ValidationException::withMessages(['error' => 'Plano Free permite apenas 1 agendamento por dia.']);
            }
        }
        // Premium is unlimited
    }

    private function enforceAvailabilityAndConflict($professionalId, Carbon $appointmentAt)
    {
        // 1. Impedir dois atendimentos no mesmo horário para o mesmo profissional
        $conflict = ProfessionalAppointment::where('professional_id', $professionalId)
            ->where('appointment_at', $appointmentAt)
            ->whereNotIn('status', [ProfessionalAppointment::STATUS_CANCELLED])
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages(['appointment_at' => 'Horário indisponível para este profissional.']);
        }

        // 2. Impedir agendamento fora do horário de funcionamento / bloqueio
        // Implementação básica validando a tabela professional_availabilities
        $dayOfWeek = $appointmentAt->dayOfWeek; // 0 (Sun) - 6 (Sat)
        $time = $appointmentAt->format('H:i:s');

        $availability = ProfessionalAvailability::where('professional_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $time)
            ->first();

        // Se o profissional registrou horários, validamos. Se não, assumimos que ele precisa configurar.
        // Como o sistema evolui, podemos usar as AgendaSettings para horários globais tbm.
        $globalStart = AgendaSetting::where('key', 'business_start_time')->value('value') ?? '06:00:00';
        $globalEnd = AgendaSetting::where('key', 'business_end_time')->value('value') ?? '22:00:00';

        if ($time < $globalStart || $time > $globalEnd) {
            throw ValidationException::withMessages(['appointment_at' => 'Horário fora do expediente da academia.']);
        }
    }

    private function logAction(User $user, string $action)
    {
        $profileName = $user->getRoleNames()[0] ?? 'user';
        Log::info("[AGENDA] UserID: {$user->id} | Perfil: {$profileName} | Action: {$action}");
        \App\Models\AdminLog::create([
            'user_id' => $user->id,
            'action' => "AGENDA: " . $action . " (Por: {$user->name} - Perfil: {$profileName})",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
