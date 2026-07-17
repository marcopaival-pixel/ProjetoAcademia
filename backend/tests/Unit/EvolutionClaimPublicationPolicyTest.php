<?php

namespace Tests\Unit;

use App\Services\AI\Evolution\ClaimPublicationPolicy;
use Tests\TestCase;

class EvolutionClaimPublicationPolicyTest extends TestCase
{
    public function test_it_allows_conservative_visual_claim_with_evidence(): void
    {
        $policy = new ClaimPublicationPolicy();

        $this->assertTrue($policy->allows([
            'id' => 'claim_001',
            'text' => 'Ha possivel diferenca visual observavel na silhueta geral.',
            'type' => 'visual_observation',
            'body_region' => 'general_silhouette',
            'evidence_ids' => ['front_previous', 'front_current'],
            'confidence' => 0.78,
            'publishable' => true,
        ]));
    }

    public function test_it_rejects_claim_without_evidence(): void
    {
        $policy = new ClaimPublicationPolicy();

        $this->assertFalse($policy->allows([
            'id' => 'claim_002',
            'text' => 'Ha possivel diferenca visual observavel.',
            'type' => 'visual_observation',
            'evidence_ids' => [],
            'confidence' => 0.90,
            'publishable' => true,
        ]));
    }

    public function test_it_rejects_low_confidence_claim(): void
    {
        $policy = new ClaimPublicationPolicy();

        $this->assertFalse($policy->allows([
            'id' => 'claim_003',
            'text' => 'Ha possivel diferenca visual observavel.',
            'type' => 'visual_observation',
            'evidence_ids' => ['front_previous', 'front_current'],
            'confidence' => 0.69,
            'publishable' => true,
        ]));
    }

    public function test_it_rejects_medical_or_body_fat_language(): void
    {
        $policy = new ClaimPublicationPolicy();

        $this->assertFalse($policy->allows([
            'id' => 'claim_004',
            'text' => 'O percentual de gordura corporal estimada diminuiu.',
            'type' => 'visual_observation',
            'evidence_ids' => ['front_previous', 'front_current'],
            'confidence' => 0.95,
            'publishable' => true,
        ]));
    }
}
