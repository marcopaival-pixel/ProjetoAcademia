<?php

namespace App\Services\AI\Evolution;

class ClaimPublicationPolicy
{
    public function filter(array $claims): array
    {
        return array_values(array_filter($claims, fn (array $claim) => $this->allows($claim)));
    }

    public function allows(array $claim): bool
    {
        if (($claim['publishable'] ?? true) !== true) {
            return false;
        }

        if (empty($claim['evidence_ids']) && empty($claim['evidencia']) && empty($claim['fonte'])) {
            return false;
        }

        if ((float) ($claim['confidence'] ?? $claim['confianca'] ?? 0) < config('ai_evolution.thresholds.claim_confidence_min', 0.70)) {
            return false;
        }

        $type = (string) ($claim['type'] ?? $claim['tipo'] ?? 'visual_observation');
        if (! in_array($type, config('ai_evolution.allowed_claim_types', []), true)) {
            return false;
        }

        $region = $claim['body_region'] ?? null;
        if ($region !== null && ! in_array($region, config('ai_evolution.allowed_body_regions', []), true)) {
            return false;
        }

        return ! $this->containsForbiddenLanguage((string) ($claim['text'] ?? $claim['texto'] ?? ''));
    }

    public function containsForbiddenLanguage(string $text): bool
    {
        $normalized = mb_strtolower($text);

        foreach (config('ai_evolution.forbidden_terms', []) as $term) {
            if (str_contains($normalized, mb_strtolower($term))) {
                return true;
            }
        }

        return false;
    }
}
