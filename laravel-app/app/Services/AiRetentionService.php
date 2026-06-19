<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AiRetentionService
{
    public function calculateRiskScore(User $user): array
    {
        $score = 0;
        $reasons = [];

        $recentWorkouts = $user->workoutSessions()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentWorkouts === 0) {
            $score += 30;
            $reasons[] = 'Nenhum treino registrado nos últimos 30 dias.';
        } elseif ($recentWorkouts < 4) {
            $score += 15;
            $reasons[] = 'Baixa frequência de treinos (menos de 1x por semana).';
        }

        $hasActiveSubscription = $user->subscriptions()
            ->whereIn('status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_FIN_ATIVO,
                Subscription::FIN_ATIVO,
                'active',
                'trial',
            ])
            ->exists();

        $hasLatePayments = $user->subscriptions()
            ->whereIn('status', [
                Subscription::FIN_ATRASADO,
                Subscription::STATUS_OVERDUE,
            ])
            ->exists();

        if (! $hasActiveSubscription && $user->subscriptions()->exists()) {
            $score += 20;
            $reasons[] = 'Assinatura inativa ou suspensa.';
        }

        if ($hasLatePayments) {
            $score += 20;
            $reasons[] = 'Existem pagamentos pendentes ou em atraso.';
        }

        $lastActivity = $user->last_activity_at;
        $daysSinceLastLogin = $lastActivity ? $lastActivity->diffInDays(now()) : 999;

        if ($daysSinceLastLogin > 21) {
            $score += 30;
            $reasons[] = 'Abandono do aplicativo (sem acesso há mais de 3 semanas).';
        } elseif ($daysSinceLastLogin > 7) {
            $score += 10;
            $reasons[] = 'Queda de engajamento no aplicativo recentemente.';
        }

        $score = min(100, $score);

        $riskLevel = 'Baixo';
        if ($score >= 70) {
            $riskLevel = 'Alto';
        } elseif ($score >= 40) {
            $riskLevel = 'Médio';
        }

        return [
            'score' => $score,
            'risk_level' => $riskLevel,
            'reasons' => $reasons,
            'suggested_action' => $this->generateSuggestedAction($riskLevel, $reasons),
            'last_calculated_at' => now()->toIso8601String(),
        ];
    }

    private function generateSuggestedAction(string $riskLevel, array $reasons): string
    {
        if ($riskLevel === 'Alto') {
            return 'Recomendamos entrar em contato via WhatsApp oferecendo um incentivo ou reagendando uma avaliação imediata. Motivos principais: '.implode(' ', $reasons);
        }

        if ($riskLevel === 'Médio') {
            return 'Envie uma notificação push motivacional ou um lembrete amigável sobre os benefícios de manter a consistência.';
        }

        return 'Nenhuma ação corretiva necessária. Continue monitorando o bom engajamento.';
    }

    public function getDashboardMetrics(): array
    {
        $patients = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'paciente'))
            ->whereIn('status', ['active', 'ATIVO', 'APROVADO'])
            ->limit(200)
            ->get();

        $atRisk = [];
        foreach ($patients as $patient) {
            $risk = $this->calculateRiskScore($patient);
            if ($risk['score'] < 40) {
                continue;
            }

            $atRisk[] = [
                'id' => $patient->id,
                'name' => $patient->name,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($patient->name).'&background=0D8ABC&color=fff',
                'risk_score' => $risk['score'],
                'risk_level' => $risk['risk_level'],
                'reasons' => $risk['reasons'],
                'suggested_action' => $risk['suggested_action'],
            ];
        }

        usort($atRisk, fn ($a, $b) => $b['risk_score'] <=> $a['risk_score']);
        $atRisk = array_slice($atRisk, 0, 20);

        $totalPatients = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'paciente'))
            ->count();

        $riskCount = count($atRisk);

        $activeLast30 = User::query()
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->count();

        $engagement = $totalPatients > 0
            ? (int) round(($activeLast30 / $totalPatients) * 100)
            : 0;

        return [
            'kpi_total_patients' => $totalPatients,
            'kpi_risk_patients' => $riskCount,
            'kpi_average_engagement' => $engagement,
            'kpi_recovered_this_month' => (int) DB::table('users')
                ->where('churn_risk', 'Low')
                ->where('updated_at', '>=', now()->startOfMonth())
                ->count(),
            'patients_at_risk' => $atRisk,
            'last_analysis_time' => now()->format('d/m/Y H:i'),
        ];
    }
}
