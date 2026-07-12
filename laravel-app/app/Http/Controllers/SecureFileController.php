<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\BodyAnalysis;
use App\Models\EvolutionPhoto;
use App\Models\PatientDocument;
use App\Models\Photo;
use App\Services\SecureFileService;
use App\Support\PatientAccessGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecureFileController extends Controller
{
    public function __construct(
        private SecureFileService $secureFiles
    ) {}

    public function show(Request $request, string $type, int $id)
    {
        $user = Auth::user();

        return match ($type) {
            'evolution' => $this->serveEvolution($user, $id),
            'body-analysis' => $this->serveBodyAnalysis($user, $id),
            'gallery' => $this->serveGallery($user, $id),
            'patient-document' => $this->servePatientDocument($user, $id),
            default => abort(404, 'Tipo de arquivo não suportado.'),
        };
    }

    private function serveEvolution($user, int $id)
    {
        $photo = EvolutionPhoto::findOrFail($id);
        $response = $this->secureFiles->streamEvolutionPhoto($user, $photo);

        $this->logSecureFileAccess($user, 'evolution', $photo->id, (int) $photo->user_id, [
            'path' => $photo->photo_path,
        ]);

        return $response;
    }

    private function serveBodyAnalysis($user, int $id)
    {
        $analysis = BodyAnalysis::findOrFail($id);
        $response = $this->secureFiles->streamForOwner($user, $analysis->photo_path, (int) $analysis->user_id);

        $this->logSecureFileAccess($user, 'body-analysis', $analysis->id, (int) $analysis->user_id, [
            'path' => $analysis->photo_path,
        ]);

        return $response;
    }

    private function serveGallery($user, int $id)
    {
        $photo = Photo::findOrFail($id);
        PatientAccessGuard::assertStudentDataAccess($user, (int) $photo->student_id);
        $response = $this->secureFiles->streamPath($photo->file_path);

        $this->logSecureFileAccess($user, 'gallery', $photo->id, (int) $photo->student_id, [
            'path' => $photo->file_path,
        ]);

        return $response;
    }

    private function servePatientDocument($user, int $id)
    {
        $document = PatientDocument::findOrFail($id);

        if ((int) $document->patient_id === (int) $user->id) {
            return $this->streamPatientDocument($user, $document);
        }

        if ($user->isProfessional() || $user->hasRole(['instructor', 'supervisor'])) {
            PatientAccessGuard::assertProfessionalPatientLink($user, (int) $document->patient_id);

            return $this->streamPatientDocument($user, $document);
        }

        if ($user->isAdministrator()) {
            $owner = $document->patient ?? \App\Models\User::find($document->patient_id);
            if ($owner && PatientAccessGuard::patientBelongsToImpersonatedTenant($owner)) {
                return $this->streamPatientDocument($user, $document);
            }
        }

        abort(403, 'Acesso não autorizado a este documento.');
    }

    private function streamPatientDocument($user, PatientDocument $document)
    {
        $response = $this->secureFiles->streamPath($document->file_path);

        $this->logSecureFileAccess($user, 'patient-document', $document->id, (int) $document->patient_id, [
            'path' => $document->file_path,
            'document_type' => $document->type ?? null,
        ]);

        return $response;
    }

    private function logSecureFileAccess($user, string $type, int $recordId, int $patientId, array $extra = []): void
    {
        AdminLog::create([
            'user_id' => $user?->id,
            'action' => 'ACCESS_SECURE_FILE',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => array_merge([
                'type' => $type,
                'record_id' => $recordId,
                'patient_id' => $patientId,
                'timestamp' => now()->toDateTimeString(),
            ], $extra),
            'created_at' => now(),
        ]);
    }
}
