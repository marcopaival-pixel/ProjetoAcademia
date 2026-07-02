<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class PainAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'pain';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $subject = $this->resolveSubjectUser($user, $context);
            $painContext = $this->getPainContext($subject);

            $promptFile = base_path('agentesprd/pain-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Pain Specialist da NexShape. Auxilie pacientes de fisioterapia na compreensão do seu diário de dor. NUNCA dê diagnósticos médicos.';

            $messages = [
                [
                    'role'    => 'system',
                    'content' => $instructions . "\n\n" . $painContext,
                ],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'main',
                context: array_merge(['temperature' => 0.4], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getPainContext(User $user): string
    {
        $logs = \App\Models\PainRecord::where('user_id', $user->id)
            ->latest('assessment_date')
            ->limit(7)
            ->get(['assessment_date', 'eva_level', 'pain_points', 'notes']);

        if ($logs->isEmpty()) {
            return "CONTEXTO: O paciente {$user->name} ainda não possui registros de dor no diário.";
        }

        $summary = $logs->map(function ($log) {
            $regions = collect($log->pain_points)->pluck('region')->filter()->implode(', ') ?: 'não especificada';
            return "- {$log->assessment_date->format('d/m/Y')}: EVA {$log->eva_level}/10, região: {$regions}"
                . ($log->notes ? " ({$log->notes})" : '');
        })->implode("\n");

        return "CONTEXTO DO DIÁRIO DE DOR — Paciente: {$user->name}\nÚltimos registros:\n{$summary}";
    }
}
