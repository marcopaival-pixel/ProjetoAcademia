<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkoutImportLog;

class WorkoutImportBillingService
{
    public function __construct(
        private AiCreditService $credits,
    ) {}

    public function chargeOnSuccessfulExtraction(User $user, WorkoutImportLog $log, string $source): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        return $this->credits->consume(
            $user,
            'workout_import_photo',
            [
                'source' => $source,
                'log_id' => $log->id,
                'status' => $log->status,
            ],
            $this->referenceId($log),
        );
    }

    public function referenceId(WorkoutImportLog $log): string
    {
        return 'workout_import_log_'.$log->id;
    }
}
