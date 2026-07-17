<?php

namespace App\Services\AI\Evolution;

class EvolutionImageValidationAgent
{
    private const REQUIRED_ANGLES = ['front', 'back', 'right_side', 'left_side'];

    public function validate(array $sessions): array
    {
        $validated = [];
        $failures = [];

        foreach ($sessions as $session) {
            $missing = array_values(array_diff(self::REQUIRED_ANGLES, $session['normalized_types'] ?? []));
            $duplicateTypes = collect($session['types'] ?? [])
                ->countBy()
                ->filter(fn ($count) => $count > 1)
                ->keys()
                ->values()
                ->all();

            $session['validation'] = [
                'approved' => $missing === [],
                'missing_angles' => $missing,
                'duplicate_angles' => $duplicateTypes,
                'message' => $missing === []
                    ? 'Registro completo para análise.'
                    : 'Para gerar uma comparação confiável, complete as quatro fotos e registre seus dados atuais.',
            ];

            if ($session['validation']['approved']) {
                $validated[] = $session;
            } else {
                $failures[] = [
                    'date' => $session['date'] ?? null,
                    'reason' => $session['validation']['message'],
                    'missing_angles' => $missing,
                ];
            }
        }

        return [
            'approved_sessions' => $validated,
            'failures' => $failures,
        ];
    }
}
