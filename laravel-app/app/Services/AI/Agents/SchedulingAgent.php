<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class SchedulingAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'scheduling';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $subject = $this->resolveSubjectUser($user, $context);
            $scheduleContext = $this->getScheduleContext($subject);

            $promptFile = base_path('agentesprd/scheduling-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Scheduling Specialist da NexShape. Auxilie com dúvidas sobre agendamentos, consultas e disponibilidade. Não confirme nem altere agendamentos diretamente.';

            $messages = [
                [
                    'role'    => 'system',
                    'content' => $instructions . "\n\n" . $scheduleContext,
                ],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'fast',
                context: array_merge(['temperature' => 0.3], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getScheduleContext(User $user): string
    {
        $upcoming = \App\Models\ProfessionalAppointment::where('patient_id', $user->id)
            ->where('appointment_at', '>=', now())
            ->orderBy('appointment_at')
            ->limit(5)
            ->with('professional:id,name')
            ->get(['id', 'appointment_at', 'status', 'professional_id', 'service_type', 'notes']);

        $past = \App\Models\ProfessionalAppointment::where('patient_id', $user->id)
            ->where('appointment_at', '<', now())
            ->orderByDesc('appointment_at')
            ->limit(3)
            ->with('professional:id,name')
            ->get(['id', 'appointment_at', 'status', 'professional_id']);

        $upcomingText = $upcoming->isEmpty()
            ? 'Nenhuma consulta agendada.'
            : $upcoming->map(fn ($a) =>
                "- {$a->appointment_at->format('d/m/Y H:i')} com {$a->professional?->name}"
                . ($a->service_type ? " ({$a->service_type})" : '')
                . " [{$a->status_label}]"
            )->implode("\n");

        $pastText = $past->isEmpty()
            ? 'Nenhum histórico.'
            : $past->map(fn ($a) =>
                "- {$a->appointment_at->format('d/m/Y H:i')} com {$a->professional?->name} [{$a->status_label}]"
            )->implode("\n");

        return "CONTEXTO DE AGENDA — Paciente: {$user->name}\n\nPróximas consultas:\n{$upcomingText}\n\nÚltimas consultas:\n{$pastText}";
    }
}
