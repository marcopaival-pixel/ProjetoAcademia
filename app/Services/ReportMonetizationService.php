<?php

namespace App\Services;

use App\Models\User;
use App\Models\AdminLog;
use Illuminate\Support\Carbon;

class ReportMonetizationService
{
    /**
     * Define as limitações do plano Free em dias.
     */
    public const FREE_PLAN_DAYS_LIMIT = 30;

    /**
     * Verifica se o usuário tem acesso premium.
     */
    public function hasPremium(User $user): bool
    {
        return $user->hasPremiumAccess();
    }

    /**
     * Aplica o filtro de data baseado no plano do usuário.
     * Se Free, limita aos últimos 30 dias.
     */
    public function applyDateLimit(User $user, Carbon $start, Carbon $end): array
    {
        if ($this->hasPremium($user)) {
            return [$start, $end];
        }

        $limitDate = Carbon::today()->subDays(self::FREE_PLAN_DAYS_LIMIT);
        
        // Se a data de início solicitada for anterior ao limite, ajusta para o limite.
        if ($start->lt($limitDate)) {
            $start = $limitDate->copy();
        }

        // Garante que o fim não seja posterior a hoje (opcional, mas bom para consistência)
        if ($end->gt(Carbon::today())) {
            $end = Carbon::today();
        }

        return [$start, $end];
    }

    /**
     * Registra log de geração de relatório.
     */
    public function logGeneration(User $user, string $reportName, array $params = []): void
    {
        AdminLog::create([
            'user_id' => $user->id,
            'action' => 'report_generated',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => [
                'report' => $reportName,
                'params' => $params,
                'plan' => $this->hasPremium($user) ? 'Premium' : 'Free',
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Retorna a lista de relatórios disponíveis.
     */
    public function getAvailableReports(User $user): array
    {
        if ($user->hasRole('aluno')) {
            return [
                'free' => [
                    ['id' => 'performance_basic', 'kind' => 'report', 'label' => 'Performance (30 dias)', 'icon' => 'fas fa-chart-line', 'route' => 'report'],
                    ['id' => 'workout_log', 'kind' => 'shortcut', 'label' => 'Registro de Treinos', 'icon' => 'fas fa-history', 'route' => 'exercise'],
                    ['id' => 'agenda', 'kind' => 'shortcut', 'label' => 'Minha Agenda', 'icon' => 'fas fa-calendar-alt', 'route' => 'agenda.index'],
                ],
                'premium' => [
                    [
                        'id' => 'physical_evolution', 
                        'label' => 'Evolução Física Detalhada', 
                        'icon' => 'fas fa-ruler-combined', 
                        'premium' => true, 
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'physical_evolution']
                    ],
                    [
                        'id' => 'training_performance', 
                        'label' => 'Desempenho no Treino', 
                        'icon' => 'fas fa-dumbbell', 
                        'premium' => true, 
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'training_performance']
                    ],
                    [
                        'id' => 'nutritional_analysis', 
                        'label' => 'Relatório Nutricional BI', 
                        'icon' => 'fas fa-apple-alt', 
                        'premium' => true,
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'nutritional_analysis']
                    ],
                    [
                        'id' => 'frequency_report', 
                        'label' => 'Frequência & Assiduidade', 
                        'icon' => 'fas fa-calendar-check', 
                        'premium' => true,
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'frequency_report']
                    ],
                    [
                        'id' => 'goals_report', 
                        'label' => 'Metas & Conquistas', 
                        'icon' => 'fas fa-trophy', 
                        'premium' => true,
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'goals_report']
                    ],
                    [
                        'id' => 'adherence_index', 
                        'label' => 'Índice de Aderência Geral', 
                        'icon' => 'fas fa-percentage', 
                        'premium' => true,
                        'route' => 'patient.reports.show', 
                        'route_params' => ['type' => 'adherence_index']
                    ],
                    [
                        'id' => 'bioimpedance_technical',
                        'label' => 'Laudo Técnico de Bioimpedância',
                        'icon' => 'fas fa-microscope',
                        'premium' => true,
                        'route' => 'bioimpedance.latest',
                    ],
                    [
                        'id' => 'clinical_report',
                        'label' => 'Laudo Clínico de Evolução',
                        'icon' => 'fas fa-file-medical',
                        'premium' => true,
                        'route' => 'patient.export-laudo',
                    ],
                    [
                        'id' => 'export_pdf',
                        'label' => 'Exportar Relatório Mensal (PDF)',
                        'icon' => 'fas fa-file-pdf',
                        'premium' => true,
                        'route' => 'report.monthly.pdf',
                        'route_params' => ['month' => Carbon::now()->format('Y-m')],
                    ],
                    [
                        'id' => 'full_history',
                        'label' => 'Histórico Completo',
                        'icon' => 'fas fa-archive',
                        'premium' => true,
                        'route' => 'patient.reports.show',
                        'route_params' => ['type' => 'full_history'],
                    ],
                ],
            ];
        }

        return [
            'free' => [
                ['id' => 'patients_list', 'kind' => 'report', 'label' => 'Lista de Alunos', 'icon' => 'fas fa-users', 'route' => 'professional.patients.index'],
                ['id' => 'daily_agenda', 'kind' => 'report', 'label' => 'Agenda do Dia', 'icon' => 'fas fa-calendar-day', 'route' => 'agenda.index'],
                ['id' => 'presence', 'kind' => 'shortcut', 'label' => 'Frequência de Alunos', 'icon' => 'fas fa-check-double', 'route' => 'professional.dashboard', 'note' => 'Resumo no painel até o módulo dedicado'],
                ['id' => 'basic_history', 'kind' => 'report', 'label' => 'Histórico de Performance', 'icon' => 'fas fa-history', 'route' => 'report'],
                ['id' => 'financial_summary', 'kind' => 'shortcut', 'label' => 'Resumo Financeiro (Geral)', 'icon' => 'fas fa-wallet', 'route' => 'professional.dashboard', 'note' => 'Indicadores no dashboard profissional'],
                ['id' => 'services_performed', 'kind' => 'shortcut', 'label' => 'Atendimentos Realizados', 'icon' => 'fas fa-handshake', 'route' => 'professional.dashboard', 'note' => 'Resumo no dashboard profissional'],
            ],
            'premium' => [
                ['id' => 'complete_analytics', 'label' => 'Relatórios de Performance Completos', 'icon' => 'fas fa-chart-line', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'complete_analytics']],
                ['id' => 'detailed_finance', 'label' => 'Relatórios Financeiros Detalhados', 'icon' => 'fas fa-file-invoice-dollar', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'detailed_finance']],
                ['id' => 'comparative', 'label' => 'Relatórios Comparativos (Evolução)', 'icon' => 'fas fa-balance-scale', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'comparative']],
                ['id' => 'management_reports', 'label' => 'Relatórios Gerenciais & Churn', 'icon' => 'fas fa-tasks', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'management_reports']],
                ['id' => 'professional_performance', 'label' => 'Relatórios por Profissional', 'icon' => 'fas fa-user-tie', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'professional_performance']],
                ['id' => 'kpi_dashboard', 'label' => 'Dashboard com Indicadores (KPIs)', 'icon' => 'fas fa-tachometer-alt', 'premium' => true, 'route' => 'professional.reports.show', 'route_params' => ['type' => 'kpi_dashboard']],
                ['id' => 'scheduled_reports', 'label' => 'Agendamento de Relatórios Automáticos', 'icon' => 'fas fa-clock', 'premium' => true],
            ],
        ];
    }

    /**
     * IDs de relatórios premium válidos para o utilizador (validação de rotas /view/{type}).
     *
     * @return list<string>
     */
    public function premiumReportIdsForUser(User $user): array
    {
        $premium = $this->getAvailableReports($user)['premium'] ?? [];

        return array_values(array_filter(array_map(
            static fn (array $item) => $item['id'] ?? null,
            $premium
        )));
    }
}
