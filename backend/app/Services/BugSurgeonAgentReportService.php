<?php

namespace App\Services;

use App\Models\BugIncident;
use App\Models\BugIncidentEvent;
use InvalidArgumentException;
use RuntimeException;

class BugSurgeonAgentReportService
{
    public function __construct(
        private readonly BugIncidentService $incidents,
    ) {}

    /** @param  array<string, mixed>  $data */
    public function applyReport(BugIncident $incident, array $data): BugIncident
    {
        $this->assertCanReceiveReport($incident);

        $confidence = isset($data['confidence']) ? (int) $data['confidence'] : null;
        if ($confidence !== null && $confidence < 80 && empty($data['force_low_confidence'])) {
            throw new RuntimeException(
                'Confiança abaixo de 80%. Use force_low_confidence=true para registrar hipótese não confirmada.'
            );
        }

        $payload = [
            'error_summary' => $data['problem_summary'] ?? $data['diagnosis'] ?? $incident->error_summary,
            'diagnosis' => $data['diagnosis'] ?? $incident->diagnosis,
            'root_cause' => $data['root_cause'] ?? $incident->root_cause,
            'module' => $data['module'] ?? $incident->module,
            'file_path' => $data['file_path'] ?? $incident->file_path,
            'line_start' => $data['line_start'] ?? $incident->line_start,
            'line_end' => $data['line_end'] ?? $incident->line_end,
            'code_snippet' => $data['code_snippet'] ?? $incident->code_snippet,
            'proposed_diff' => $data['proposed_diff'] ?? $incident->proposed_diff,
            'confidence' => $confidence ?? $incident->confidence,
            'error_reproduced' => array_key_exists('error_reproduced', $data)
                ? (bool) $data['error_reproduced']
                : $incident->error_reproduced,
            'risk_level' => $data['risk_level'] ?? $incident->risk_level,
            'evidence' => $data['evidence'] ?? $incident->evidence,
            'impact_analysis' => $data['impact_analysis'] ?? $incident->impact_analysis,
            'affected_role' => $data['affected_role'] ?? $incident->affected_role,
        ];

        if (isset($data['method_name'])) {
            $incident->method_name = $data['method_name'];
        }

        if (isset($data['checklist']) && is_array($data['checklist'])) {
            $incident->analysis_checklist = $data['checklist'];
        }

        $incident = $this->incidents->updateAnalysis($incident, $payload);

        $agentMeta = $incident->agent_context ?? [];
        $agentMeta['last_agent_report_at'] = now()->toIso8601String();
        $agentMeta['report_confidence'] = $incident->confidence;
        $incident->agent_context = $agentMeta;
        $incident->save();

        if (filled($incident->root_cause) || filled($incident->diagnosis)) {
            $this->incidents->logEvent($incident, BugIncidentEvent::ROOT_CAUSE_IDENTIFIED, [
                'confidence' => $incident->confidence,
                'file' => $incident->file_path,
            ]);
            $incident = $this->advanceIfNeeded($incident, BugIncident::STATE_DIAGNOSIS_READY);
        }

        if (filled($incident->impact_analysis)) {
            $incident = $this->advanceIfNeeded($incident, BugIncident::STATE_IMPACT_ANALYZED);
        }

        if (filled($incident->proposed_diff) && filled($incident->file_path)) {
            $this->incidents->logEvent($incident, BugIncidentEvent::PATCH_PROPOSED, [
                'file' => $incident->file_path,
            ]);
            $incident = $this->advanceIfNeeded($incident, BugIncident::STATE_PATCH_PROPOSED);
        }

        if (! empty($data['submit_for_approval'])) {
            $incident = $this->incidents->advanceToWaitingApproval($incident->fresh());
            $this->incidents->logEvent($incident, BugIncidentEvent::PATCH_PROPOSED, [
                'submitted_for_approval' => true,
            ]);
        }

        return $incident->fresh();
    }

    private function assertCanReceiveReport(BugIncident $incident): void
    {
        $allowed = [
            BugIncident::STATE_INVESTIGATING,
            BugIncident::STATE_DIAGNOSIS_READY,
            BugIncident::STATE_IMPACT_ANALYZED,
            BugIncident::STATE_PATCH_PROPOSED,
        ];

        if (! in_array($incident->state, $allowed, true)) {
            throw new RuntimeException(
                "Incidente em estado {$incident->state} não aceita relatório do agente."
            );
        }

        if ($incident->ignored_at !== null) {
            throw new RuntimeException('Incidente ignorado.');
        }
    }

    private function advanceIfNeeded(BugIncident $incident, string $targetState): BugIncident
    {
        $currentIndex = array_search($incident->state, BugIncident::ORDERED_STATES, true);
        $targetIndex = array_search($targetState, BugIncident::ORDERED_STATES, true);

        if ($currentIndex === false || $targetIndex === false || $currentIndex >= $targetIndex) {
            return $incident;
        }

        try {
            return $this->incidents->transition($incident, $targetState);
        } catch (InvalidArgumentException) {
            while ($currentIndex < $targetIndex) {
                $next = BugIncident::ORDERED_STATES[$currentIndex + 1];
                try {
                    $incident = $this->incidents->transition($incident, $next);
                    $currentIndex++;
                } catch (InvalidArgumentException) {
                    break;
                }
            }

            return $incident;
        }
    }
}
