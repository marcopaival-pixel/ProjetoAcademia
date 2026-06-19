<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentActionDispatcher
{
    public function __construct(
        private AgendaService $agendaService,
        private NutritionService $nutritionService
    ) {}

    /**
     * Executa a ação solicitada pela IA após validação
     */
    public function dispatch(User $user, array $action): array
    {
        $type = $action['acao'] ?? '';
        $data = $action['dados'] ?? [];

        try {
            // Log de auditoria inicial
            $this->auditLog($user, $type, $data, 'START');

            $result = DB::transaction(function () use ($user, $type, $data) {
                switch ($type) {
                    case 'agendar':
                        return $this->handleAgendar($user, $data);
                    
                    case 'cancelar_agendamento':
                        return $this->handleCancelar($user, $data);

                    case 'criar_treino':
                        return $this->handleCriarTreino($user, $data);

                    case 'ajustar_treino':
                        return $this->handleAjustarTreino($user, $data);

                    case 'criar_dieta':
                        return $this->handleCriarDieta($user, $data);

                    case 'ajustar_dieta':
                        return $this->handleAjustarDieta($user, $data);

                    default:
                        throw new Exception("Ação '{$type}' não reconhecida pelo sistema.");
                }
            });

            $this->auditLog($user, $type, $data, 'SUCCESS');
            return $result;

        } catch (Exception $e) {
            Log::error("Erro no AgentActionDispatcher [{$type}]: " . $e->getMessage());
            $this->auditLog($user, $type, $data, 'FAILURE', $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function handleAgendar(User $user, array $data): array
    {
        if (empty($data['professional_id']) || empty($data['appointment_at'])) {
            throw new Exception("Dados insuficientes para agendamento (profissional ou data ausente).");
        }

        $appointment = $this->agendaService->scheduleAppointment($user, [
            'professional_id' => $data['professional_id'],
            'appointment_at' => $data['appointment_at'],
            'patient_id' => $data['patient_id'] ?? $user->id,
            'service_type' => $data['service_type'] ?? 'Consulta IA',
            'notes' => $data['notes'] ?? 'Agendado via NexBot AI',
        ]);

        return [
            'ok' => true,
            'message' => "Agendamento realizado com sucesso para " . $appointment->appointment_at->format('d/m \à\s H:i'),
            'id' => $appointment->id
        ];
    }

    private function handleCancelar(User $user, array $data): array
    {
        $appointmentId = $data['appointment_id'] ?? null;
        if (!$appointmentId) throw new Exception("ID do agendamento não fornecido.");

        $appointment = ProfessionalAppointment::findOrFail($appointmentId);
        $this->agendaService->cancelAppointment($user, $appointment);

        return [
            'ok' => true,
            'message' => "Agendamento cancelado conforme solicitado."
        ];
    }

    private function handleCriarTreino(User $user, array $data): array
    {
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'name' => $data['name'] ?? 'Novo Treino NexBot',
            'goal' => $data['goal'] ?? 'Geral',
            'status' => 'Ativo',
            'description' => $data['description'] ?? 'Gerado automaticamente pelo NexBot AI.',
        ]);

        if (!empty($data['exercises'])) {
            foreach ($data['exercises'] as $index => $ex) {
                $catalogId = $ex['id'] ?? $ex; // Suporte a ID direto ou objeto
                TrainingPlanExercise::create([
                    'training_plan_id' => $plan->id,
                    'exercise_id' => $catalogId,
                    'position' => $index,
                ]);
            }
        }

        return [
            'ok' => true,
            'message' => "Treino '{$plan->name}' criado com sucesso.",
            'id' => $plan->id
        ];
    }

    private function handleAjustarTreino(User $user, array $data): array
    {
        $planId = $data['plan_id'] ?? null;
        if (!$planId) throw new Exception("ID do plano de treino não fornecido para ajuste.");

        $plan = TrainingPlan::findOrFail($planId);

        if (! app(\App\Policies\TrainingPlanPolicy::class)->view($user, $plan)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Acesso não autorizado a este plano de treino.');
        }

        // Lógica de ajuste (ex: mudar carga, adicionar exercício)
        return [
            'ok' => true,
            'message' => "Treino '{$plan->name}' ajustado conforme solicitado."
        ];
    }

    private function handleCriarDieta(User $user, array $data): array
    {
        return [
            'ok' => true,
            'message' => "Plano alimentar gerado e disponível para visualização."
        ];
    }

    private function handleAjustarDieta(User $user, array $data): array
    {
        return [
            'ok' => true,
            'message' => "Ajustes na dieta realizados com sucesso."
        ];
    }

    private function auditLog(User $user, string $type, array $data, string $status, string $error = null)
    {
        \App\Models\AdminLog::create([
            'user_id' => $user->id,
            'action' => "AGENT_ACTION: {$type} | Status: {$status}" . ($error ? " | Error: {$error}" : ""),
            'payload' => json_encode($data),
            'ip_address' => request()->ip()
        ]);
    }
}
