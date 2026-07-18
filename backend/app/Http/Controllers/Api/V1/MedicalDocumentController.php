<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MedicalCertificate;
use App\Models\MedicalPrescription;
use App\Models\MedicalReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalDocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'reports' => $user->medicalReports()->latest()->get()->map(fn (MedicalReport $report) => [
                    'id' => $report->id,
                    'title' => $report->title ?? 'Laudo medico',
                    'date' => optional($report->created_at)->toDateString(),
                    'description' => $report->description ?? null,
                    'professional_name' => $report->professional?->name,
                ])->values(),
                'prescriptions' => $user->medicalPrescriptions()->latest()->get()->map(fn (MedicalPrescription $prescription) => [
                    'id' => $prescription->id,
                    'title' => $prescription->title ?? 'Receita',
                    'date' => optional($prescription->created_at)->toDateString(),
                    'description' => $prescription->description ?? null,
                    'professional_name' => $prescription->professional?->name,
                ])->values(),
                'certificates' => $user->medicalCertificates()->latest()->get()->map(fn (MedicalCertificate $certificate) => [
                    'id' => $certificate->id,
                    'title' => $certificate->title ?? 'Atestado',
                    'date' => optional($certificate->created_at)->toDateString(),
                    'description' => $certificate->description ?? null,
                    'professional_name' => $certificate->professional?->name,
                ])->values(),
            ],
        ]);
    }

    /**
     * Faz o download do PDF armazenado do documento clínico.
     */
    public function download(Request $request, string $type, int $id): StreamedResponse|JsonResponse
    {
        $user = $request->user();
        $document = null;

        switch ($type) {
            case 'report':
                $document = MedicalReport::where('patient_id', $user->id)->find($id);
                break;
            case 'prescription':
                $document = MedicalPrescription::where('patient_id', $user->id)->find($id);
                break;
            case 'certificate':
                $document = MedicalCertificate::where('patient_id', $user->id)->find($id);
                break;
            default:
                return response()->json(['message' => 'Tipo de documento inválido.'], 400);
        }

        if (! $document) {
            return response()->json(['message' => 'Documento não encontrado ou sem permissão.'], 404);
        }

        if (empty($document->pdf_path)) {
            return response()->json(['message' => 'O PDF deste documento ainda não foi gerado ou anexado.'], 404);
        }

        $disk = config('filesystems.default', 'local');
        if (! Storage::disk($disk)->exists($document->pdf_path)) {
             // Pode ser que esteja no disco específico de pdfs
             $diskHistorico = config('pdf.historico_disk', 'local');
             if (Storage::disk($diskHistorico)->exists($document->pdf_path)) {
                 $disk = $diskHistorico;
             } else {
                 return response()->json(['message' => 'Arquivo PDF físico não encontrado no servidor.'], 404);
             }
        }

        $filename = basename($document->pdf_path);

        return Storage::disk($disk)->download($document->pdf_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
