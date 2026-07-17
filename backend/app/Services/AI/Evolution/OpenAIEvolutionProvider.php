<?php

namespace App\Services\AI\Evolution;

use App\Models\User;
use App\Services\AI\AIProviderService;
use RuntimeException;

class OpenAIEvolutionProvider
{
    public function __construct(
        private AIProviderService $provider,
        private EvolutionSchemaValidator $schemaValidator,
    ) {}

    public function compare(User $user, array $context): array
    {
        $schema = $this->schema('evolution_comparison.schema.json');

        $response = $this->provider->structuredCall(
            user: $user,
            messages: [
                [
                    'role' => 'system',
                    'content' => 'You compare body evolution records conservatively. Return only JSON that matches the schema. Do not diagnose, estimate body fat from images, identify the person, or infer confirmed muscle gain.',
                ],
                [
                    'role' => 'user',
                    'content' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ],
            ],
            agentName: 'evolution_visual_comparison',
            modelName: (string) config('ai_evolution.openai.comparison_model'),
            schemaName: 'evolution_comparison',
            schema: $schema,
            context: [
                'store' => (bool) config('ai_evolution.openai.store', false),
                'temperature' => 0.2,
            ],
        );

        if (! ($response['ok'] ?? false)) {
            throw new RuntimeException((string) ($response['error'] ?? 'OpenAI comparison failed.'));
        }

        $payload = json_decode((string) ($response['message'] ?? ''), true);
        if (! is_array($payload)) {
            throw new RuntimeException('OpenAI comparison returned invalid JSON.');
        }

        return $this->schemaValidator->validateComparison($payload);
    }

    public function audit(User $user, array $context): array
    {
        $schema = $this->schema('report_audit.schema.json');

        $response = $this->provider->structuredCall(
            user: $user,
            messages: [
                [
                    'role' => 'system',
                    'content' => 'You audit body evolution claims. Approve only evidence-backed conservative claims. Rewrite or reject unsupported, medical, biometric, body-fat, or absolute visual claims. Return only JSON that matches the schema.',
                ],
                [
                    'role' => 'user',
                    'content' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ],
            ],
            agentName: 'evolution_report_audit',
            modelName: (string) config('ai_evolution.openai.audit_model'),
            schemaName: 'report_audit',
            schema: $schema,
            context: [
                'store' => (bool) config('ai_evolution.openai.store', false),
                'temperature' => 0.1,
            ],
        );

        if (! ($response['ok'] ?? false)) {
            throw new RuntimeException((string) ($response['error'] ?? 'OpenAI audit failed.'));
        }

        $payload = json_decode((string) ($response['message'] ?? ''), true);
        if (! is_array($payload)) {
            throw new RuntimeException('OpenAI audit returned invalid JSON.');
        }

        return $this->schemaValidator->validateAudit($payload);
    }

    private function schema(string $file): array
    {
        $path = __DIR__.DIRECTORY_SEPARATOR.'Schemas'.DIRECTORY_SEPARATOR.$file;
        $schema = json_decode((string) file_get_contents($path), true);

        if (! is_array($schema)) {
            throw new RuntimeException("Invalid schema file: {$file}");
        }

        unset($schema['$schema'], $schema['title']);

        return $schema;
    }
}
