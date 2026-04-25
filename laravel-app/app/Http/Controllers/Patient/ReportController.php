<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Services\ReportMonetizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportMonetizationService $monetizationService
    ) {}

    /**
     * Index do módulo de relatórios para alunos.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $reports = $this->monetizationService->getAvailableReports($user);

        return view('patient.reports.index', [
            'reports' => $reports,
            'isPremium' => $this->monetizationService->hasPremium($user),
        ]);
    }

    /**
     * Relatórios premium específicos (placeholder) ou redirecionamento para rotas já implementadas.
     */
    public function show(Request $request, string $type): RedirectResponse|View
    {
        $user = $request->user();

        if (! $this->monetizationService->hasPremium($user)) {
            return redirect()->route('patient.reports.index')
                ->with('premium_required', true);
        }

        $allowed = $this->monetizationService->premiumReportIdsForUser($user);
        if (! in_array($type, $allowed, true)) {
            abort(404);
        }

        if ($type === 'physical_evolution') {
            // Relatório de Evolução Física -> Página de Medidas e Fotos
            return redirect()->route('patient.evolution');
        }

        if ($type === 'training_performance') {
            // Desempenho no Treino -> BI de Performance
            return redirect()->route('report', ['tab' => 'performance']);
        }

        if ($type === 'nutritional_analysis') {
            // Relatório Nutricional -> Central de Nutrição Dashboard
            return redirect()->route('nutrition.index', ['tab' => 'dashboard']);
        }

        if ($type === 'frequency_report') {
            // Frequência -> BI Geral (Scrolled to frequency if applicable)
            return redirect()->to(route('report') . '#daily-log');
        }

        if ($type === 'goals_report') {
            // Metas -> BI Geral
            return redirect()->route('report');
        }

        if ($type === 'adherence_index') {
            // Índice de Aderência -> BI Geral
            return redirect()->route('report');
        }

        if ($type === 'export_pdf') {
            return redirect()->route('report.monthly.pdf', [
                'month' => Carbon::now()->format('Y-m'),
            ]);
        }

        $this->monetizationService->logGeneration($user, 'Student Report: '.$type);

        return view('errors.coming-soon', ['feature' => $type]);
    }
}
