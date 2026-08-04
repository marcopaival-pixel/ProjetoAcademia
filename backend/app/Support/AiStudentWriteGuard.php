<?php

namespace App\Support;

use App\Models\TrainingPlan;
use App\Models\User;

class AiStudentWriteGuard
{
    /**
     * Acoes mutaveis que o NexBot pode disparar via execute-action.
     *
     * @return list<string>
     */
    public static function writeActionCodes(): array
    {
        return [
            'agendar',
            'cancelar_agendamento',
            'criar_treino',
            'ajustar_treino',
            'criar_dieta',
            'ajustar_dieta',
        ];
    }

    public static function writesEnabled(): bool
    {
        return (bool) config('services.ai.student_module_writes', true);
    }

    public static function blockPrescribedPlanChanges(): bool
    {
        return (bool) config('services.ai.block_ai_modify_prescribed_plans', true);
    }

    public static function assertWritesEnabled(): void
    {
        if (! self::writesEnabled()) {
            throw new \RuntimeException(
                'Escritas automaticas da IA no modulo aluno estao desativadas. A IA permanece apenas consultiva.'
            );
        }
    }

    public static function isWriteAction(?string $actionCode): bool
    {
        return $actionCode !== null && in_array($actionCode, self::writeActionCodes(), true);
    }

    /**
     * Plano criado ou vinculado por profissional — IA nao deve alterar.
     */
    public static function isPrescribedByProfessional(TrainingPlan $plan): bool
    {
        if ($plan->professional_id !== null) {
            return true;
        }

        if ($plan->creator_id !== null && (int) $plan->creator_id !== (int) $plan->user_id) {
            return true;
        }

        return false;
    }

    public static function assertCanAiModifyPlan(User $user, TrainingPlan $plan): void
    {
        if ((int) $plan->user_id !== (int) $user->id) {
            throw new \RuntimeException('Voce nao pode alterar treinos de outro usuario.');
        }

        if (self::blockPrescribedPlanChanges() && self::isPrescribedByProfessional($plan)) {
            throw new \RuntimeException(
                'Este treino foi prescrito pelo profissional. A IA nao pode altera-lo — fale com seu profissional ou crie um treino proprio.'
            );
        }
    }
}
