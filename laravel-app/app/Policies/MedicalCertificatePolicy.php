<?php

namespace App\Policies;

use App\Models\MedicalCertificate;
use App\Models\User;
use App\Support\PatientAccessGuard;

class MedicalCertificatePolicy
{
    public function view(User $user, MedicalCertificate $certificate): bool
    {
        if ($user->isAdministrator()) {
            $patient = User::find($certificate->patient_id);

            return $patient !== null && PatientAccessGuard::patientBelongsToImpersonatedTenant($patient);
        }

        if ((int) $user->id === (int) $certificate->patient_id) {
            return true;
        }

        if ($user->isProfessional()) {
            return $user->patients()->where('users.id', $certificate->patient_id)->exists();
        }

        return false;
    }

    public function update(User $user, MedicalCertificate $certificate): bool
    {
        return $user->isProfessional()
            && (int) $certificate->professional_id === (int) $user->id
            && $this->view($user, $certificate);
    }

    public function delete(User $user, MedicalCertificate $certificate): bool
    {
        return $this->update($user, $certificate);
    }
}
