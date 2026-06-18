<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Support\PatientAccessGuard;
use App\Support\TenantContext;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator()
            || $user->hasRole(['professional', 'receptionist', 'manager']);
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isAdministrator()) {
            if ($patient->user_id) {
                $patientUser = User::find($patient->user_id);

                return $patientUser !== null && PatientAccessGuard::patientBelongsToImpersonatedTenant($patientUser);
            }

            return session()->has('impersonated_clinic_id');
        }

        $companyId = TenantContext::getCompanyId() ?? $user->academy_company_id;
        $clinicId = TenantContext::get() ?? $user->clinic_id;

        if ($patient->academy_company_id && $companyId) {
            return (int) $patient->academy_company_id === (int) $companyId;
        }

        if ($patient->clinic_id && $clinicId) {
            return (int) $patient->clinic_id === (int) $clinicId;
        }

        return (int) $patient->user_id === (int) $user->id
            || (int) ($patient->professional_id ?? 0) === (int) $user->id;
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->view($user, $patient)
            && ($user->isAdministrator() || $user->hasRole(['professional', 'receptionist', 'manager']));
    }
}
