<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class ClinicalAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'clinical';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $subject = $this->resolveSubjectUser($user, $context);

            $assessment = \App\Models\BodyAssessment::where('user_id', $subject->id)
                ->latest('assessment_date')
                ->first();

            $clinicalContext = 'O usuário está solicitando uma análise clínica ou de saúde.';
            if ($assessment) {
                $clinicalContext .= " Dados da última avaliação ({$assessment->assessment_date}): ";
                $clinicalContext .= "Peso: {$assessment->weight_kg}kg, BF: {$assessment->bf_percent}%, ";
                $clinicalContext .= "Músculo: {$assessment->muscle_mass_kg}kg, Gordura Visceral: {$assessment->visceral_fat}.";
            } else {
                $clinicalContext .= ' O usuário ainda não possui avaliações físicas registradas.';
            }

            $promptFile = base_path('agentesprd/clinical-insights-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Clinical Specialist da NexShape. Analise dados de saúde e bioimpedância. NUNCA dê diagnósticos médicos definitivos.';

            $messages = [
                [
                    'role' => 'system',
                    'content' => $instructions."\n\nContexto do paciente: {$clinicalContext}",
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
}
