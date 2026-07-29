<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalCertificate;
use App\Models\MedicalPrescription;
use App\Models\MedicalReport;
use App\Models\ProfessionalPatient;
use App\Services\DompdfPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $professionalId = $this->professionalId($request);

        return response()->json([
            'data' => [
                'reports' => $user->medicalReports()
                    ->where('professional_id', $professionalId)
                    ->latest('date')
                    ->get()
                    ->map(fn (MedicalReport $report) => $this->documentPayload($report))
                    ->values(),
                'prescriptions' => $user->medicalPrescriptions()
                    ->where('professional_id', $professionalId)
                    ->latest('date')
                    ->get()
                    ->map(fn (MedicalPrescription $prescription) => $this->documentPayload($prescription))
                    ->values(),
                'certificates' => $user->medicalCertificates()
                    ->where('professional_id', $professionalId)
                    ->latest('date')
                    ->get()
                    ->map(fn (MedicalCertificate $certificate) => $this->documentPayload($certificate))
                    ->values(),
            ],
        ]);
    }

    public function download(Request $request, string $type, int $id, DompdfPdfService $pdfService): StreamedResponse|JsonResponse
    {
        $user = auth()->user();
        $professionalId = $this->professionalId($request);
        $document = $this->resolveDocument($type, $user->id, $professionalId, $id);

        if (! $document) {
            return response()->json(['message' => 'Documento não encontrado ou sem permissão.'], 404);
        }

        if (! empty($document->pdf_path)) {
            return $this->downloadStoredPdf($document->pdf_path);
        }

        return $this->downloadGeneratedPdf($type, $user, $document, $pdfService);
    }

    private function professionalId(Request $request): int
    {
        $link = $this->activeLink($request);

        return (int) $link->professional_id;
    }

    private function activeLink(Request $request): ProfessionalPatient
    {
        $link = $request->attributes->get('active_patient_link');

        if (! $link instanceof ProfessionalPatient) {
            abort(400, 'Contexto de vínculo não informado.');
        }

        return $link;
    }

    private function documentPayload(MedicalReport|MedicalPrescription|MedicalCertificate $document): array
    {
        return [
            'id' => $document->id,
            'title' => $document->title ?? class_basename($document),
            'date' => optional($document->date ?? $document->created_at)->toDateString(),
            'description' => $document->description ?? null,
            'has_pdf' => ! empty($document->pdf_path),
        ];
    }

    private function resolveDocument(string $type, int $patientId, int $professionalId, int $id): MedicalReport|MedicalPrescription|MedicalCertificate|null
    {
        return match ($type) {
            'report' => MedicalReport::where('patient_id', $patientId)
                ->where('professional_id', $professionalId)
                ->find($id),
            'prescription' => MedicalPrescription::where('patient_id', $patientId)
                ->where('professional_id', $professionalId)
                ->find($id),
            'certificate' => MedicalCertificate::where('patient_id', $patientId)
                ->where('professional_id', $professionalId)
                ->find($id),
            default => null,
        };
    }

    private function downloadStoredPdf(string $pdfPath): StreamedResponse|JsonResponse
    {
        $disk = config('filesystems.default', 'local');
        if (! Storage::disk($disk)->exists($pdfPath)) {
            $diskHistorico = config('pdf.historico_disk', 'local');
            if (Storage::disk($diskHistorico)->exists($pdfPath)) {
                $disk = $diskHistorico;
            } else {
                return response()->json(['message' => 'Arquivo PDF físico não encontrado no servidor.'], 404);
            }
        }

        return Storage::disk($disk)->download($pdfPath, basename($pdfPath), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function downloadGeneratedPdf(
        string $type,
        $patient,
        MedicalReport|MedicalPrescription|MedicalCertificate $document,
        DompdfPdfService $pdfService
    ): StreamedResponse {
        $html = match ($type) {
            'report' => view('professional.medical-records.reports.pdf', [
                'patient' => $patient,
                'report' => $document,
            ])->render(),
            'prescription' => view('professional.medical-records.prescriptions.pdf', [
                'patient' => $patient,
                'prescription' => $document,
            ])->render(),
            'certificate' => view('professional.medical-records.certificates.pdf', [
                'patient' => $patient,
                'certificate' => $document,
            ])->render(),
            default => abort(400, 'Tipo de documento inválido.'),
        };

        $filename = match ($type) {
            'report' => "laudo-{$document->id}.pdf",
            'prescription' => "receita-{$document->id}.pdf",
            'certificate' => "atestado-{$document->id}.pdf",
            default => "documento-{$document->id}.pdf",
        };

        return $pdfService->generate($html, $filename);
    }
}
