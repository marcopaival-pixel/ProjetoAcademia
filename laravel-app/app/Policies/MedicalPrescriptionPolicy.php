<?php

namespace App\Policies;

use App\Models\MedicalPrescription;
use App\Models\User;
use App\Support\PatientAccessGuard;

class MedicalPrescriptionPolicy
{
    public function view(User $user, MedicalPrescription $prescription): bool
    {
        if ($user->isAdministrator()) {
            $patient = User::find($prescription->patient_id);

            return $patient !== null && PatientAccessGuard::patientBelongsToImpersonatedTenant($patient);
        }

        if ((int) $prescription->patient_id === (int) $user->id) {
            return true;
        }

        if ($user->isProfessional()) {
            return $user->patients()->wherePivot('user_id', $prescription->patient_id)->exists();
        }

        return false;
    }
}
