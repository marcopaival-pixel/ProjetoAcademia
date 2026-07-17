<?php

namespace Tests\Unit;

use App\Services\AI\Evolution\EvolutionSchemaValidator;
use InvalidArgumentException;
use Tests\TestCase;

class EvolutionSchemaValidatorTest extends TestCase
{
    public function test_it_accepts_valid_comparison_payload(): void
    {
        $validator = new EvolutionSchemaValidator();

        $payload = $validator->validateComparison([
            'claims' => [[
                'id' => 'claim_001',
                'text' => 'Ha possivel diferenca visual.',
                'type' => 'visual_observation',
                'body_region' => 'general_silhouette',
                'evidence_ids' => ['front_previous', 'front_current'],
                'confidence' => 0.78,
                'publishable' => true,
            ]],
            'limitations' => [],
        ]);

        $this->assertSame('claim_001', $payload['claims'][0]['id']);
    }

    public function test_it_rejects_comparison_claim_without_evidence_ids(): void
    {
        $validator = new EvolutionSchemaValidator();

        $this->expectException(InvalidArgumentException::class);

        $validator->validateComparison([
            'claims' => [[
                'id' => 'claim_001',
                'text' => 'Ha possivel diferenca visual.',
                'type' => 'visual_observation',
                'confidence' => 0.78,
                'publishable' => true,
            ]],
            'limitations' => [],
        ]);
    }

    public function test_it_accepts_valid_audit_payload(): void
    {
        $validator = new EvolutionSchemaValidator();

        $payload = $validator->validateAudit([
            'approved' => true,
            'claims' => [[
                'claim_id' => 'claim_001',
                'decision' => 'approve',
                'reason' => 'Evidencia suficiente.',
            ]],
        ]);

        $this->assertTrue($payload['approved']);
    }

    public function test_it_rejects_invalid_audit_decision(): void
    {
        $validator = new EvolutionSchemaValidator();

        $this->expectException(InvalidArgumentException::class);

        $validator->validateAudit([
            'approved' => false,
            'claims' => [[
                'claim_id' => 'claim_001',
                'decision' => 'publish_anyway',
                'reason' => 'Invalid.',
            ]],
        ]);
    }
}
