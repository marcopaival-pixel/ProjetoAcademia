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

        $patientCompany = $patient->getAttribute('academy_company_id');
        $patientClinic = $patient->getAttribute('clinic_id');
        $patientUserId = $patient->getAttribute('user_id');
        $patientProfessionalId = $patient->getAttribute('professional_id') ?? 0;

        if ($patientCompany && $companyId) {
            return (int) $patientCompany === (int) $companyId;
        }

        if ($patientClinic && $clinicId) {
            return (int) $patientClinic === (int) $clinicId;
        }

        return (int) $patientUserId === (int) $user->id
            || (int) $patientProfessionalId === (int) $user->id;
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->view($user, $patient)
            && ($user->isAdministrator() || $user->hasRole(['professional', 'receptionist', 'manager']));
    }
}
