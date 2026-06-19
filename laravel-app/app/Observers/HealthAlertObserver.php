<?php

namespace App\Observers;

use App\Models\HealthAlert;
use App\Services\Notifications\FcmPushService;

class HealthAlertObserver
{
    public function __construct(private FcmPushService $pushService) {}

    public function created(HealthAlert $alert): void
    {
        if ($alert->is_read) {
            return;
        }

        $patient = $alert->user;
        if ($patient === null) {
            return;
        }

        $professionals = $patient->professionals()
            ->wherePivot('status', 'Sim')
            ->get();

        foreach ($professionals as $professional) {
            $this->pushService->sendToUser(
                $professional,
                'Alerta de saúde',
                $alert->message,
                [
                    'type' => 'health_alert',
                    'alert_id' => (string) $alert->id,
                    'patient_id' => (string) $alert->user_id,
                    'severity' => (string) $alert->severity,
                ]
            );
        }
    }
}
