<?php



namespace App\Policies;



use App\Models\User;

use App\Support\PatientAccessGuard;



class ProfessionalPatientPolicy

{

    public function view(User $professional, User $patient): bool

    {

        if ($professional->isAdministrator()) {

            return PatientAccessGuard::patientBelongsToImpersonatedTenant($patient);

        }



        if (! $professional->isProfessional() && ! $professional->hasRole(['receptionist', 'manager'])) {

            return false;

        }



        return $professional->patients()->wherePivot('user_id', $patient->id)->exists();

    }



    public function update(User $professional, User $patient): bool

    {

        return $this->view($professional, $patient);

    }



    public function delete(User $professional, User $patient): bool

    {

        return $this->view($professional, $patient);

    }

}


