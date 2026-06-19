<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Services\ReportMonetizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportMonetizationService $monetizationService
    ) {}

    /**
     * Index do módulo de relatórios para profissionais.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $reports = $this->monetizationService->getAvailableReports($user);

        return view('professional.reports.index', [
            'reports' => $reports,
            'isPremium' => $this->monetizationService->hasPremium($user),
        ]);
    }

    /**
     * Exporta dados de um relatório para CSV.
     */
    public function export(Request $request, string $type)
    {
        $user = $request->user();
        if (! $this->monetizationService->hasPremium($user)) {
            abort(403);
        }

        $format = $request->get('format', 'csv');
        $data = [];
        
        if ($format === 'csv') {
            return $this->exportCsv($user, $type, $request);
        }

        if ($format === 'pdf') {
            return $this->exportPdf($user, $type, $request);
        }

        abort(400, 'Formato não suportado');
    }

    private function exportCsv($user, $type, $request)
    {
        $data = [];
        $filename = "relatorio_{$type}_" . now()->format('YmdHis') . ".csv";

        switch ($type) {
            case 'complete_analytics':
                $range = (int) $request->get('range', 30);
                $agg = \App\Services\ProfessionalReportAggregator::studentPerformance($user->id, now()->subDays($range), now());
                $data[] = ['Nome', 'Email', 'Treinos', 'Dias Alimentacao', 'Aderencia Treino %', 'Aderencia Nutri %'];
                foreach ($agg['students_data'] as $s) {
                    $data[] = [$s['name'], $s['email'], $s['workouts'], $s['food_days'], $s['adherence_training'], $s['adherence_food']];
                }
                break;

            case 'detailed_finance':
                $agg = \App\Services\ProfessionalReportAggregator::studentFinancials($user->id);
                $data[] = ['Paciente', 'Plano', 'Status', 'Valor'];
                foreach ($agg['subscriptions'] as $s) {
                    $data[] = [$s['user']['name'] ?? 'N/D', $s['plan']['name'] ?? 'N/D', $s['status'] ?? 'N/D', $s['plan']['price'] ?? 0];
                }
                break;
            
            case 'management_reports':
                $agg = \App\Services\ProfessionalReportAggregator::managementSummary($user->id);
                $data[] = ['Paciente', 'Email', 'Ultima Atividade', 'Risco'];
                foreach ($agg['churn_risk'] as $s) {
                    $data[] = [$s['name'], $s['email'], $s['last_activity'], $s['risk_level']];
                }
                break;
        }

        $handle = fopen('php://output', 'w');
        fputs($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($data as $row) {
            fputcsv($handle, $row, ';');
        }

        fclose($handle);
        exit;
    }

    private function exportPdf($user, $type, $request)
    {
        $agg = [];
        $view = '';
        $title = '';

        switch ($type) {
            case 'complete_analytics':
                $range = (int) $request->get('range', 30);
                $agg = \App\Services\ProfessionalReportAggregator::studentPerformance($user->id, now()->subDays($range), now());
                $view = 'professional.reports.pdf.analytics';
                $title = "Relatório de Performance - {$range} Dias";
                break;
            case 'detailed_finance':
                $agg = \App\Services\ProfessionalReportAggregator::studentFinancials($user->id);
                $view = 'professional.reports.pdf.finance';
                $title = "Relatório Financeiro Detalhado";
                break;
        }

        if (!$view) abort(404);

        $html = view($view, ['data' => $agg, 'user' => $user, 'title' => $title])->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="relatorio_' . $type . '.pdf"');
    }

    /**
     * Relatórios premium específicos (placeholder) — apenas IDs listados em getAvailableReports.
     */
    public function show(Request $request, string $type): RedirectResponse|View
    {
        $user = $request->user();

        if (! $this->monetizationService->hasPremium($user)) {
            return redirect()->route('professional.reports.index')
                ->with('premium_required', true);
        }

        $allowed = $this->monetizationService->premiumReportIdsForUser($user);
        if (! in_array($type, $allowed, true)) {
            abort(404);
        }

        $this->monetizationService->logGeneration($user, $type);

        if ($type === 'complete_analytics') {
            $range = (int) $request->get('range', 30);
            $end = now();
            $start = now()->subDays($range - 1);

            $data = \App\Services\ProfessionalReportAggregator::studentPerformance(
                $user->id,
                $start,
                $end
            );

            return view('professional.reports.analytics', compact('data', 'range', 'start', 'end'));
        }

        if ($type === 'detailed_finance') {
            $data = \App\Services\ProfessionalReportAggregator::studentFinancials($user->id);
            return view('professional.reports.finance', compact('data'));
        }

        if ($type === 'comparative') {
            $data = \App\Services\ProfessionalReportAggregator::studentComparison($user->id);
            return view('professional.reports.comparative', compact('data'));
        }

        if ($type === 'kpi_dashboard') {
            $data = \App\Services\ProfessionalReportAggregator::kpiSummary($user->id);
            return view('professional.reports.kpis', compact('data'));
        }

        if ($type === 'management_reports') {
            $data = \App\Services\ProfessionalReportAggregator::managementSummary($user->id);
            return view('professional.reports.management', compact('data'));
        }

        if ($type === 'professional_performance') {
            if (! $user->academy_company_id) {
                return back()->with('error', 'Este relatório requer vínculo com uma unidade/clínica.');
            }
            $data = \App\Services\ProfessionalReportAggregator::companyPerformance($user->academy_company_id);
            return view('professional.reports.professionals', compact('data'));
        }

        if ($type === 'scheduled_reports') {
            return view('professional.reports.scheduled');
        }

        return view('errors.coming-soon', ['feature' => $type]);
    }
}


