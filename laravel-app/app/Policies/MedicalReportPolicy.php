<?php

namespace App\Policies;

use App\Models\MedicalReport;
use App\Models\User;
use App\Support\PatientAccessGuard;

class MedicalReportPolicy
{
    public function view(User $user, MedicalReport $report): bool
    {
        if ($user->isAdministrator()) {
            $patient = User::find($report->patient_id);

            return $patient !== null && PatientAccessGuard::patientBelongsToImpersonatedTenant($patient);
        }

        if ((int) $user->id === (int) $report->patient_id) {
            return true;
        }

        if ($user->isProfessional()) {
            return $user->patients()->where('users.id', $report->patient_id)->exists();
        }

        return false;
    }
}
