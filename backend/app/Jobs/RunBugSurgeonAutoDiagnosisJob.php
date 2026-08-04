<?php

namespace App\Jobs;

use App\Models\BugIncident;
use App\Models\BugIncidentEvent;
use App\Services\BugIncidentService;
use App\Services\BugSurgeonAutoDiagnosisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunBugSurgeonAutoDiagnosisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 180;

    public function __construct(private int $incidentId) {}

    public function handle(BugSurgeonAutoDiagnosisService $diagnosis, BugIncidentService $incidents): void
    {
        $incident = BugIncident::with('systemError')->find($this->incidentId);
        if ($incident === null || $incident->systemError === null) {
            return;
        }

        if ($incident->ignored_at !== null) {
            return;
        }

        if ($incident->state !== BugIncident::STATE_INVESTIGATING) {
            return;
        }

        if (filled($incident->diagnosis) && filled($incident->proposed_diff)) {
            return;
        }

        try {
            $updated = $diagnosis->run($incident);
            $incidents->logEvent($updated, BugIncidentEvent::PATCH_PROPOSED, [
                'mode' => 'auto_diagnosis_complete',
                'state' => $updated->state,
            ]);
        } catch (Throwable $e) {
            Log::warning('BugSurgeon auto-diagnosis failed', [
                'incident_id' => $this->incidentId,
                'error' => $e->getMessage(),
            ]);

            $incidents->logEvent($incident, BugIncidentEvent::AI_ANALYSIS_STARTED, [
                'mode' => 'auto_diagnosis_failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
