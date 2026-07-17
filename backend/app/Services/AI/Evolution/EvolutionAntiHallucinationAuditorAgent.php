<?php

namespace App\Services\AI\Evolution;

use App\Models\User;

class EvolutionAntiHallucinationAuditorAgent
{
    public function __construct(
        private EvolutionSchemaValidator $schemaValidator,
        private OpenAIEvolutionProvider $openAIProvider,
    ) {}

    private const FORBIDDEN_PATTERNS = [
        '/ganhou\s+\d+([\.,]\d+)?\s*kg\s+de\s+massa\s+muscular/i',
        '/perdeu\s+\d+([\.,]\d+)?\s*%\s+de\s+gordura/i',
        '/diagn[oÃ³]stico/i',
        '/horm[oÃ´]nio|anabolizante|medicamento/i',
        '/percentual\s+(exato\s+)?de\s+gordura/i',
        '/gordura\s+corporal\s+estimada/i',
        '/ganho\s+muscular\s+confirmado/i',
        '/postura\s+(errada|inadequada)/i',
        '/reten[cÃ§][aÃ£]o|inflama[cÃ§][aÃ£]o|obesidade|sobrepeso/i',
    ];

    public function audit(array $report, ?User $user = null): array
    {
        $report['auditoria']['claims'] = [];

        foreach ($report['observacoes_visuais'] ?? [] as $index => $item) {
            $claimId = $item['id'] ?? 'visual_'.$index;

            if (empty($item['fonte']) || empty($item['registro_atual']) || empty($item['evidencia']) || ! isset($item['confianca'])) {
                $report = $this->rejectReport($report, "Observacao visual #{$index} sem fonte, evidencia, registro ou confianca.");
                $report['auditoria']['claims'][] = $this->claimDecision($claimId, 'reject', 'Claim visual sem evidencia minima.');
                unset($report['observacoes_visuais'][$index]);
                continue;
            }

            if ((float) ($item['confianca'] ?? 0) < config('ai_evolution.thresholds.claim_confidence_min', 0.70)) {
                $report['limitacoes'][] = 'Uma observacao visual foi removida por baixa confianca.';
                $report['auditoria']['claims'][] = $this->claimDecision($claimId, 'reject', 'Confianca abaixo do limite de publicacao.');
                unset($report['observacoes_visuais'][$index]);
                continue;
            }

            if ($this->containsForbiddenPattern((string) ($item['texto'] ?? ''))) {
                $report['limitacoes'][] = 'Uma observacao visual foi removida por nao possuir sustentacao segura.';
                $report['auditoria']['falhas'][] = 'Frase visual removida por padrao proibido.';
                $report['auditoria']['claims'][] = $this->claimDecision($claimId, 'reject', 'Linguagem proibida para relatorio corporal.');
                unset($report['observacoes_visuais'][$index]);
                continue;
            }

            $report['auditoria']['claims'][] = $this->claimDecision($claimId, 'approve', 'Observacao possui fonte, evidencia e linguagem conservadora.');
        }

        if ($user && config('ai_evolution.provider') === 'openai' && config('services.openai.api_key')) {
            try {
                $externalAudit = $this->openAIProvider->audit($user, [
                    'claims' => $report['claims_visuais'] ?? [],
                    'observacoes_visuais' => $report['observacoes_visuais'] ?? [],
                    'dados_confirmados' => $report['dados_confirmados'] ?? [],
                    'qualidade_fotos' => $report['qualidade_fotos'] ?? [],
                    'rules' => [
                        'approve_rewrite_or_reject_only',
                        'prefer_conservative_answer_when_uncertain',
                        'reject_medical_or_body_fat_language',
                    ],
                ]);

                $report['auditoria']['claims'] = $externalAudit['claims'];
                $report['auditoria_aprovada'] = (bool) $externalAudit['approved'];
            } catch (\Throwable) {
                $report['auditoria']['falhas'][] = 'Auditoria externa indisponivel; auditoria deterministica aplicada.';
            }
        }

        $report['observacoes_visuais'] = array_values($report['observacoes_visuais'] ?? []);
        $this->schemaValidator->validateAudit([
            'approved' => (bool) ($report['auditoria_aprovada'] ?? false),
            'claims' => $report['auditoria']['claims'] ?? [],
        ]);

        return $report;
    }

    private function containsForbiddenPattern(string $text): bool
    {
        foreach (self::FORBIDDEN_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function claimDecision(string $claimId, string $decision, string $reason): array
    {
        return [
            'claim_id' => $claimId,
            'decision' => $decision,
            'reason' => $reason,
        ];
    }

    private function rejectReport(array $report, string $failure): array
    {
        $report['auditoria_aprovada'] = false;
        $report['status'] = 'reprovado';
        $report['auditoria']['falhas'][] = $failure;

        return $report;
    }
}
