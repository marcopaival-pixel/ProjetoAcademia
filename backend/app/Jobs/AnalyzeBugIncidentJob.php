<?php

namespace App\Jobs;

use App\Models\BugIncident;
use App\Models\BugIncidentEvent;
use App\Services\BugAgentContextService;
use App\Services\BugIncidentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Enriquece contexto do incidente (context pack + git) em background.
 */
class AnalyzeBugIncidentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(private int $incidentId) {}

    public function handle(BugAgentContextService $agentContext, BugIncidentService $incidents): void
    {
        $incident = BugIncident::with('systemError')->find($this->incidentId);
        if ($incident === null || $incident->systemError === null) {
            return;
        }

        if ($incident->ignored_at !== null) {
            return;
        }

        $context = $agentContext->build($incident->systemError, $incident);
        $checklist = $agentContext->syncChecklistFromPack(
            $incident->analysis_checklist ?? $agentContext->defaultChecklist(),
            $context['context_pack'] ?? []
        );

        $incident->agent_context = $context;
        $incident->analysis_checklist = $checklist;
        $incident->save();

        $incidents->logEvent($incident, BugIncidentEvent::AI_ANALYSIS_STARTED, [
            'mode' => 'context_pack_ready',
            'files_in_pack' => $context['context_pack']['stats']['files_in_pack'] ?? null,
        ]);

        if (config('services.bug_surgeon.auto_diagnosis', true)) {
            RunBugSurgeonAutoDiagnosisJob::dispatch($incident->id)->onQueue(
                config('services.bug_surgeon.queue', 'default')
            );
        }
    }
}
