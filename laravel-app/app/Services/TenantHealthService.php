<?php

namespace App\Services;

use App\Models\AcademyCompany;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenantHealthService
{
    /**
     * Calcula o Health Score de uma AcademyCompany (Tenant).
     * Pesos propostos:
     * - 40%: Engajamento dos alunos (alunos ativos / total de alunos cadastrados).
     * - 30%: Adimplência (percentual de pagamentos não atrasados).
     * - 30%: Uso de ferramentas core (ex: uso do App/IA).
     */
    public function calculateHealthScore(AcademyCompany $company): array
    {
        $score = 0;
        $reasons = [];

        // 1. Engajamento de Alunos (40 pontos)
        $totalStudents = $company->users()->whereHas('roles', fn ($q) => $q->where('name', 'patient')->orWhere('name', 'student'))->count();
        if ($totalStudents === 0) {
            $score += 0;
            $reasons[] = 'Nenhum aluno cadastrado.';
        } else {
            $activeStudents = $company->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'patient')->orWhere('name', 'student'))
                ->whereNotNull('last_activity_at')
                ->where('last_activity_at', '>=', now()->subDays(30))
                ->count();

            $engagementRatio = $activeStudents / $totalStudents;
            $engagementScore = (int) round($engagementRatio * 40);
            $score += $engagementScore;

            if ($engagementRatio < 0.3) {
                $reasons[] = 'Baixo engajamento: menos de 30% dos alunos estão usando o app.';
            } elseif ($engagementRatio > 0.7) {
                $reasons[] = 'Ótimo engajamento de alunos no app.';
            }
        }

        // 2. Adimplência da Academia com a plataforma (30 pontos)
        // Se a academia tem assinatura ativa e sem atrasos
        $activeSubscription = $company->subscriptions()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, 'active'])
            ->exists();

        $overdueSubscription = $company->subscriptions()
            ->whereIn('status', [Subscription::STATUS_OVERDUE, 'overdue'])
            ->exists();

        if ($overdueSubscription) {
            $score += 0;
            $reasons[] = 'Inadimplência: existem faturas pendentes da academia com a plataforma.';
        } elseif ($activeSubscription) {
            $score += 30;
            $reasons[] = 'Assinatura em dia.';
        } else {
            // Em período de trial ou sem assinatura
            $score += 15;
            $reasons[] = 'Conta sem assinatura ativa (pode estar em trial).';
        }

        // 3. Uso de IA e Funcionalidades Core (30 pontos)
        // Avaliando geração de treinos ou uso de assistentes (simplificado)
        // Verificar se existe a tabela ai_usages, se não usar um log genérico
        $aiUsage = 0;
        if (DB::getSchemaBuilder()->hasTable('ai_usages')) {
            $aiUsage = DB::table('ai_usages')->where('company_id', $company->id)->where('created_at', '>=', now()->subDays(30))->count();
        }
        
        if ($aiUsage > 10) {
            $score += 30;
            $reasons[] = 'Alto uso da Inteligência Artificial.';
        } elseif ($aiUsage > 0) {
            $score += 15;
            $reasons[] = 'Uso moderado da Inteligência Artificial.';
        } else {
            $score += 0;
            $reasons[] = 'Não utilizou recursos de Inteligência Artificial nos últimos 30 dias.';
        }

        // Limita score entre 0 e 100
        $score = max(0, min(100, $score));

        // Define o nível de risco de churn
        $riskLevel = 'Baixo'; // Saudável
        if ($score < 40) {
            $riskLevel = 'Alto';
        } elseif ($score < 70) {
            $riskLevel = 'Médio';
        }

        return [
            'score' => $score,
            'risk_level' => $riskLevel,
            'reasons' => $reasons,
        ];
    }

    /**
     * Atualiza o Health Score no banco de dados.
     */
    public function updateHealthScore(AcademyCompany $company): void
    {
        $healthData = $this->calculateHealthScore($company);

        $company->update([
            'health_score' => $healthData['score'],
            'churn_risk_level' => $healthData['risk_level'],
            'health_reasons' => $healthData['reasons'],
            'last_health_calculated_at' => now(),
        ]);
    }
}
