<?php

namespace App\Http\Controllers;

use App\Services\DompdfPdfService;
use App\Services\MonthlyReportAggregator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class MonthlyReportPdfController extends Controller
{
    public function __invoke(Request $request, DompdfPdfService $dompdfPdf, \App\Services\ReportMonetizationService $monetizationService): Response
    {
        $user = $request->user();
        if (! $monetizationService->hasPremium($user)) {
            return response(
                'Relatório PDF mensal é um recurso Premium. Abra Meu Plano para assinar.',
                403,
                ['Content-Type' => 'text/plain; charset=UTF-8']
            );
        }

        $monthRaw = (string) $request->query('month', Carbon::now()->format('Y-m'));
        if (! preg_match('/^(\d{4})-(\d{2})$/', $monthRaw)) {
            return response('Parâmetro month inválido. Use o formato AAAA-MM.', 400, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        $start = Carbon::createFromFormat('Y-m-d', $monthRaw.'-01')->startOfMonth();
        $endMonth = $start->copy()->endOfMonth();
        $today = Carbon::today();
        if ($start->isFuture()) {
            return response('Mês inválido.', 400, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        $end = $endMonth->gt($today) ? $today : $endMonth;

        $uid = (int) $user->id;
        $data = MonthlyReportAggregator::forUserMonth($uid, $start, $end);

        // Sistema de Versionamento e Validação
        $validationService = app(\App\Services\ReportValidationService::class);
        $reportRecord = $validationService->generateVersion($user, 'monthly_performance');
        $validationUrl = $validationService->getValidationUrl($reportRecord);

        // Gera QR Code Localmente
        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($validationUrl)
            ->size(150)
            ->margin(0)
            ->build();
        
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode->getString());

        $html = view('pdf.monthly-report', [
            'user' => $user,
            'monthLabel' => $start->translatedFormat('F Y'),
            'rangeLabel' => $start->format('d/m/Y').' — '.$end->format('d/m/Y'),
            'reportRecord' => $reportRecord,
            'validationUrl' => $validationUrl,
            'qrCode' => $qrCodeBase64,
            ...$data,
        ])->render();

        // Registrar log de geração
        $monetizationService->logGeneration($user, 'Monthly PDF Report', [
            'month' => $monthRaw,
            'version' => $reportRecord->version,
            'doc_id' => $reportRecord->document_id
        ]);

        try {
            $binary = $dompdfPdf->render($html, 'A4', 'portrait', true, 'DejaVu Sans');
        } catch (\RuntimeException) {
            return response(
                'Geração de PDF indisponível: execute composer update na pasta laravel-app (pacote dompdf).',
                503,
                ['Content-Type' => 'text/plain; charset=UTF-8']
            );
        }

        $slug = 'projetoacademia_relatorio_'.$start->format('Y-m').'.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$slug.'"',
        ]);
    }
}
