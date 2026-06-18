<?php

namespace App\Policies;

use App\Models\BodyAssessment;
use App\Models\User;
use App\Support\PatientAccessGuard;

class BodyAssessmentPolicy
{
    public function view(User $user, BodyAssessment $assessment): bool
    {
        if ($user->isAdministrator()) {
            $student = User::find($assessment->user_id);

            return $student !== null && PatientAccessGuard::patientBelongsToImpersonatedTenant($student);
        }

        if ((int) $assessment->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isProfessional()) {
            return $user->patients()->wherePivot('user_id', $assessment->user_id)->exists();
        }

        return false;
    }

    public function delete(User $user, BodyAssessment $assessment): bool
    {
        return $this->view($user, $assessment);
    }
}
