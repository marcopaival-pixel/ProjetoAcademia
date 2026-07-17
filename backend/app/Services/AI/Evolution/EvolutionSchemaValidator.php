<?php

namespace App\Services\AI\Evolution;

use InvalidArgumentException;

class EvolutionSchemaValidator
{
    public function validateComparison(array $payload): array
    {
        if (! array_key_exists('claims', $payload) || ! is_array($payload['claims'])) {
            throw new InvalidArgumentException('Comparison payload must include a claims array.');
        }

        foreach ($payload['claims'] as $index => $claim) {
            foreach (['id', 'text', 'type', 'evidence_ids', 'confidence', 'publishable'] as $field) {
                if (! array_key_exists($field, $claim)) {
                    throw new InvalidArgumentException("Comparison claim {$index} missing {$field}.");
                }
            }

            if (! is_array($claim['evidence_ids'])) {
                throw new InvalidArgumentException("Comparison claim {$index} evidence_ids must be an array.");
            }

            if (! is_numeric($claim['confidence'])) {
                throw new InvalidArgumentException("Comparison claim {$index} confidence must be numeric.");
            }
        }

        return $payload;
    }

    public function validateAudit(array $payload): array
    {
        if (! array_key_exists('approved', $payload) || ! is_bool($payload['approved'])) {
            throw new InvalidArgumentException('Audit payload must include boolean approved.');
        }

        if (! array_key_exists('claims', $payload) || ! is_array($payload['claims'])) {
            throw new InvalidArgumentException('Audit payload must include a claims array.');
        }

        foreach ($payload['claims'] as $index => $claim) {
            foreach (['claim_id', 'decision', 'reason'] as $field) {
                if (! array_key_exists($field, $claim)) {
                    throw new InvalidArgumentException("Audit claim {$index} missing {$field}.");
                }
            }

            if (! in_array($claim['decision'], ['approve', 'rewrite', 'reject'], true)) {
                throw new InvalidArgumentException("Audit claim {$index} has invalid decision.");
            }
        }

        return $payload;
    }
}
