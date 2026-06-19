<?php

namespace App\Services;

use App\Models\AcademyCompany;
use App\Models\Clinic;
use App\Models\ExerciseEntry;
use App\Models\FoodEntry;
use App\Models\Subscription;
use App\Models\User;
use App\Support\SubscriptionStatus;
use Carbon\Carbon;
use App\Support\AppVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardService
{
    /** Coordenadas aproximadas para mapa interativo (cidades brasileiras comuns). */
    private const CITY_COORDS = [
        'São Paulo' => [-23.5505, -46.6333],
        'Rio de Janeiro' => [-22.9068, -43.1729],
        'Belo Horizonte' => [-19.9167, -43.9345],
        'Brasília' => [-15.7975, -47.8919],
        'Curitiba' => [-25.4284, -49.2733],
        'Porto Alegre' => [-30.0346, -51.2177],
        'Salvador' => [-12.9714, -38.5014],
        'Fortaleza' => [-3.7172, -38.5433],
        'Recife' => [-8.0476, -34.8770],
        'Manaus' => [-3.1190, -60.0217],
        'Goiânia' => [-16.6869, -49.2648],
        'Campinas' => [-22.9099, -47.0626],
        'Florianópolis' => [-27.5954, -48.5480],
        'Vitória' => [-20.3155, -40.3128],
        'Natal' => [-5.7945, -35.2110],
        'João Pessoa' => [-7.1195, -34.8450],
        'Maceió' => [-9.6658, -35.7353],
        'Cuiabá' => [-15.6014, -56.0979],
        'Belém' => [-1.4558, -48.4902],
        'Santos' => [-23.9608, -46.3336],
    ];

    /**
     * Agrega todas as métricas do Dashboard Executivo Inteligente.
     */
    public function getDashboardData(): array
    {
        return Cache::remember($this->cacheKey(), 300, function () {
            $overview = AdminOverviewStats::collect();
            $saasMetrics = app(SaaSMetricsService::class);

            $activeUsers = User::whereIn('status', ['active', 'ATIVO', 'APROVADO'])->count();
            $inactiveUsers = User::whereIn('status', ['blocked', 'inactive', 'INATIVO'])->count();
            $pendingUsers = User::where('status', 'PENDENTE_APROVACAO')->count();

            $totalClinics = Clinic::count();
            $totalAcademies = AcademyCompany::count();
            $activeAcademies = AcademyCompany::where('is_active', true)->count();

            $financial = app(FinancialMetricsService::class);

            $thisMonthRevenue = $financial->monthlyRevenue();
            $lastMonthRevenue = $financial->monthlyRevenue(now()->subMonth());
            $annualRevenue = $financial->annualRevenue();

            $revenueGrowth = $lastMonthRevenue > 0
                ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
                : ($thisMonthRevenue > 0 ? 100 : 0);

            $delinquentCount = Subscription::whereCanonicalStatus(...SubscriptionStatus::delinquent())->count();

            $cancellationsThisMonth = Subscription::whereCanonicalStatus(SubscriptionStatus::CANCELLED)
                ->whereMonth('cancelled_at', now()->month)
                ->whereYear('cancelled_at', now()->year)
                ->count();

            $commissions = app(CommissionMetricsService::class)->summary();

            $churnRate = $saasMetrics->getChurnRate();

            $premiumActive = $overview['premium_subscriptions_active'];
            $retentionRate = $overview['total_users'] > 0
                ? round(($overview['active_users_30d'] / max(1, $overview['total_users'])) * 100, 1)
                : 0;

            $usersByCity = DB::table('user_profiles')
                ->select('city', DB::raw('count(*) as total'))
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->groupBy('city')
                ->orderByDesc('total')
                ->limit(15)
                ->get();

            $cityRanking = $usersByCity->take(10)->values();

            $clinicRanking = AcademyCompany::query()
                ->leftJoin('users', 'users.academy_company_id', '=', 'academy_companies.id')
                ->select(
                    'academy_companies.id',
                    'academy_companies.name',
                    'academy_companies.city',
                    'academy_companies.is_active',
                    DB::raw('count(users.id) as users_count')
                )
                ->groupBy(
                    'academy_companies.id',
                    'academy_companies.name',
                    'academy_companies.city',
                    'academy_companies.is_active'
                )
                ->orderByDesc('users_count')
                ->limit(10)
                ->get();

            $monthlyGrowth = $this->getMonthlyUserGrowth(12);
            $monthlyRevenue = $this->getMonthlyRevenue(12);
            $systemEvolution = $this->getSystemEvolution(12);

            $atRiskStudents = User::query()
                ->where(function ($q) {
                    $q->whereIn('churn_risk', ['High', 'Medium'])
                        ->orWhere('last_activity_at', '<', now()->subDays(21));
                })
                ->where('is_admin', false)
                ->orderByRaw("CASE WHEN churn_risk = 'High' THEN 1 WHEN churn_risk = 'Medium' THEN 2 ELSE 3 END")
                ->limit(8)
                ->get(['id', 'name', 'email', 'churn_risk', 'last_activity_at', 'health_score']);

            $usageFrequency = $this->getUsageFrequency();

            $mapMarkers = $this->buildMapMarkers($usersByCity);

            $kpis = [
                'total_users' => $overview['total_users'],
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'pending_users' => $pendingUsers,
                'premium_active' => $premiumActive,
                'active_users_30d' => $overview['active_users_30d'],
                'new_users_7d' => $overview['new_users_7d'],
                'total_clinics' => $totalClinics,
                'total_academies' => $totalAcademies,
                'active_academies' => $activeAcademies,
                'mrr' => $saasMetrics->calculateMRR(),
                'total_revenue' => $financial->totalRevenue(),
                'daily_revenue' => $financial->dailyRevenue(),
                'monthly_revenue' => $thisMonthRevenue,
                'annual_revenue' => $annualRevenue,
                'average_ticket' => $financial->averageTicket(),
                'estimated_ltv' => $financial->estimatedLtv(),
                'estimated_cac' => $financial->estimatedCac(),
                'revenue_growth' => round($revenueGrowth, 1),
                'retention_rate' => $retentionRate,
                'delinquency_count' => $delinquentCount,
                'cancellations_month' => $cancellationsThisMonth,
                'churn_rate' => $churnRate,
                'at_risk_count' => User::whereIn('churn_risk', ['High', 'Medium'])->count(),
                'usage_frequency_pct' => $usageFrequency['frequency_pct'],
                'commissions_pending_total' => $commissions['pending_total'],
                'commissions_pending_count' => $commissions['pending_count'],
                'commissions_available_total' => $commissions['available_total'],
                'commissions_paid_month' => $commissions['paid_month'],
                'commissions_generated_month' => $commissions['generated_month'],
                'commissions_clawback_month' => $commissions['clawback_month'],
                'commissions_cancelled_month' => $commissions['cancelled_month'],
            ];

            $ai = $this->generateAiInsights($kpis, $monthlyGrowth, $atRiskStudents);

            $operations = app(\App\Services\Operations\SystemHealthService::class)->checkAll();

            return [
                'kpis' => $kpis,
                'operations' => [
                    'status' => $operations['status'],
                    'pending_jobs' => $operations['jobs']['pending'] ?? 0,
                    'failed_jobs' => $operations['jobs']['failed'] ?? 0,
                    'errors_24h' => \App\Models\SystemError::where('created_at', '>=', now()->subDay())->count(),
                    'disk_used_percent' => $operations['disk']['used_percent'] ?? null,
                ],
                'users_by_city' => $usersByCity,
                'city_ranking' => $cityRanking,
                'clinic_ranking' => $clinicRanking,
                'monthly_growth' => $monthlyGrowth,
                'monthly_revenue' => $monthlyRevenue,
                'system_evolution' => $systemEvolution,
                'at_risk_students' => $atRiskStudents,
                'usage_frequency' => $usageFrequency,
                'map_markers' => $mapMarkers,
                'ai' => $ai,
                'last_updated' => now()->format('d/m/Y H:i'),
            ];
        });
    }

    /**
     * Invalida cache após eventos relevantes (webhooks, etc.).
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    private function cacheKey(): string
    {
        return 'executive_dashboard_v_' . AppVersion::current();
    }

    private function getMonthlyUserGrowth(int $months): array
    {
        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = User::select(
            DB::raw("{$monthExpr} as month"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy(DB::raw($monthExpr))
            ->orderBy('month')
            ->get();

        return $rows->map(fn ($r) => [
            'month' => $r->month,
            'label' => Carbon::createFromFormat('Y-m', $r->month)->translatedFormat('M/y'),
            'total' => (int) $r->total,
        ])->values()->all();
    }

    private function getMonthlyRevenue(int $months): array
    {
        return app(FinancialMetricsService::class)->monthlyRevenueSeries($months);
    }

    private function getSystemEvolution(int $months): array
    {
        $growth = collect($this->getMonthlyUserGrowth($months))->keyBy('month');
        $revenue = collect($this->getMonthlyRevenue($months))->keyBy('month');

        $months = $growth->keys()->merge($revenue->keys())->unique()->sort()->values();

        return $months->map(function ($month) use ($growth, $revenue) {
            return [
                'month' => $month,
                'label' => Carbon::createFromFormat('Y-m', $month)->translatedFormat('M/y'),
                'users' => $growth->get($month)['total'] ?? 0,
                'revenue' => $revenue->get($month)['total'] ?? 0,
            ];
        })->values()->all();
    }

    private function getUsageFrequency(): array
    {
        $since30 = now()->subDays(30)->toDateString();

        $activeLoggers = (int) FoodEntry::where('entry_date', '>=', $since30)
            ->distinct('user_id')
            ->count('user_id');

        $activeExercisers = (int) ExerciseEntry::where('entry_date', '>=', $since30)
            ->distinct('user_id')
            ->count('user_id');

        $foodUserIds = FoodEntry::where('entry_date', '>=', $since30)->pluck('user_id');
        $exerciseUserIds = ExerciseEntry::where('entry_date', '>=', $since30)->pluck('user_id');
        $combinedActive = $foodUserIds->merge($exerciseUserIds)->unique()->count();

        $totalUsers = max(1, User::count());
        $frequencyPct = round(($combinedActive / $totalUsers) * 100, 1);

        return [
            'food_loggers_30d' => $activeLoggers,
            'exercise_loggers_30d' => $activeExercisers,
            'combined_active_30d' => $combinedActive,
            'frequency_pct' => $frequencyPct,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection  $usersByCity
     */
    private function buildMapMarkers($usersByCity): array
    {
        $markers = [];

        foreach ($usersByCity as $row) {
            $city = trim((string) $row->city);
            $coords = $this->resolveCityCoords($city);

            if ($coords === null) {
                continue;
            }

            $markers[] = [
                'city' => $city,
                'users' => (int) $row->total,
                'lat' => $coords[0],
                'lng' => $coords[1],
            ];
        }

        return $markers;
    }

    private function resolveCityCoords(string $city): ?array
    {
        foreach (self::CITY_COORDS as $known => $coords) {
            if (strcasecmp($city, $known) === 0 || str_contains(strtolower($city), strtolower($known))) {
                return $coords;
            }
        }

        return null;
    }

    /**
     * Motor de insights administrativos (regras + heurísticas sobre dados reais).
     */
    private function generateAiInsights(array $kpis, array $monthlyGrowth, $atRiskStudents): array
    {
        $positives = [];
        $negatives = [];
        $risks = [];
        $predictions = [];
        $solutions = [];
        $insights = [];

        if (($kpis['commissions_pending_total'] ?? 0) > 500) {
            $risks[] = 'R$ '.number_format($kpis['commissions_pending_total'], 2, ',', '.').' em comissões pendentes — monitorar liberações e saques.';
        }
        if (($kpis['commissions_clawback_month'] ?? 0) > 0) {
            $negatives[] = 'R$ '.number_format($kpis['commissions_clawback_month'], 2, ',', '.').' em clawback de comissões neste mês (estornos).';
        }

        if ($kpis['revenue_growth'] > 10) {
            $positives[] = "Faturamento mensal cresceu {$kpis['revenue_growth']}% — tração comercial positiva.";
        }
        if ($kpis['retention_rate'] >= 40) {
            $positives[] = "Taxa de engajamento de {$kpis['retention_rate']}% indica base ativa saudável.";
        }
        if ($kpis['new_users_7d'] > 0) {
            $positives[] = "{$kpis['new_users_7d']} novos usuários nos últimos 7 dias — pipeline de aquisição ativo.";
        }
        if ($kpis['active_academies'] > 0) {
            $positives[] = "{$kpis['active_academies']} academias/clínicas ativas na plataforma.";
        }

        if ($kpis['revenue_growth'] < -5) {
            $negatives[] = "Queda de {$kpis['revenue_growth']}% no faturamento vs mês anterior.";
        }
        if ($kpis['delinquency_count'] > 0) {
            $negatives[] = "{$kpis['delinquency_count']} assinaturas em inadimplência — impacto direto no caixa.";
        }
        if ($kpis['inactive_users'] > $kpis['active_users'] * 0.3) {
            $negatives[] = "Proporção elevada de usuários inativos ({$kpis['inactive_users']}) vs ativos.";
        }
        if ($kpis['cancellations_month'] > 0) {
            $negatives[] = "{$kpis['cancellations_month']} cancelamentos registrados neste mês.";
        }

        if ($kpis['at_risk_count'] > 5) {
            $risks[] = "{$kpis['at_risk_count']} alunos classificados em risco de evasão (churn médio/alto).";
        }
        if ($kpis['churn_rate'] > 5) {
            $risks[] = "Churn rate de {$kpis['churn_rate']}% — acima do benchmark SaaS saudável (~3-5%).";
        }
        if ($kpis['usage_frequency_pct'] < 25) {
            $risks[] = "Frequência de uso baixa ({$kpis['usage_frequency_pct']}%) — risco de abandono silencioso.";
        }

        $growthTrend = count($monthlyGrowth) >= 2
            ? ($monthlyGrowth[count($monthlyGrowth) - 1]['total'] ?? 0) - ($monthlyGrowth[count($monthlyGrowth) - 2]['total'] ?? 0)
            : 0;

        if ($growthTrend < 0 && $kpis['at_risk_count'] > 3) {
            $predictions[] = 'Modelo preditivo: probabilidade elevada de cancelamentos nos próximos 30 dias (queda de novos cadastros + alunos em risco).';
        }
        if ($kpis['delinquency_count'] > 2) {
            $predictions[] = "Previsão: até " . min($kpis['delinquency_count'], 5) . ' contas podem migrar para cancelamento se não houver cobrança ativa em 15 dias.';
        }
        if ($atRiskStudents->count() > 0) {
            $highRisk = $atRiskStudents->where('churn_risk', 'High')->count();
            if ($highRisk > 0) {
                $predictions[] = "{$highRisk} aluno(s) com risco alto identificado(s) — ação preventiva recomendada esta semana.";
            }
        }

        if ($kpis['at_risk_count'] > 0) {
            $solutions[] = 'Acionar fluxo de retenção via WhatsApp/e-mail para alunos em risco (módulo IA de Retenção).';
        }
        if ($kpis['delinquency_count'] > 0) {
            $solutions[] = 'Disparar régua de cobrança automática e oferta de renegociação para inadimplentes.';
        }
        if ($kpis['usage_frequency_pct'] < 30) {
            $solutions[] = 'Campanha push de reengajamento com desafio semanal e lembrete de treino personalizado.';
        }
        if ($kpis['revenue_growth'] < 0) {
            $solutions[] = 'Revisar funil comercial e ativar cupom promocional para conversão de leads pendentes.';
        }

        $insights[] = 'Comissões: R$ '.number_format($kpis['commissions_available_total'] ?? 0, 2, ',', '.').' disponíveis | R$ '.number_format($kpis['commissions_paid_month'] ?? 0, 2, ',', '.').' pagas no mês.';
        $insights[] = "MRR atual: R$ " . number_format($kpis['mrr'], 2, ',', '.') . " | Receita anual acumulada: R$ " . number_format($kpis['annual_revenue'], 2, ',', '.');
        $insights[] = "Retenção operacional: {$kpis['retention_rate']}% dos usuários com atividade nos últimos 30 dias.";
        $insights[] = 'Última varredura NexBot concluída — dados consolidados em tempo quasi-real (cache 5 min).';

        if (empty($positives)) {
            $positives[] = 'Sistema operacional estável — nenhuma anomalia crítica detectada nos indicadores positivos.';
        }
        if (empty($negatives)) {
            $negatives[] = 'Nenhum ponto negativo crítico identificado no período atual.';
        }
        if (empty($risks)) {
            $risks[] = 'Perfil de risco dentro dos parâmetros normais de operação.';
        }
        if (empty($predictions)) {
            $predictions[] = 'Tendência estável — sem picos de cancelamento previstos para os próximos 30 dias.';
        }
        if (empty($solutions)) {
            $solutions[] = 'Manter monitoramento contínuo e revisar dashboard semanalmente.';
        }

        return [
            'positives' => $positives,
            'negatives' => $negatives,
            'risks' => $risks,
            'predictions' => $predictions,
            'solutions' => $solutions,
            'insights' => $insights,
            'summary' => $this->buildExecutiveSummary($kpis),
        ];
    }

    private function buildExecutiveSummary(array $kpis): string
    {
        if ($kpis['revenue_growth'] > 15 && $kpis['retention_rate'] >= 50) {
            return 'Operação em excelência: crescimento acelerado com base engajada. Momento ideal para escalar aquisição.';
        }
        if ($kpis['delinquency_count'] > 5 || $kpis['churn_rate'] > 8) {
            return 'Alerta executivo: pressão financeira e risco de churn elevados. Priorizar cobrança e retenção imediata.';
        }
        if ($kpis['at_risk_count'] > 10) {
            return 'Atenção: volume significativo de alunos em risco. Ativar protocolos de retenção preventiva.';
        }

        return 'NexShape opera dentro dos parâmetros esperados. Monitoramento contínuo recomendado.';
    }
}
