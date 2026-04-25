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
        // Verifica vínculo profissional
        if (!auth()->user()->patients()->wherePivot('user_id', $patient->id)->exists()) {
            abort(403, 'Acesso não autorizado a este paciente.');
        }

        $patient->load(['profile', 'weightEntries', 'assessments']);
        
        $clinicalData = [
            'goal' => $patient->profile->goal ?? 'Não definido',
            'sex' => $patient->profile->sex === 'M' ? 'Masculino' : 'Feminino',
            'height' => $patient->profile->height_cm ? $patient->profile->height_cm . ' cm' : 'N/A',
            'last_weight' => $patient->weightEntries()->latest()->first()?->weight_kg ? $patient->weightEntries()->latest()->first()?->weight_kg . ' kg' : 'N/A',
        ];

        // Histórico simplificado
        $history = $patient->weightEntries()->latest()->limit(5)->get();

        // QR Code (Usando API do Google Charts para simplicidade no ambiente XAMPP)
        $qrCodeData = urlencode(route('access', ['token' => 'verification-' . $patient->id])); // Placeholder or real token
        $qrCodeUrl = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={$qrCodeData}&choe=UTF-8";

        $html = view('pdf.patient-report', [
            'patient' => $patient,
            'clinicalData' => $clinicalData,
            'history' => $history,
            'qrCodeUrl' => $qrCodeUrl,
            'professional' => auth()->user(),
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
