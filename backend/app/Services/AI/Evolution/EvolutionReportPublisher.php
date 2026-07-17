<?php

namespace App\Services\AI\Evolution;

class EvolutionReportPublisher
{
    public function visualObservations(array $claims, array $audit): array
    {
        $audited = collect($audit['claims'] ?? [])->keyBy('claim_id');

        return collect($claims)
            ->filter(fn (array $claim) => ($claim['type'] ?? 'visual_observation') === 'visual_observation')
            ->map(function (array $claim) use ($audited) {
                $decision = $audited->get($claim['id'] ?? null);
                if ($decision && ($decision['decision'] ?? null) === 'reject') {
                    return null;
                }

                $text = $decision['safe_text'] ?? $this->templateFor($claim);

                return [
                    'texto' => $text,
                    'fonte' => $claim['source'] ?? $claim['fonte'] ?? 'evolution_visual_claim',
                    'registro_atual' => $claim['current_record'] ?? $claim['registro_atual'] ?? null,
                    'registro_anterior' => $claim['previous_record'] ?? $claim['registro_anterior'] ?? null,
                    'evidencia' => $claim['evidence'] ?? $claim['evidencia'] ?? ['ids' => $claim['evidence_ids'] ?? []],
                    'confianca' => round((float) ($claim['confidence'] ?? $claim['confianca'] ?? 0), 2),
                    'limitacao' => $claim['limitation'] ?? $claim['limitacao'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function templateFor(array $claim): string
    {
        $confidence = (float) ($claim['confidence'] ?? $claim['confianca'] ?? 0);
        $region = $this->regionLabel($claim['body_region'] ?? null);

        if ($confidence >= config('ai_evolution.thresholds.claim_consistent_min', 0.85)) {
            return "As fotos indicam uma diferenca visual consistente em {$region}, dentro das condicoes de comparacao.";
        }

        return "Foi observada uma possivel diferenca visual em {$region}, considerando as fotos disponiveis.";
    }

    private function regionLabel(?string $region): string
    {
        return match ($region) {
            'abdomen' => 'abdomen',
            'waist' => 'cintura',
            'arms' => 'bracos',
            'legs' => 'pernas',
            'shoulders' => 'ombros',
            'back' => 'costas',
            default => 'silhueta geral',
        };
    }
}
