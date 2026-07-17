<?php

namespace Tests\Unit;

use App\Services\BodyAnalysisInterpretationService;
use PHPUnit\Framework\TestCase;

class BodyAnalysisInterpretationServiceTest extends TestCase
{
    public function test_it_flags_hip_asymmetry_low_confidence_and_side_head_projection(): void
    {
        $service = new BodyAnalysisInterpretationService();

        $result = $service->interpret([
            'asymmetry_shoulders' => 2,
            'asymmetry_hips' => 8,
            'posture_score' => 62,
            'head_forward_score' => 12,
            'landmark_confidence' => 0.4,
        ], 'side');

        $this->assertSame('mediapipe_pose_rules_v2', $result['version']);
        $this->assertNotEmpty($result['attention_points']);
        $this->assertStringContainsString('quadril', implode(' ', $result['attention_points']));
        $this->assertStringContainsString('confianca', implode(' ', $result['attention_points']));
        $this->assertStringContainsString('cabeca', implode(' ', $result['attention_points']));
    }

    public function test_it_merges_optional_vision_analysis(): void
    {
        $service = new BodyAnalysisInterpretationService();

        $result = $service->interpret([
            'posture_score' => 95,
            'landmark_confidence' => 0.9,
        ], 'front', [
            'summary' => 'Leitura visual complementar.',
            'attention_points' => ['Ajustar enquadramento nas proximas fotos.'],
            'limitations' => ['Roupa larga pode reduzir precisao.'],
            'training_notes' => 'Mantenha progressao gradual.',
        ]);

        $this->assertSame('Leitura visual complementar.', $result['vision_summary']);
        $this->assertContains('Ajustar enquadramento nas proximas fotos.', $result['attention_points']);
        $this->assertContains('Roupa larga pode reduzir precisao.', $result['limitations']);
        $this->assertStringContainsString('Mantenha progressao gradual.', $result['workout']);
    }
}
