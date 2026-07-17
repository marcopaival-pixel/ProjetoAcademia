<?php

namespace App\Services;

use App\Models\SmartStack;
use App\Models\User;
use App\Services\AI\AIProviderService;
use App\Services\AI\PrescriptionSafetyAuditor;
use Exception;
use Illuminate\Support\Facades\Log;

class SmartStackAIService
{
    public function __construct(
        private AIProviderService $aiProvider,
        private PrescriptionSafetyAuditor $safetyAuditor
    ) {}

    public function suggestStack(User $user, string $goal = null): array
    {
        $result = $this->aiProvider->call($user, [
            ['role' => 'system', 'content' => 'Voce sugere suplementacao conservadora e segura. Nao recomende medicamentos, hormonios ou substancias ilegais. Retorne apenas JSON valido.'],
            ['role' => 'user', 'content' => json_encode($this->getUserContext($user, $goal), JSON_UNESCAPED_UNICODE)],
        ], 'smart_stack_suggestion', 'main', [
            'temperature' => 0.2,
            'response_format' => ['type' => 'json_object'],
            'feature_key' => 'smart_stack_suggestion',
        ]);

        if (! ($result['ok'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'IA indisponivel'];
        }

        $audit = $this->safetyAuditor->audit($user, 'nutrition', $result['message'], ['feature_key' => 'smart_stack_suggestion']);
        if (! ($audit['approved'] ?? false)) {
            Log::warning('SmartStack suggestion blocked by audit', ['audit' => $audit]);
            return ['success' => false, 'error' => 'Sugestao bloqueada pela auditoria de seguranca.', 'audit' => $audit];
        }

        return ['success' => true, 'suggestion' => json_decode((string) $result['message'], true) ?: []];
    }

    public function analyzeStack(SmartStack $stack): array
    {
        return ['alerts' => []];
    }

    private function getUserContext(User $user, string $goal = null): array
    {
        try {
            $user->load(['profile', 'assessments']);
            $profile = $user->profile;
            $latestAssessment = $user->assessments()->orderBy('assessment_date', 'desc')->first();

            return [
                'age' => $profile?->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->age : null,
                'weight' => $latestAssessment?->weight_kg ?? $profile?->weight_kg,
                'sex' => $profile?->sex,
                'goal' => $goal ?? $profile?->goal ?? 'Saude geral',
                'activity_level' => $profile?->activity_level,
            ];
        } catch (Exception $e) {
            return ['goal' => $goal ?? 'Saude geral'];
        }
    }
}
