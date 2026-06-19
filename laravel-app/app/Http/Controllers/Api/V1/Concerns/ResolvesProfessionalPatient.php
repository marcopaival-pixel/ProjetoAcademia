<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

trait ResolvesProfessionalPatient
{
    protected function linkedPatient(Request $request, int $patientId): User
    {
        $patient = $request->user()
            ->patients()
            ->where('users.id', $patientId)
            ->wherePivot('status', 'Sim')
            ->first();

        if ($patient === null) {
            throw new AuthorizationException('Sem vínculo com este aluno.');
        }

        return $patient;
    }
}
