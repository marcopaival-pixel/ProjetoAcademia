<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Unifica a jornada aluno/paciente: utilizadores com role aluno recebem paciente
 * para aceder ao portal e APIs mobile sem intervenção manual.
 */
class StudentRoleBridgeService
{
    public function ensurePortalAccess(User $user): bool
    {
        if (! $user->hasRole('aluno')) {
            return false;
        }

        if ($user->hasRole('paciente')) {
            return false;
        }

        try {
            $user->assignRole('paciente');

            return true;
        } catch (\Throwable $e) {
            Log::warning('StudentRoleBridge: falha ao atribuir role paciente', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
