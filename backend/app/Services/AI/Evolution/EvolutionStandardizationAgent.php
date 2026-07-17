<?php

namespace App\Services\AI\Evolution;

class EvolutionStandardizationAgent
{
    public function compare(?array $current, ?array $previous): array
    {
        if (!$current) {
            return ['status' => 'nao_analisavel', 'issues' => ['Nenhum registro completo encontrado.'], 'confidence_modifier' => -0.4];
        }

        $issues = [];
        if (!$previous) {
            $issues[] = 'Há apenas um registro completo; o sistema gera linha de base, não comparação evolutiva.';
        }

        $currentValidation = $current['validation'] ?? [];
        $previousValidation = $previous['validation'] ?? [];

        if (($currentValidation['approved'] ?? false) !== true) {
            $issues[] = $currentValidation['message'] ?? 'Registro atual incompleto.';
        }
        if ($previous && ($previousValidation['approved'] ?? false) !== true) {
            $issues[] = $previousValidation['message'] ?? 'Registro anterior incompleto.';
        }

        return [
            'status' => $issues === [] ? 'padronizacao_basica_aprovada' : 'aprovado_com_limitacoes',
            'issues' => $issues,
            'confidence_modifier' => $issues === [] ? 0.08 : -0.08,
        ];
    }
}
