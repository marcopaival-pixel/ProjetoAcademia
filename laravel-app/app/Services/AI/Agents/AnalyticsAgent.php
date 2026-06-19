<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class AnalyticsAgent extends BaseAgent
{
    public function getName(): string
    {
        return 'analytics';
    }

    /**
     * Insights baseados em SQL — sem chamada LLM (economia de tokens).
     */
    public function execute(User $user, string $message, array $context = []): array
    {
        $subject = $this->resolveSubjectUser($user, $context);

        $workoutCount = \App\Models\WorkoutSession::where('user_id', $subject->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $nutritionDays = \App\Models\FoodEntry::where('user_id', $subject->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('entry_date')
            ->count('entry_date');

        $profile = $subject->profile;
        $goal = $profile->goal ?? 'maintain';
        $targetDays = (int) ($profile->training_days_per_week ?? 3) * 4;

        $message = $this->buildInsightMessage($workoutCount, $nutritionDays, $targetDays, $goal);

        return [
            'ok' => true,
            'message' => $message,
            'tokens' => 0,
            'cost' => 0,
            'model' => 'rule_engine',
        ];
    }

    private function buildInsightMessage(int $workouts, int $nutritionDays, int $targetWorkouts, string $goal): string
    {
        $lines = ["📊 **Resumo dos últimos 30 dias**\n"];
        $lines[] = "- Treinos realizados: **{$workouts}**";
        $lines[] = "- Dias com registro alimentar: **{$nutritionDays}**";

        if ($workouts >= $targetWorkouts) {
            $lines[] = "\n✅ Excelente constância nos treinos! Você atingiu ou superou a meta esperada ({$targetWorkouts} sessões/mês).";
        } elseif ($workouts >= (int) ($targetWorkouts * 0.6)) {
            $lines[] = "\n⚡ Boa evolução, mas há margem para aumentar a frequência. Meta: {$targetWorkouts} treinos/mês.";
        } else {
            $lines[] = "\n🎯 Foco na regularidade: tente agendar pelo menos ".max(2, (int) ceil($targetWorkouts / 2))." treinos por semana.";
        }

        if ($nutritionDays >= 20) {
            $lines[] = '✅ Registro alimentar consistente — isso acelera seus resultados.';
        } elseif ($nutritionDays >= 10) {
            $lines[] = '⚡ Registre mais refeições para insights nutricionais mais precisos.';
        } else {
            $lines[] = '📝 Comece registrando pelo menos 1 refeição por dia no diário nutricional.';
        }

        $strategy = match ($goal) {
            'lose_weight', 'emagrecimento' => 'Priorize déficit calórico leve e treinos com componente metabólico.',
            'gain_mass', 'hipertrofia' => 'Mantenha superávit proteico e progressão de carga nos treinos.',
            'performance' => 'Alterne dias de alta intensidade com recuperação ativa.',
            default => 'Equilibre treino, nutrição e descanso para manutenção saudável.',
        };

        $lines[] = "\n**Estratégia sugerida:** {$strategy}";

        return implode("\n", $lines);
    }
}
