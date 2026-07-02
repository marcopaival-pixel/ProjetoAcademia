<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use App\Models\PatientDocument;
use App\Models\MedicalPrescription;
use App\Models\PainRecord;
use App\Models\BodyAssessment;

class TimelineService
{
    /**
     * Compila e ordena cronologicamente o feed de eventos de múltiplos profissionais para o paciente.
     */
    public function getPatientTimeline(User $patient, int $limit = 20): array
    {
        $timeline = collect();

        // 1. Consultas Futuras e Recentes
        $appointments = ProfessionalAppointment::where('patient_id', $patient->id)
            ->with(['professional', 'professional.branding'])
            ->where('appointment_at', '>=', now()->subDays(30))
            ->get()
            ->map(fn($item) => [
                'type' => 'appointment',
                'title' => 'Consulta Médica / Agendamento',
                'description' => "Consulta agendada com " . ($item->professional->name ?? 'Profissional') . " às " . $item->appointment_at->format('H:i'),
                'timestamp' => $item->appointment_at,
                'badge' => 'Consulta',
                'icon' => 'calendar',
                'color' => 'indigo',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($appointments);

        // 2. Planos de Treino
        $workouts = TrainingPlan::where('user_id', $patient->id)
            ->with(['professional'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type' => 'workout',
                'title' => "Plano de Treino Atualizado: {$item->name}",
                'description' => "Novo plano de treino ativo prescrito por " . ($item->professional->name ?? 'Instrutor'),
                'timestamp' => $item->created_at,
                'badge' => 'Treino',
                'icon' => 'dumbbell',
                'color' => 'orange',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($workouts);

        // 3. Receitas e Prescrições Médicas
        $prescriptions = MedicalPrescription::where('patient_id', $patient->id)
            ->with(['professional'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type' => 'prescription',
                'title' => 'Nova Receita / Prescrição',
                'description' => "Receita digital gerada por " . ($item->professional->name ?? 'Médico'),
                'timestamp' => $item->created_at,
                'badge' => 'Receita',
                'icon' => 'file-prescription',
                'color' => 'teal',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($prescriptions);

        // 4. Diário de Dor (EVA)
        $painRecords = PainRecord::where('user_id', $patient->id)
            ->with(['professional'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type' => 'pain',
                'title' => "Registro de Dor (Nível EVA: {$item->eva_level})",
                'description' => "Nível de dor reportado: " . $item->eva_level . "/10. Notas: " . ($item->notes ?? 'Nenhuma nota'),
                'timestamp' => $item->created_at,
                'badge' => 'Fisioterapia',
                'icon' => 'heartbeat',
                'color' => 'red',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($painRecords);

        // 5. Avaliações Físicas / Bioimpedância
        $assessments = BodyAssessment::where('user_id', $patient->id)
            ->with(['professional'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type' => 'assessment',
                'title' => 'Nova Avaliação Física / Bioimpedância',
                'description' => "Avaliação de composição corporal realizada por " . ($item->professional->name ?? 'Avaliador'),
                'timestamp' => $item->created_at,
                'badge' => 'Bioimpedância',
                'icon' => 'weight',
                'color' => 'cyan',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($assessments);

        // 6. Documentos e Exames Anexados
        $documents = PatientDocument::where('patient_id', $patient->id)
            ->with(['professional'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type' => 'document',
                'title' => "Documento Clínico: {$item->title}",
                'description' => "Anexado por " . ($item->professional->name ?? 'Clínica') . " (" . $item->document_type . ")",
                'timestamp' => $item->created_at,
                'badge' => 'Documento',
                'icon' => 'file-medical',
                'color' => 'blue',
                'professional' => $item->professional->name ?? null,
            ]);
        $timeline = $timeline->merge($documents);

        // Ordenação final descrescente de data
        return $timeline->sortByDesc('timestamp')
            ->values()
            ->take($limit)
            ->toArray();
    }
}
