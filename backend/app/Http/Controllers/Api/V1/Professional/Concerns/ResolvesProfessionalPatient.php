<?php

namespace App\Http\Controllers\Api\V1\Professional\Concerns;

use App\Models\User;
use Illuminate\Http\Request;

trait ResolvesProfessionalPatient
{
    protected function assertProfessionalPatient(Request $request, int $patientId): User
    {
        $professional = $request->user();

        if ($professional->isAdministrator()) {
            return User::findOrFail($patientId);
        }

        $linked = $professional->patients()
            ->where('users.id', $patientId)
            ->where('pacientes.status', 'Sim')
            ->exists();

        if (! $linked) {
            abort(403, 'Paciente não vinculado a este profissional.');
        }

        return User::findOrFail($patientId);
    }
}
