<?php

namespace App\Http\Controllers;

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

        return $this->secureFiles->streamForOwner($user, $photo->photo_path, (int) $photo->user_id);
    }

    private function serveBodyAnalysis($user, int $id)
    {
        $analysis = BodyAnalysis::findOrFail($id);

        return $this->secureFiles->streamForOwner($user, $analysis->photo_path, (int) $analysis->user_id);
    }

    private function serveGallery($user, int $id)
    {
        $photo = Photo::findOrFail($id);
        PatientAccessGuard::assertStudentDataAccess($user, (int) $photo->student_id);

        return $this->secureFiles->streamPath($photo->file_path);
    }

    private function servePatientDocument($user, int $id)
    {
        $document = PatientDocument::findOrFail($id);

        if ((int) $document->patient_id === (int) $user->id) {
            return $this->secureFiles->streamPath($document->file_path);
        }

        if ($user->isProfessional() || $user->hasRole(['instructor', 'supervisor'])) {
            PatientAccessGuard::assertProfessionalPatientLink($user, (int) $document->patient_id);

            return $this->secureFiles->streamPath($document->file_path);
        }

        if ($user->isAdministrator()) {
            $owner = $document->patient ?? \App\Models\User::find($document->patient_id);
            if ($owner && PatientAccessGuard::patientBelongsToImpersonatedTenant($owner)) {
                return $this->secureFiles->streamPath($document->file_path);
            }
        }

        abort(403, 'Acesso não autorizado a este documento.');
    }
}
