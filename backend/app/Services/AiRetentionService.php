<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AiRetentionService
{
    /**
     * Calcula o Risco de Evasão (Risk Score) de um paciente/aluno.
     * Quanto maior a pontuação (0 a 100), maior o risco.
     *
     * Fatores:
     * - Frequência de acesso/treino (30% do peso)
     * - Atrasos financeiros (40% do peso)
     * - Engajamento com o App (30% do peso)
     *
     * @param User $user
     * @return array
     */
    public function calculateRiskScore(User $user): array
    {
        $score = 0;
        $reasons = [];

        // 1. Análise de Frequência (Treinos Concluídos nos últimos 30 dias)
        // Como podemos não ter os treinos exatos no mock, fazemos uma aproximação.
        $recentWorkouts = 0; // Idealmente: $user->workoutSessions()->where('created_at', '>=', now()->subDays(30))->count();
        if ($recentWorkouts === 0) {
            $score += 30;
            $reasons[] = 'Nenhum treino registrado nos últimos 30 dias.';
        } elseif ($recentWorkouts < 4) {
            $score += 15;
            $reasons[] = 'Baixa frequência de treinos (menos de 1x por semana).';
        }

        // 2. Análise Financeira (Pagamentos em atraso ou assinatura cancelada/suspensa)
        $hasActiveSubscription = true; // $user->subscriptions()->active()->exists();
        $hasLatePayments = false; // Simulação: verificar parcelas em atraso.
        
        if (!$hasActiveSubscription) {
            $score += 20;
            $reasons[] = 'Assinatura inativa ou suspensa.';
        }
        if ($hasLatePayments) {
            $score += 20;
            $reasons[] = 'Existem pagamentos pendentes ou em atraso.';
        }

        // 3. Engajamento no App (Último login)
        $daysSinceLastLogin = 15; // Simulação: $user->last_login_at ? $user->last_login_at->diffInDays(now()) : 999;
        if ($daysSinceLastLogin > 21) {
            $score += 30;
            $reasons[] = 'Abandono do aplicativo (sem acesso há mais de 3 semanas).';
        } elseif ($daysSinceLastLogin > 7) {
            $score += 10;
            $reasons[] = 'Queda de engajamento no aplicativo recentemente.';
        }

        // Normalização
        $score = min(100, $score);
        
        // Identificação de risco
        $riskLevel = 'Baixo';
        if ($score >= 70) $riskLevel = 'Alto';
        elseif ($score >= 40) $riskLevel = 'Médio';

        // Sugestão da IA
        $suggestedAction = $this->generateSuggestedAction($riskLevel, $reasons);

        return [
            'score' => $score,
            'risk_level' => $riskLevel,
            'reasons' => $reasons,
            'suggested_action' => $suggestedAction,
            'last_calculated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Gera recomendações práticas de retenção baseadas no nível de risco.
     */
    private function generateSuggestedAction(string $riskLevel, array $reasons): string
    {
        if ($riskLevel === 'Alto') {
            return "Recomendamos entrar em contato via WhatsApp oferecendo um incentivo ou reagendando uma avaliação imediata. Motivos principais: " . implode(" ", $reasons);
        } elseif ($riskLevel === 'Médio') {
            return "Envie uma notificação push motivacional ou um lembrete amigável sobre os benefícios de manter a consistência.";
        }

        return "Nenhuma ação corretiva necessária. Continue monitorando o bom engajamento.";
    }

    /**
     * Gera dados simulados do painel administrativo para a dashboard da IA.
     * Retorna alunos em risco e KPIs globais.
     */
    public function getDashboardMetrics(): array
    {
        // Em um cenário real, consultaríamos usuários paginados ordenados pelo score de risco armazenado no DB.
        $patientsAtRisk = [
            [
                'id' => 101,
                'name' => 'Carlos Silva',
                'avatar' => 'https://ui-avatars.com/api/?name=Carlos+Silva&background=0D8ABC&color=fff',
                'risk_score' => 85,
                'risk_level' => 'Alto',
                'reasons' => ['Sem acesso há 25 dias', 'Nenhum treino registrado'],
                'suggested_action' => 'Acionar script de recuperação por WhatsApp com oferta de aula bônus.',
            ],
            [
                'id' => 102,
                'name' => 'Ana Beatriz',
                'avatar' => 'https://ui-avatars.com/api/?name=Ana+Beatriz&background=F44336&color=fff',
                'risk_score' => 60,
                'risk_level' => 'Médio',
                'reasons' => ['Baixa frequência de treinos'],
                'suggested_action' => 'Enviar mensagem de motivação e perguntar se a carga do treino está adequada.',
            ],
            [
                'id' => 103,
                'name' => 'Roberto Alves',
                'avatar' => 'https://ui-avatars.com/api/?name=Roberto+Alves&background=4CAF50&color=fff',
                'risk_score' => 75,
                'risk_level' => 'Alto',
                'reasons' => ['Atraso no pagamento (12 dias)'],
                'suggested_action' => 'Entrar em contato para entender a dificuldade e propor novo acordo financeiro.',
            ]
        ];

        return [
            'kpi_total_patients' => 450,
            'kpi_risk_patients' => 38,
            'kpi_average_engagement' => 68, // Porcentagem
            'kpi_recovered_this_month' => 12,
            'patients_at_risk' => $patientsAtRisk,
            'last_analysis_time' => now()->format('d/m/Y H:i'),
        ];
    }
}
