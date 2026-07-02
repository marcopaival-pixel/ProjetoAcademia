<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

/**
 * MedicAgent — Agente de Apoio Médico
 *
 * AVISO CRÍTICO:
 * Este agente auxilia na interpretação de informações médicas com disclaimers obrigatórios.
 * - NUNCA emite diagnósticos definitivos
 * - NUNCA altera prescrições ou medicamentos
 * - SEMPRE recomenda consultar o médico responsável
 */
class MedicAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'medic';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $subject = $this->resolveSubjectUser($user, $context);
            $medicContext = $this->getMedicContext($subject);

            $promptFile = base_path('agentesprd/medic-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Medical Support Specialist da NexShape. Auxilie pacientes na compreensão de receitas e orientações médicas. NUNCA emita diagnósticos nem altere prescrições.';

            $messages = [
                [
                    'role'    => 'system',
                    'content' => $instructions . "\n\n" . $medicContext,
                ],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'main',
                context: array_merge(['temperature' => 0.3], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getMedicContext(User $user): string
    {
        $prescriptions = \App\Models\MedicalPrescription::where('patient_id', $user->id)
            ->latest('date')
            ->limit(5)
            ->with('professional:id,name')
            ->get(['id', 'date', 'medicine', 'dosage', 'frequency', 'duration', 'professional_id', 'observations']);

        if ($prescriptions->isEmpty()) {
            return "CONTEXTO: O paciente {$user->name} não possui receitas médicas registradas.";
        }

        $list = $prescriptions->map(fn ($p) =>
            "- [{$p->date->format('d/m/Y')}] {$p->medicine}"
            . ($p->dosage ? " — {$p->dosage}" : '')
            . ($p->frequency ? ", {$p->frequency}" : '')
            . ($p->duration ? ", por {$p->duration}" : '')
            . " (Dr(a). {$p->professional?->name})"
        )->implode("\n");

        return "CONTEXTO MÉDICO — Paciente: {$user->name}\nReceitas/Prescrições registradas:\n{$list}";
    }
}
