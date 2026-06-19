<?php

namespace App\Http\Controllers;

use App\Models\BodyAssessment;
use App\Services\DompdfPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BioimpedancePdfController extends Controller
{
    public function __invoke(
        BodyAssessment $assessment,
        DompdfPdfService $dompdfPdf,
        \App\Services\ReportValidationService $validationService
    ): Response {
        $user = Auth::user();

        if (! $this->canAccessAssessment($user, $assessment)) {
            abort(403);
        }

        $patient = $assessment->user;
        $profile = $patient->profile;
        $motor = app(\App\Services\IntelligenceMotorService::class);
        $bioInsights = $motor->analyzeBioimpedance($assessment);

        $reportRecord = $validationService->generateVersion($patient, 'Bioimpedance Technical');
        $validationUrl = $validationService->getValidationUrl($reportRecord);

        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($validationUrl)
            ->size(150)
            ->margin(0)
            ->build();

        $qrCodeBase64 = 'data:image/png;base64,'.base64_encode($qrCode->getString());

        $html = view('pdf.bioimpedance', [
            'assessment' => $assessment,
            'user' => $patient,
            'profile' => $profile,
            'bioInsights' => $bioInsights,
            'qrCode' => $qrCodeBase64,
            'reportRecord' => $reportRecord,
            'emissionDate' => now()->format('d/m/Y H:i'),
        ])->render();

        try {
            $binary = $dompdfPdf->render($html, 'A4', 'portrait', true, 'DejaVu Sans');
        } catch (\Exception $e) {
            return response('Erro ao gerar PDF: '.$e->getMessage(), 500);
        }

        $filename = 'laudo_tecnico_'.$patient->name.'_'.$assessment->assessment_date->format('Y-m-d').'.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function canAccessAssessment(\App\Models\User $user, BodyAssessment $assessment): bool
    {
        if ((int) $assessment->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isAdministrator()) {
            $subject = $assessment->user ?? \App\Models\User::find($assessment->user_id);

            return $subject && \App\Support\PatientAccessGuard::patientBelongsToImpersonatedTenant($subject);
        }

        if ($user->isProfessional()) {
            return $user->patients()->where('users.id', $assessment->user_id)->exists();
        }

        return false;
    }
}
