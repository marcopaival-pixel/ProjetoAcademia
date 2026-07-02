<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

/**
 * PsychologyAgent — Agente de Saúde Mental
 *
 * AVISO CRÍTICO DE PRIVACIDADE:
 * Este agente lida com dados extremamente sensíveis de saúde mental.
 * - NUNCA acesse nem exponha notas marcadas como is_confidential = true
 * - NUNCA revele conteúdo de sessões de psicologia ao paciente além do que o profissional liberou
 * - NUNCA sugira alteração de medicamentos psiquiátricos
 */
class PsychologyAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'psychology';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $subject = $this->resolveSubjectUser($user, $context);
            $moodContext = $this->getMoodContext($subject);

            $promptFile = base_path('agentesprd/psychology-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Psychology Support Specialist da NexShape. Ofereça suporte emocional acolhedor e técnicas de bem-estar. NUNCA dê diagnósticos psiquiátricos nem sugira medicamentos.';

            $messages = [
                [
                    'role'    => 'system',
                    'content' => $instructions . "\n\n" . $moodContext,
                ],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'main',
                context: array_merge(['temperature' => 0.6], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getMoodContext(User $user): string
    {
        $moodLogs = \App\Models\MoodLog::where('user_id', $user->id)
            ->visibleToPatient()
            ->latest('logged_at')
            ->limit(7)
            ->get(['logged_at', 'mood_score', 'energy_level', 'sleep_hours', 'stress_level', 'notes']);

        if ($moodLogs->isEmpty()) {
            return "CONTEXTO: O paciente {$user->name} ainda não possui registros de humor ou bem-estar.";
        }

        $summary = $moodLogs->map(fn ($log) =>
            "- {$log->logged_at->format('d/m/Y')}: humor {$log->mood_score}/10"
            . ($log->energy_level !== null ? ", energia {$log->energy_level}/10" : '')
            . ($log->sleep_hours !== null ? ", sono {$log->sleep_hours}h" : '')
            . ($log->stress_level !== null ? ", estresse {$log->stress_level}/10" : '')
        )->implode("\n");

        return "CONTEXTO DE BEM-ESTAR — Paciente: {$user->name}\nRegistros recentes (apenas dados do próprio paciente):\n{$summary}";
    }
}
