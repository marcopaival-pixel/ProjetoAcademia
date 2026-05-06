<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DompdfPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class PatientReportController extends Controller
{
    public function export(User $patient, DompdfPdfService $dompdfPdf): Response
    {
        $loggedUser = auth()->user();
        
        // Verifica se é o próprio paciente ou se existe vínculo profissional
        $isOwnReport = (string)$loggedUser->id == (string)$patient->id;
        
        $isProfessionalWithLink = false;
        if ($loggedUser->hasRole('professional')) {
            $isProfessionalWithLink = $loggedUser->patients()->wherePivot('user_id', $patient->id)->exists();
        }

        if (!$isOwnReport && !$isProfessionalWithLink) {
            abort(403, 'Acesso não autorizado a este paciente.');
        }

        // Se o paciente está baixando o próprio laudo, buscamos o profissional ativo na sessão
        $professional = $isOwnReport 
            ? User::find(session('active_professional_id')) 
            : $loggedUser;

        // Se ainda não tiver profissional (ex: sem sessão ativa), pega o primeiro vínculo
        if (!$professional && $patient->professionals()->exists()) {
            $professional = $patient->professionals()->first();
        }

        $patient->load(['profile', 'weightEntries', 'assessments']);
        
        $clinicalData = [
            'goal' => $patient->profile->goal ?? 'Não definido',
            'sex' => ($patient->profile->sex ?? 'M') === 'M' ? 'Masculino' : 'Feminino',
            'height' => ($patient->profile->height_cm ?? 0) ? $patient->profile->height_cm . ' cm' : 'N/A',
            'last_weight' => $patient->weightEntries()->latest()->first()?->weight_kg ? $patient->weightEntries()->latest()->first()?->weight_kg . ' kg' : 'N/A',
        ];

        // Histórico simplificado
        $history = $patient->weightEntries()->latest()->limit(5)->get();

        // QR Code
        $qrCodeData = urlencode(route('access', ['token' => 'verification-' . $patient->id]));
        $qrCodeUrl = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={$qrCodeData}&choe=UTF-8";

        $html = view('pdf.patient-report', [
            'patient' => $patient,
            'clinicalData' => $clinicalData,
            'history' => $history,
            'qrCodeUrl' => $qrCodeUrl,
            'professional' => $professional ?? $loggedUser,
            'emissionDate' => now()->format('d/m/Y H:i'),
        ])->render();

        $binary = $dompdfPdf->render($html, 'A4', 'portrait', false, 'DejaVu Sans');

        $slug = 'laudo_paciente_' . \Illuminate\Support\Str::slug($patient->name) . '_' . now()->format('Ymd') . '.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $slug . '"',
        ]);
    }
}
