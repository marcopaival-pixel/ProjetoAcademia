<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\MedicalReport;
use App\Models\MedicalPrescription;
use App\Models\MedicalCertificate;
use App\Models\AdminLog;
use App\Services\DompdfPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentMedicalDocumentController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $activeProfId = session('active_professional_id') ?: $request->attributes->get('active_professional_id');
        $activeClinicId = session('active_clinic_id') ?: $request->attributes->get('active_clinic_id');

        $reportsQuery = $user->medicalReports()->with('professional')->latest('date');
        $prescriptionsQuery = $user->medicalPrescriptions()->with('professional')->latest('date');
        $certificatesQuery = $user->medicalCertificates()->with('professional')->latest('date');

        if ($activeProfId) {
            $reportsQuery->where('professional_id', $activeProfId);
            $prescriptionsQuery->where('professional_id', $activeProfId);
            $certificatesQuery->where('professional_id', $activeProfId);
        } elseif ($activeClinicId) {
            $reportsQuery->where('academy_company_id', $activeClinicId);
            $prescriptionsQuery->where('academy_company_id', $activeClinicId);
            $certificatesQuery->where('academy_company_id', $activeClinicId);
        } elseif ($request->header('X-Active-Context-ID') === 'personal' || session('active_personal_context')) {
            $reportsQuery->whereNull('professional_id');
            $prescriptionsQuery->whereNull('professional_id');
            $certificatesQuery->whereNull('professional_id');
        }

        $reports = $reportsQuery->get()
            ->map(fn (MedicalReport $report) => [
                'id' => $report->id,
                'title' => $report->title ?? 'Laudo Clínico',
                'date' => $report->date ? $report->date->toDateString() : null,
                'description' => $report->description ?? $report->conclusion ?? '',
                'professional_name' => $report->professional?->name ?? 'Profissional',
            ]);

        $prescriptions = $prescriptionsQuery->get()
            ->map(fn (MedicalPrescription $prescription) => [
                'id' => $prescription->id,
                'title' => $prescription->objective ?? 'Receita Médica',
                'date' => $prescription->date ? $prescription->date->toDateString() : null,
                'description' => $prescription->medicine ?? $prescription->observations ?? '',
                'professional_name' => $prescription->professional?->name ?? 'Profissional',
            ]);

        $certificates = $certificatesQuery->get()
            ->map(fn (MedicalCertificate $certificate) => [
                'id' => $certificate->id,
                'title' => $certificate->reason ?? 'Atestado Médico',
                'date' => $certificate->date ? $certificate->date->toDateString() : null,
                'description' => $certificate->observations ?? '',
                'professional_name' => $certificate->professional?->name ?? 'Profissional',
            ]);

        return $this->success([
            'reports' => $reports,
            'prescriptions' => $prescriptions,
            'certificates' => $certificates,
        ]);
    }

    public function downloadReport(Request $request, MedicalReport $report, DompdfPdfService $pdfService): Response
    {
        $this->authorize('view', $report);
        $patient = $request->user();

        AdminLog::create([
            'user_id' => $patient->id,
            'action' => 'PATIENT_DOWNLOAD_MEDICAL_REPORT',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $patient->id,
                'document_type' => 'medical_report',
                'document_id' => $report->id,
                'professional_id' => $report->professional_id,
                'timestamp' => now()->toDateTimeString(),
            ],
            'created_at' => now(),
        ]);

        $html = view('professional.medical-records.reports.pdf', compact('patient', 'report'))->render();
        $binary = $pdfService->render($html);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laudo-' . $report->id . '.pdf"',
        ]);
    }

    public function downloadPrescription(Request $request, MedicalPrescription $prescription, DompdfPdfService $pdfService): Response
    {
        $this->authorize('view', $prescription);
        $patient = $request->user();

        AdminLog::create([
            'user_id' => $patient->id,
            'action' => 'PATIENT_DOWNLOAD_MEDICAL_PRESCRIPTION',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $patient->id,
                'document_type' => 'medical_prescription',
                'document_id' => $prescription->id,
                'professional_id' => $prescription->professional_id,
                'timestamp' => now()->toDateTimeString(),
            ],
            'created_at' => now(),
        ]);

        $html = view('professional.medical-records.prescriptions.pdf', compact('patient', 'prescription'))->render();
        $binary = $pdfService->render($html);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="receita-' . $prescription->id . '.pdf"',
        ]);
    }

    public function downloadCertificate(Request $request, MedicalCertificate $certificate, DompdfPdfService $pdfService): Response
    {
        $this->authorize('view', $certificate);
        $patient = $request->user();

        AdminLog::create([
            'user_id' => $patient->id,
            'action' => 'PATIENT_DOWNLOAD_MEDICAL_CERTIFICATE',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $patient->id,
                'document_type' => 'medical_certificate',
                'document_id' => $certificate->id,
                'professional_id' => $certificate->professional_id,
                'timestamp' => now()->toDateTimeString(),
            ],
            'created_at' => now(),
        ]);

        $html = view('professional.medical-records.certificates.pdf', compact('patient', 'certificate'))->render();
        $binary = $pdfService->render($html);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="atestado-' . $certificate->id . '.pdf"',
        ]);
    }
}
