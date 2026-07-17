<?php

namespace App\Services\AI\Evolution;

use App\Models\EvolutionSessionAnalysis;
use App\Models\User;

class EvolutionVisualComparisonAgent
{
    public function __construct(
        private EvolutionSchemaValidator $schemaValidator,
        private OpenAIEvolutionProvider $openAIProvider,
    ) {}

    public function compare(User $user, array $current, ?array $previous): array
    {
        $analysis = EvolutionSessionAnalysis::query()
            ->where('user_id', $user->id)
            ->whereDate('session_date', $current['date'])
            ->latest()
            ->first();

        if (! $analysis) {
            return [];
        }

        $data = $analysis->analysis ?? [];
        $items = $this->localClaims($analysis->id, $data, $current, $previous);

        if (config('ai_evolution.provider') === 'openai' && config('services.openai.api_key')) {
            try {
                $payload = $this->openAIProvider->compare($user, [
                    'current_record' => $current,
                    'previous_record' => $previous,
                    'saved_session_analysis' => $data,
                    'evidence_ids_available' => collect($items)->pluck('evidence_ids')->flatten()->values()->all(),
                    'rules' => [
                        'no_medical_diagnosis',
                        'no_body_fat_percentage_from_photos',
                        'no_biometric_identification',
                        'no_confirmed_muscle_gain_from_photos',
                    ],
                ]);

                return $payload['claims'];
            } catch (\Throwable) {
                // Conservative fallback: use deterministic local claims.
            }
        }

        $payload = $this->schemaValidator->validateComparison([
            'claims' => $items,
            'limitations' => [],
        ]);

        return $payload['claims'];
    }

    private function localClaims(int $analysisId, array $data, array $current, ?array $previous): array
    {
        $items = [];

        foreach (['summary', 'posture_notes', 'comparison_quality'] as $key) {
            $value = $data[$key] ?? null;
            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $confidence = $previous ? 0.70 : 0.55;
            $text = $this->safeLanguage($value, (bool) $previous);

            $items[] = [
                'id' => 'visual_'.$analysisId.'_'.$key,
                'text' => $text,
                'type' => 'visual_observation',
                'body_region' => 'general_silhouette',
                'evidence_ids' => ['analysis_'.$analysisId.'_'.$key],
                'confidence' => $confidence,
                'publishable' => true,
                'source' => 'evolution_session_analyses.'.$key,
                'current_record' => $current['date'],
                'previous_record' => $previous['date'] ?? null,
                'evidence' => ['analysis_id' => $analysisId, 'field' => $key],

                'texto' => $text,
                'fonte' => 'evolution_session_analyses.'.$key,
                'registro_atual' => $current['date'],
                'registro_anterior' => $previous['date'] ?? null,
                'evidencia' => ['analysis_id' => $analysisId, 'field' => $key],
                'confianca' => $confidence,
                'limitacao' => $previous ? null : 'Linha de base: sem registro anterior para comparacao temporal.',
            ];
        }

        return $items;
    }

    private function safeLanguage(string $text, bool $hasPrevious): string
    {
        $text = trim($text);
        $lower = mb_strtolower($text);

        if (! $hasPrevious) {
            return 'Registro de linha de base: '.$text;
        }

        if (! str_contains($lower, 'possivel') && ! str_contains($lower, 'possível') && ! str_contains($lower, 'nao foi possivel')) {
            return 'Ha possivel diferenca visual observavel: '.$text;
        }

        return $text;
    }
}
