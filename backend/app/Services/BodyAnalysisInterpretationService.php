<?php

namespace App\Services;

class BodyAnalysisInterpretationService
{
    public const VERSION = 'mediapipe_pose_rules_v2';

    public function interpret(?array $metrics, string $viewType, ?array $visionAnalysis = null): array
    {
        $metrics = $metrics ?? [];
        $attention = [];
        $recommendations = [];
        $exercises = [];

        $summary = 'Foto processada com pontos corporais detectados. Use esta leitura como apoio visual, nao como diagnostico.';
        $recovery = 'Priorize sono, hidratacao e consistencia no treino. Ajustes nutricionais personalizados devem considerar avaliacao fisica e alimentar.';

        $confidence = $this->number($metrics, 'landmark_confidence');
        if ($confidence !== null && $confidence < 0.65) {
            $attention[] = 'A confianca dos pontos detectados esta baixa. Refaça a foto com corpo inteiro visivel, boa luz e fundo simples.';
        }

        $shoulders = abs((float) ($metrics['asymmetry_shoulders'] ?? 0));
        $hips = abs((float) ($metrics['asymmetry_hips'] ?? 0));
        $postureScore = $this->number($metrics, 'posture_score');
        $headForward = $this->number($metrics, 'head_forward_score');

        if ($shoulders > 5) {
            $attention[] = 'Ha diferenca visual relevante no alinhamento dos ombros.';
            $recommendations[] = 'Inclua exercicios unilaterais e controle de escapulas para reduzir compensacoes.';
            $exercises[] = 'Remada unilateral com pausa';
            $exercises[] = 'Elevacao lateral unilateral';
        }

        if ($hips > 5) {
            $attention[] = 'Ha diferenca visual relevante no alinhamento do quadril.';
            $recommendations[] = 'Trabalhe estabilidade de core e gluteos, com foco em controle pelvico.';
            $exercises[] = 'Ponte unilateral';
            $exercises[] = 'Prancha lateral';
        }

        if ($postureScore !== null && $postureScore < 70) {
            $attention[] = 'A postura geral ficou abaixo do alvo visual esperado para esta foto.';
            $recommendations[] = 'Revise mobilidade toracica, fortalecimento de costas e controle de tronco.';
            $exercises[] = 'Face pull';
            $exercises[] = 'Dead bug';
        }

        if ($viewType === 'side' && $headForward !== null && $headForward > 8) {
            $attention[] = 'A vista lateral sugere cabeca projetada a frente em relacao ao tronco.';
            $recommendations[] = 'Inclua mobilidade cervical leve e fortalecimento de flexores profundos do pescoco.';
            $exercises[] = 'Chin tuck';
        }

        if ($attention !== []) {
            $summary = implode(' ', $attention);
        }

        if ($recommendations === []) {
            $recommendations[] = 'Mantenha o plano atual e repita fotos padronizadas para acompanhar tendencia, nao uma leitura isolada.';
            $exercises = ['Agachamento livre tecnico', 'Remada baixa', 'Mobilidade toracica'];
        }

        $result = [
            'version' => self::VERSION,
            'summary' => $summary,
            'vision_summary' => $visionAnalysis['summary'] ?? null,
            'attention_points' => array_values(array_unique($attention)),
            'limitations' => [
                'Analise estimativa por foto 2D com pontos do MediaPipe.',
                'Nao substitui avaliacao presencial de educador fisico, fisioterapeuta ou medico.',
                'Iluminacao, roupa, lente e enquadramento podem alterar a leitura.',
            ],
            'diet' => $recovery,
            'workout' => implode(' ', array_values(array_unique($recommendations))),
            'exercises' => array_values(array_unique($exercises)),
        ];

        if (is_array($visionAnalysis)) {
            $result['attention_points'] = array_values(array_unique(array_merge(
                $result['attention_points'],
                $this->stringList($visionAnalysis['attention_points'] ?? [])
            )));
            $result['limitations'] = array_values(array_unique(array_merge(
                $result['limitations'],
                $this->stringList($visionAnalysis['limitations'] ?? [])
            )));
            $result['workout'] = trim($result['workout'] . ' ' . (string) ($visionAnalysis['training_notes'] ?? ''));
        }

        return $result;
    }

    private function number(array $metrics, string $key): ?float
    {
        if (!isset($metrics[$key]) || !is_numeric($metrics[$key])) {
            return null;
        }

        return (float) $metrics[$key];
    }

    private function stringList(mixed $value): array
    {
        if (is_string($value) && $value !== '') {
            return [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, fn ($item) => is_string($item) && $item !== ''));
    }
}
