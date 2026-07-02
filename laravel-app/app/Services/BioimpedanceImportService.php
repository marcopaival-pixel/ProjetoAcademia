<?php

namespace App\Services;

use App\Models\BodyAssessment;
use Exception;

class BioimpedanceImportService
{
    /**
     * Parses raw input text or structured CSV data to extract bioimpedance metrics.
     */
    public function parseAndCreate(array $data, int $userId, ?int $professionalId = null): BodyAssessment
    {
        $parsedMetrics = [];

        // If a file is uploaded (CSV or raw text/JSON)
        if (isset($data['file_content'])) {
            $parsedMetrics = $this->parseFileContent($data['file_content'], $data['file_type'] ?? 'csv');
        } else {
            // Direct array mapping from standard input form
            $parsedMetrics = $data;
        }

        // Fill mapping to BodyAssessment properties
        $assessmentData = array_merge([
            'user_id' => $userId,
            'professional_id' => $professionalId,
            'assessment_date' => $data['assessment_date'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? 'Importado automaticamente via Bioimpedance Importer',
            'status' => 'approved',
        ], $this->filterValidFields($parsedMetrics));

        return BodyAssessment::create($assessmentData);
    }

    private function parseFileContent(string $content, string $type): array
    {
        if (strtolower($type) === 'csv') {
            return $this->parseCsv($content);
        }

        if (strtolower($type) === 'json') {
            return json_decode($content, true) ?? [];
        }

        // Fallback: simple text parsing using key-value heuristics
        return $this->parseTextHeuristic($content);
    }

    private function parseCsv(string $content): array
    {
        $metrics = [];
        $lines = explode("\n", str_replace("\r", "", $content));
        foreach ($lines as $line) {
            $cols = str_getcsv($line);
            if (count($cols) >= 2) {
                $key = trim($cols[0]);
                $val = trim($cols[1]);
                $metrics[$key] = $val;
            }
        }
        return $metrics;
    }

    private function parseTextHeuristic(string $content): array
    {
        $metrics = [];
        // Extract common InBody patterns using regex
        $patterns = [
            'weight_kg' => '/(?:Peso|Weight|Mass)\s*:\s*([\d\.,]+)/i',
            'bf_percent' => '/(?:Percentual de Gordura|BF%|Body Fat %)\s*:\s*([\d\.,]+)/i',
            'muscle_percent' => '/(?:Massa Muscular|Muscle %)\s*:\s*([\d\.,]+)/i',
            'icw_l' => '/(?:Agua Intracelular|ICW)\s*:\s*([\d\.,]+)/i',
            'ecw_l' => '/(?:Agua Extracelular|ECW)\s*:\s*([\d\.,]+)/i',
            'visceral_fat_level' => '/(?:Gordura Visceral|Visceral Fat)\s*:\s*([\d\.,]+)/i',
            'basal_metabolic_rate' => '/(?:Metabolismo Basal|TMB|BMR)\s*:\s*([\d\.,]+)/i',
            'phase_angle' => '/(?:Angulo de Fase|Phase Angle)\s*:\s*([\d\.,]+)/i',
        ];

        foreach ($patterns as $field => $regex) {
            if (preg_match($regex, $content, $matches)) {
                $val = str_replace(',', '.', $matches[1]);
                $metrics[$field] = (float) $val;
            }
        }

        return $metrics;
    }

    private function filterValidFields(array $input): array
    {
        $allowed = [
            'weight_kg', 'bf_percent', 'muscle_percent', 'neck', 'chest', 'waist', 'abdomen', 'hips',
            'bicep_l', 'bicep_r', 'forearm_l', 'forearm_r', 'thigh_l', 'thigh_r', 'calf_l', 'calf_r',
            'blood_pressure', 'heart_rate', 'icw_l', 'ecw_l', 'dry_lean_mass_kg', 'body_fat_mass_kg',
            'segmental_lean_arm_l', 'segmental_lean_arm_r', 'segmental_lean_leg_l', 'segmental_lean_leg_r',
            'segmental_lean_trunk', 'visceral_fat_level', 'basal_metabolic_rate', 'phase_angle'
        ];

        $filtered = [];
        foreach ($input as $key => $value) {
            if (in_array($key, $allowed)) {
                $filtered[$key] = is_numeric($value) ? (float) $value : $value;
            }
        }
        return $filtered;
    }
}
