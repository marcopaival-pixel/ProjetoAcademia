<?php

namespace App\Services\AI\Evolution;

class EvolutionObjectiveDataAgent
{
    public function extract($assessments): array
    {
        $current = $assessments->first();
        $previous = $assessments->skip(1)->first();
        $items = [];

        foreach ([
            'weight_kg' => 'Peso',
            'bf_percent' => 'Gordura corporal',
            'muscle_percent' => 'Massa muscular percentual',
            'waist' => 'Cintura',
            'abdomen' => 'Abdômen',
            'hips' => 'Quadril',
        ] as $field => $label) {
            if ($current?->{$field} === null) {
                continue;
            }

            $delta = null;
            $percent = null;
            if ($previous?->{$field} !== null) {
                $delta = round((float) $current->{$field} - (float) $previous->{$field}, 2);
                if ((float) $previous->{$field} !== 0.0) {
                    $percent = round(($delta / (float) $previous->{$field}) * 100, 2);
                }
            }

            $items[] = [
                'metrica' => $label,
                'atual' => (float) $current->{$field},
                'anterior' => $previous?->{$field} !== null ? (float) $previous->{$field} : null,
                'variacao_absoluta' => $delta,
                'variacao_percentual' => $percent,
                'fonte' => 'body_assessments.'.$field,
                'confianca' => 1.0,
            ];
        }

        return $items;
    }
}
