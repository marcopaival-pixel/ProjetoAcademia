<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExerciseEntry;
use App\Models\FoodEntry;
use App\Models\WeightEntry;
use App\Models\ProfessionalAppointment;
use App\Models\WaterEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EvolutionStatusService
{
    /**
     * Calcula o score geral de evolução (0-100) e o status visual.
     */
    public function getEvolutionStatus(User $user)
    {
        $pillars = [
            'training' => $this->calculateTrainingScore($user),
            'nutrition' => $this->calculateNutritionScore($user),
            'evolution' => $this->calculateBodyEvolutionScore($user),
            'agenda' => $this->calculateAgendaScore($user),
            'engagement' => $this->calculateEngagementScore($user),
        ];

        $totalScore = (
            ($pillars['training']['score'] * 0.25) +
            ($pillars['nutrition']['score'] * 0.25) +
            ($pillars['evolution']['score'] * 0.20) +
            ($pillars['agenda']['score'] * 0.15) +
            ($pillars['engagement']['score'] * 0.15)
        );

        $status = $this->determineStatus($totalScore);

        return [
            'score' => round($totalScore),
            'color' => $status['color'],
            'status_label' => $status['label'],
            'message' => $status['message'],
            'pillars' => $pillars,
            'recommendation' => $user->hasPremiumAccess() ? $this->generatePremiumRecommendation($totalScore, $pillars) : null,
        ];
    }

    private function calculateTrainingScore(User $user): array
    {
        $profile = $user->profile;
        $goalPerWeek = $profile->training_days_per_week ?? 4;
        $monthlyGoal = $goalPerWeek * 4;
        
        $actualMonth = ExerciseEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(30))
            ->count();

        $score = $monthlyGoal > 0 ? min(($actualMonth / $monthlyGoal) * 100, 100) : 100;

        return [
            'score' => $score,
            'label' => 'Frequência de Treinos',
            'detail' => "$actualMonth treinos nos últimos 30 dias (Meta: $monthlyGoal)",
        ];
    }

    private function calculateNutritionScore(User $user): array
    {
        $profile = $user->profile;
        $target = $profile ? $profile->daily_calorie_target : null;
        if (!$target) return ['score' => 70, 'label' => 'Plano Alimentar', 'detail' => 'Meta calórica não definida'];

        $last7Days = FoodEntry::where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(7))
            ->select('entry_date', DB::raw('SUM(calories) as total'))
            ->groupBy('entry_date')
            ->get();

        if ($last7Days->isEmpty()) return ['score' => 0, 'label' => 'Plano Alimentar', 'detail' => 'Nenhum registro nos últimos 7 dias'];

        $dayScores = $last7Days->map(function ($day) use ($target) {
            $diff = abs($day->total - $target);
            $precision = max(0, 100 - (($diff / $target) * 100));
            return $precision;
        });

        $score = $dayScores->avg();

        return [
            'score' => $score,
            'label' => 'Adesão Alimentar',
            'detail' => round($score) . '% de precisão calórica média (7 dias)',
        ];
    }

    private function calculateBodyEvolutionScore(User $user): array
    {
        $weights = WeightEntry::where('user_id', $user->id)
            ->orderByDesc('weighed_at')
            ->limit(2)
            ->get();

        if ($weights->count() < 2) {
            return ['score' => 50, 'label' => 'Evolução Corporal', 'detail' => 'Dados insuficientes para análise de tendência'];
        }

        $latest = $weights[0]->weight_kg;
        $previous = $weights[1]->weight_kg;
        
        $profile = $user->profile;
        $target = $profile ? $profile->target_weight_kg : null;
        $goal = $profile ? $profile->goal : 'maintain';

        $positiveTrend = false;
        if (str_contains($goal, 'lose')) {
            $positiveTrend = $latest < $previous;
        } elseif (str_contains($goal, 'gain')) {
            $positiveTrend = $latest > $previous;
        } else {
            $positiveTrend = abs($latest - $previous) < 1.0; // Maintain
        }

        return [
            'score' => $positiveTrend ? 100 : 40,
            'label' => 'Tendência de Peso',
            'detail' => $positiveTrend ? 'Evoluindo conforme objetivo' : 'Peso estagnado ou fora da meta',
        ];
    }

    private function calculateAgendaScore(User $user): array
    {
        $appointments = ProfessionalAppointment::where('patient_id', $user->id)
            ->whereIn('status', [ProfessionalAppointment::STATUS_FINISHED, ProfessionalAppointment::STATUS_NO_SHOW])
            ->where('appointment_at', '>=', now()->subDays(90))
            ->get();

        if ($appointments->isEmpty()) {
            return ['score' => 100, 'label' => 'Compromissos', 'detail' => 'Sem consultas agendadas recentemente'];
        }

        $finished = $appointments->where('status', ProfessionalAppointment::STATUS_FINISHED)->count();
        $score = ($finished / $appointments->count()) * 100;

        return [
            'score' => $score,
            'label' => 'Presença em Consultas',
            'detail' => "$finished de " . $appointments->count() . " comparecimentos",
        ];
    }

    private function calculateEngagementScore(User $user): array
    {
        $activeDays = DB::table(function ($query) use ($user) {
            $query->select('entry_date')->from('food_entries')->where('user_id', $user->id)->where('entry_date', '>=', now()->subDays(7))
                ->union(DB::table('exercise_entries')->select('entry_date')->where('user_id', $user->id)->where('entry_date', '>=', now()->subDays(7)))
                ->union(DB::table('water_entries')->select('entry_date')->where('user_id', $user->id)->where('entry_date', '>=', now()->subDays(7)))
                ->union(DB::table('weight_entries')->select(DB::raw('DATE(weighed_at)'))->where('user_id', $user->id)->where('weighed_at', '>=', now()->subDays(7)));
        }, 'activity')->count();

        $score = ($activeDays / 7) * 100;

        return [
            'score' => $score,
            'label' => 'Engajamento',
            'detail' => "$activeDays de 7 dias com atividade na plataforma",
        ];
    }

    private function determineStatus(float $score): array
    {
        if ($score >= 80) {
            return [
                'color' => 'green',
                'label' => 'Excelente',
                'message' => 'Você está no caminho certo! Mantenha a consistência.',
            ];
        }

        if ($score >= 50) {
            return [
                'color' => 'orange',
                'label' => 'Atenção',
                'message' => 'Seu desempenho oscilou. Ajuste sua rotina para retomar os resultados.',
            ];
        }

        return [
            'color' => 'red',
            'label' => 'Abaixo do Esperado',
            'message' => 'Sinal vermelho. Procure seu mentor ou revise suas metas urgentemente.',
        ];
    }

    private function generatePremiumRecommendation(float $score, array $pillars): string
    {
        $weakest = collect($pillars)->sortBy('score')->first();
        
        if ($score >= 80) {
            return "Alta Performance Detectada! Você está dominando a maioria dos pilares. Para o próximo nível, tente otimizar seu " . strtolower($weakest['label']) . ".";
        }

        return "Foco em Recuperação: Notamos que seu " . strtolower($weakest['label']) . " é o ponto com maior margem de melhoria. Priorize este pilar nos próximos 7 dias.";
    }
}
