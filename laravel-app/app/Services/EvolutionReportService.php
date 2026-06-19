<?php

namespace App\Services;

use App\Models\BodyAssessment;
use App\Models\FoodEntry;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WorkoutSession;

class EvolutionReportService
{
    public function __construct(
        private IntelligenceMotorService $motor
    ) {}

    /**
     * Relatório semanal determinístico (compatível com evolution/ai-report.blade.php).
     */
    public function generate(User $user): array
    {
        $profile = $user->profile;
        if (! $profile) {
            return ['ok' => false, 'error' => 'Perfil não encontrado'];
        }

        $workouts30 = WorkoutSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $nutritionDays = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(30)->format('Y-m-d'))
            ->distinct('entry_date')
            ->count('entry_date');

        $targetWorkouts = max(4, (int) ($profile->training_days_per_week ?? 3) * 4);
        $disciplina = (int) min(100, round(($nutritionDays / 20) * 100));
        $consistencia = (int) min(100, round(($workouts30 / $targetWorkouts) * 100));
        $performance = (int) round(($disciplina + $consistencia) / 2);

        $assessments = BodyAssessment::where('user_id', $user->id)
            ->orderByDesc('assessment_date')
            ->take(2)
            ->get();

        $current = $assessments->first();
        $previous = $assessments->skip(1)->first();
        $tendencias = [];

        if ($current && $previous && $current->id !== $previous->id) {
            $deltaWeight = round($current->weight_kg - $previous->weight_kg, 1);
            $deltaBf = round(($current->bf_percent ?? 0) - ($previous->bf_percent ?? 0), 1);
            $tendencias[] = "Peso: ".($deltaWeight > 0 ? '+' : '')."{$deltaWeight} kg desde a avaliação anterior.";
            if ($current->bf_percent && $previous->bf_percent) {
                $tendencias[] = "Gordura corporal: ".($deltaBf > 0 ? '+' : '')."{$deltaBf}% no período.";
            }
        } else {
            $tendencias[] = 'Registre avaliações periódicas para acompanhar tendências com precisão.';
        }

        $activePlan = TrainingPlan::where('user_id', $user->id)->where('is_active', true)->first();
        $goal = $profile->goal ?? 'maintain';

        $estrategia = match ($goal) {
            'lose_weight', 'emagrecimento' => 'Mantenha déficit calórico moderado e priorize treinos com componente metabólico.',
            'gain_mass', 'hipertrofia' => 'Foque em progressão de carga e superávit proteico controlado.',
            'performance' => 'Alterne sessões de alta intensidade com recuperação ativa.',
            default => 'Equilibre treino, nutrição e descanso para manutenção saudável.',
        };

        $ajustesTreino = $workouts30 >= $targetWorkouts
            ? 'Manter volume atual; considere periodizar cargas a cada 4–6 semanas.'
            : 'Aumente frequência em '.max(1, (int) ceil(($targetWorkouts - $workouts30) / 4)).' sessões/semana.';

        $risks = $this->motor->detectRisks($user);
        $recuperacao = empty($risks)
            ? 'Sem alertas metabólicos. Priorize 7–8h de sono e hidratação adequada.'
            : collect($risks)->pluck('message')->first();

        $prediction = $this->motor->predictEvolution($user);
        $alimentacao = $prediction['possible']
            ? ($prediction['message'] ?? 'Acompanhe calorias e macros diariamente.')
            : 'Defina meta de peso e registre refeições para projeções mais precisas.';

        $veredito = $performance >= 75
            ? 'Excelente consistência — continue o plano atual.'
            : ($performance >= 50 ? 'Boa evolução com margem de melhoria na regularidade.' : 'Foque em hábitos básicos: treino + registro alimentar.');

        $proximos = [
            'Registrar pelo menos 5 refeições esta semana.',
            'Completar '.max(2, (int) ceil($targetWorkouts / 4)).' treinos na próxima semana.',
        ];
        if ($activePlan) {
            $proximos[] = "Seguir o plano ativo: {$activePlan->name}.";
        }

        return [
            'ok' => true,
            'report' => [
                'diagnostico' => "Análise baseada em {$workouts30} treinos e {$nutritionDays} dias com registro alimentar nos últimos 30 dias.",
                'scores' => [
                    'performance_geral' => $performance,
                    'disciplina' => $disciplina,
                    'consistencia' => $consistencia,
                    'recuperacao' => (int) min(100, 60 + ($disciplina / 5)),
                    'intensidade' => (int) min(100, 50 + ($workouts30 * 3)),
                    'condicionamento' => (int) min(100, $consistencia),
                    'regularidade' => $consistencia,
                    'evolucao' => $performance,
                ],
                'tendencias' => $tendencias,
                'insight_premium' => $veredito,
                'estrategia_semana' => $estrategia,
                'ajustes_treino' => $ajustesTreino,
                'recuperacao_energia' => $recuperacao,
                'alimentacao_metabolismo' => $alimentacao,
                'veredito' => $veredito,
                'proximos_passos' => $proximos,
            ],
            'tokens' => 0,
            'model' => 'rule_engine',
        ];
    }
}
