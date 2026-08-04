<?php

namespace App\Services;

use App\Models\ProfessionalAppointment;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use App\Support\AiStudentWriteGuard;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
            AiStudentWriteGuard::assertWritesEnabled();

            if (! AiStudentWriteGuard::isWriteAction($type)) {
                throw new Exception("Acao '{$type}' nao reconhecida pelo sistema.");
            }

            $this->auditLog($user, $type, $data, 'START');

            $result = DB::transaction(function () use ($user, $type, $data) {
                return match ($type) {
                    'agendar' => $this->handleAgendar($user, $data),
                    'cancelar_agendamento' => $this->handleCancelar($user, $data),
                    'criar_treino' => $this->handleCriarTreino($user, $data),
                    'ajustar_treino' => $this->handleAjustarTreino($user, $data),
                    'criar_dieta', 'ajustar_dieta' => $this->handleDietWriteBlocked($type),
                    default => throw new Exception("Acao '{$type}' nao reconhecida pelo sistema."),
                };
            });

            $this->auditLog($user, $type, $data, 'SUCCESS');

            return $result;
        } catch (Exception $e) {
            Log::error("Erro no AgentActionDispatcher [{$type}]: ".$e->getMessage());
            $this->auditLog($user, $type, $data, 'FAILURE', $e->getMessage());

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function handleAgendar(User $user, array $data): array
    {
        if (empty($data['professional_id']) || empty($data['appointment_at'])) {
            throw new Exception('Dados insuficientes para agendamento (profissional ou data ausente).');
        }

        $patientId = (int) ($data['patient_id'] ?? $user->id);
        if ($patientId !== (int) $user->id && ! $user->isAdministrator()) {
            throw new Exception('A IA so pode agendar consultas para o proprio aluno.');
        }

        $appointment = $this->agendaService->scheduleAppointment($user, [
            'professional_id' => $data['professional_id'],
            'appointment_at' => $data['appointment_at'],
            'patient_id' => $patientId,
            'service_type' => $data['service_type'] ?? 'Consulta IA',
            'notes' => $data['notes'] ?? 'Agendado via NexBot AI',
        ]);

        return [
            'ok' => true,
            'message' => 'Agendamento realizado com sucesso para '.$appointment->appointment_at->format('d/m \à\s H:i'),
            'id' => $appointment->id,
        ];
    }

    private function handleCancelar(User $user, array $data): array
    {
        $appointmentId = $data['appointment_id'] ?? null;
        if (! $appointmentId) {
            throw new Exception('ID do agendamento nao fornecido.');
        }

        $appointment = ProfessionalAppointment::query()
            ->where('patient_id', $user->id)
            ->find($appointmentId);

        if (! $appointment) {
            throw new Exception('Agendamento nao encontrado ou voce nao tem permissao para cancela-lo.');
        }

        $this->agendaService->cancelAppointment($user, $appointment);

        return [
            'ok' => true,
            'message' => 'Agendamento cancelado conforme solicitado.',
        ];
    }

    private function handleCriarTreino(User $user, array $data): array
    {
        Gate::forUser($user)->authorize('create', TrainingPlan::class);

        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'professional_id' => null,
            'name' => $data['name'] ?? 'Novo Treino NexBot',
            'goal' => $data['goal'] ?? 'Geral',
            'status' => 'Ativo',
            'description' => $data['description'] ?? 'Gerado automaticamente pelo NexBot AI.',
            'created_by_ai' => true,
            'is_active' => true,
        ]);

        if (! empty($data['exercises'])) {
            foreach ($data['exercises'] as $index => $ex) {
                $catalogId = is_array($ex) ? ($ex['id'] ?? null) : $ex;
                if (! $catalogId) {
                    continue;
                }

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
            'id' => $plan->id,
        ];
    }

    private function handleAjustarTreino(User $user, array $data): array
    {
        $planId = $data['plan_id'] ?? null;
        if (! $planId) {
            throw new Exception('ID do plano de treino nao fornecido para ajuste.');
        }

        $plan = TrainingPlan::query()
            ->where('user_id', $user->id)
            ->find($planId);

        if (! $plan) {
            throw new Exception('Plano de treino nao encontrado ou voce nao tem permissao para altera-lo.');
        }

        Gate::forUser($user)->authorize('update', $plan);
        AiStudentWriteGuard::assertCanAiModifyPlan($user, $plan);

        return [
            'ok' => true,
            'message' => "Treino '{$plan->name}' marcado para revisao manual. Ajustes automaticos completos ainda nao estao disponiveis — edite o plano na area de treinos.",
            'id' => $plan->id,
        ];
    }

    private function handleDietWriteBlocked(string $type): array
    {
        throw new Exception(
            'A IA nao grava dietas automaticamente (acao: '.$type.'). Use o diario alimentar ou solicite sugestao de refeicao para revisar antes de salvar.'
        );
    }

    private function auditLog(User $user, string $type, array $data, string $status, ?string $error = null): void
    {
        \App\Models\AdminLog::create([
            'user_id' => $user->id,
            'action' => "AGENT_ACTION: {$type} | Status: {$status}".($error ? " | Error: {$error}" : ''),
            'payload' => json_encode($data),
            'ip_address' => request()->ip(),
        ]);
    }
}
