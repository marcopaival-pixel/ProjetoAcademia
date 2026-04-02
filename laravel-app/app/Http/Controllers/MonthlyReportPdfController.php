<?php

namespace App\Http\Controllers;

use App\Services\MonthlyReportAggregator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class MonthlyReportPdfController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        if (! $user->hasPremiumAccess()) {
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

        if (! class_exists(Dompdf::class)) {
            return response(
                'Geração de PDF indisponível: execute composer update na pasta laravel-app (pacote dompdf).',
                503,
                ['Content-Type' => 'text/plain; charset=UTF-8']
            );
        }

        $uid = (int) $user->id;
        $data = MonthlyReportAggregator::forUserMonth($uid, $start, $end);

        $html = view('pdf.monthly-report', [
            'user' => $user,
            'monthLabel' => $start->translatedFormat('F Y'),
            'rangeLabel' => $start->format('d/m/Y').' — '.$end->format('d/m/Y'),
            ...$data,
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $slug = 'projetoacademia_relatorio_'.$start->format('Y-m').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$slug.'"',
        ]);
    }
}
