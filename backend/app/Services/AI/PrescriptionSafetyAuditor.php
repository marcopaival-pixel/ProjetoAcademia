<?php

namespace App\Services\AI;

use App\Models\User;

class PrescriptionSafetyAuditor
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function audit(User $user, string $type, mixed $content, array $context = []): array
    {
        $payload = is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE);

        $result = $this->aiProvider->call($user, [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'Voce e um auditor de seguranca para prescricoes de treino, nutricao e orientacao clinica.',
                    'Aprove apenas conteudo conservador, sem diagnostico medico, medicamento, anabolizante, dieta extrema, promessa absoluta ou conduta sem profissional.',
                    'Retorne exclusivamente JSON valido: {"approved": boolean, "risk_level": "low|medium|high", "issues": string[], "safe_content": string|null}.',
                ]),
            ],
            [
                'role' => 'user',
                'content' => "Tipo: {$type}\nConteudo:\n{$payload}",
            ],
        ], 'prescription_safety_auditor', 'fast', array_merge($context, [
            'temperature' => 0,
            'max_tokens' => 700,
            'response_format' => ['type' => 'json_object'],
        ]));

        if (! ($result['ok'] ?? false)) {
            return [
                'approved' => false,
                'risk_level' => 'high',
                'issues' => [$result['error'] ?? 'Auditoria indisponivel.'],
                'safe_content' => null,
                'source' => 'ai_error',
            ];
        }

        $decoded = json_decode((string) ($result['message'] ?? ''), true);
        if (! is_array($decoded)) {
            return [
                'approved' => false,
                'risk_level' => 'high',
                'issues' => ['Auditoria retornou formato invalido.'],
                'safe_content' => null,
                'source' => 'invalid_audit',
            ];
        }

        return [
            'approved' => (bool) ($decoded['approved'] ?? false),
            'risk_level' => (string) ($decoded['risk_level'] ?? 'high'),
            'issues' => is_array($decoded['issues'] ?? null) ? $decoded['issues'] : [],
            'safe_content' => $decoded['safe_content'] ?? null,
            'source' => 'ai',
        ];
    }
}
