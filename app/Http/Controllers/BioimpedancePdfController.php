<?php

namespace App\Http\Controllers;

use App\Models\BodyAssessment;
use App\Services\DompdfPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BioimpedancePdfController extends Controller
{
    public function __invoke(
        BodyAssessment $assessment, 
        DompdfPdfService $dompdfPdf,
        \App\Services\ReportValidationService $validationService
    ): Response
    {
        if ($assessment->user_id !== Auth::id() && !Auth::user()->hasRole(['professional', 'admin'])) {
            abort(403);
        }

        $user = $assessment->user;
        $profile = $user->profile;
        $motor = app(\App\Services\IntelligenceMotorService::class);
        $bioInsights = $motor->analyzeBioimpedance($assessment);

        // Gera registro de validação para o QR Code
        $reportRecord = $validationService->generateVersion($user, 'Bioimpedance Technical');
        $validationUrl = $validationService->getValidationUrl($reportRecord);

        // Gera QR Code
        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($validationUrl)
            ->size(150)
            ->margin(0)
            ->build();
        
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode->getString());

        $html = view('pdf.bioimpedance', [
            'assessment' => $assessment,
            'user' => $user,
            'profile' => $profile,
            'bioInsights' => $bioInsights,
            'qrCode' => $qrCodeBase64,
            'reportRecord' => $reportRecord,
            'emissionDate' => now()->format('d/m/Y H:i'),
        ])->render();

        try {
            $binary = $dompdfPdf->render($html, 'A4', 'portrait', true, 'DejaVu Sans');
        } catch (\Exception $e) {
            return response('Erro ao gerar PDF: ' . $e->getMessage(), 500);
        }

        $filename = 'laudo_tecnico_' . $user->name . '_' . $assessment->assessment_date->format('Y-m-d') . '.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
