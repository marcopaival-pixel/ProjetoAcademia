<?php

namespace App\Jobs;

use App\Models\AiExecutionLog;
use App\Models\EvolutionReport;
use App\Services\AI\EvolutionReportOrchestratorService;
use App\Services\AiCreditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateEvolutionReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(private int $reportId) {}

    public function handle(EvolutionReportOrchestratorService $orchestrator, AiCreditService $aiCredits): void
    {
        $report = EvolutionReport::with('user')->findOrFail($this->reportId);
        $startedAt = microtime(true);
        $referenceId = 'evolution_report_'.$report->id;

        $report->update([
            'status' => EvolutionReport::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        try {
            $finalReport = $orchestrator->generate($report->user);
            $status = $this->completedStatus($finalReport);

            $report->update([
                'status' => $status,
                'validation_result' => $finalReport['qualidade_fotos'] ?? null,
                'objective_metrics' => $finalReport['dados_confirmados'] ?? null,
                'comparison_result' => [
                    'claims' => $finalReport['claims_visuais'] ?? [],
                    'observacoes_visuais' => $finalReport['observacoes_visuais'] ?? [],
                    'limitacoes' => $finalReport['limitacoes'] ?? [],
                ],
                'audit_result' => $finalReport['auditoria'] ?? null,
                'final_report' => $finalReport,
                'confidence' => $finalReport['confianca_geral'] ?? null,
                'limited_reason' => $status === EvolutionReport::STATUS_COMPLETED_WITH_LIMITATIONS
                    ? implode(' ', array_slice($finalReport['limitacoes'] ?? [], 0, 3))
                    : null,
                'completed_at' => now(),
                'published_at' => now(),
            ]);

            $aiCredits->consume($report->user, 'evolution_ai_report', [
                'source' => 'async_evolution_report',
                'report_id' => $report->id,
            ], $referenceId);

            $this->logExecution($report, 'evolution_report_orchestrator', 'completed', $startedAt);
            $this->logPipelineSteps($report, $finalReport);
        } catch (Throwable $e) {
            $report->update([
                'status' => EvolutionReport::STATUS_FAILED,
                'failure_reason' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->logExecution($report, 'evolution_report_orchestrator', 'failed', $startedAt, $e);

            throw $e;
        }
    }

    private function completedStatus(array $finalReport): string
    {
        $status = (string) ($finalReport['status'] ?? '');

        if (str_contains($status, 'limitacoes') || str_contains($status, 'linha_base') || ($finalReport['observacoes_visuais'] ?? []) === []) {
            return EvolutionReport::STATUS_COMPLETED_WITH_LIMITATIONS;
        }

        return EvolutionReport::STATUS_COMPLETED;
    }

    private function logPipelineSteps(EvolutionReport $report, array $finalReport): void
    {
        $this->logExecution($report, 'image_validator', 'completed');
        $this->logExecution($report, 'objective_data', 'completed');
        $this->logExecution(
            $report,
            'visual_comparison',
            ($finalReport['claims_visuais'] ?? []) === [] ? 'skipped' : 'completed'
        );
        $this->logExecution(
            $report,
            'anti_hallucination_auditor',
            ($finalReport['observacoes_visuais'] ?? []) === [] ? 'completed_with_limitations' : 'completed'
        );
    }

    private function logExecution(EvolutionReport $report, string $agent, string $status, ?float $startedAt = null, ?Throwable $e = null): void
    {
        AiExecutionLog::create([
            'evolution_report_id' => $report->id,
            'agent' => $agent,
            'provider' => $report->provider,
            'prompt_version' => $report->prompt_version,
            'schema_version' => $report->schema_version,
            'request_hash' => hash('sha256', implode('|', [
                $report->user_id,
                optional($report->current_session_date)->toDateString(),
                optional($report->previous_session_date)->toDateString(),
                $report->prompt_version,
                $report->schema_version,
            ])),
            'duration_ms' => $startedAt !== null ? (int) round((microtime(true) - $startedAt) * 1000) : null,
            'attempt' => $this->attempts(),
            'status' => $status,
            'error_code' => $e ? class_basename($e) : null,
            'error' => $e?->getMessage(),
            'created_at' => now(),
        ]);
    }
}
