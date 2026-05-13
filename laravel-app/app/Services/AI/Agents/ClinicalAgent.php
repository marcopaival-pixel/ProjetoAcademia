<?php

namespace App\Services\AI\Agents;

use App\Models\User;

class ClinicalAgent extends BaseAgent {
    public function getName(): string { return 'clinical'; }

    public function execute(User $user, string $message, array $context = []): array
    {
        $assessment = \App\Models\BodyAssessment::where('user_id', $user->id)
            ->latest('assessment_date')
            ->first();

        $clinicalContext = "O usuário está solicitando uma análise clínica ou de saúde.";
        if ($assessment) {
            $clinicalContext .= " Dados da última avaliação ({$assessment->assessment_date}): ";
            $clinicalContext .= "Peso: {$assessment->weight_kg}kg, BF: {$assessment->bf_percent}%, ";
            $clinicalContext .= "Músculo: {$assessment->muscle_mass_kg}kg, Gordura Visceral: {$assessment->visceral_fat}.";
        } else {
            $clinicalContext .= " O usuário ainda não possui avaliações físicas registradas.";
        }

        $systemPrompt = "Você é o Clinical Specialist da NexShape. Sua função é analisar dados de saúde e bioimpedância. 
        Sempre seja profissional, técnico e encorajador. 
        NUNCA dê diagnósticos médicos definitivos, sempre recomende acompanhamento profissional se notar algo fora do normal.
        Contexto do paciente: {$clinicalContext}";

        return $this->aiProvider->generateResponse($systemPrompt, $message, [
            'model' => 'main',
            'user_id' => $user->id,
            'clinic_id' => $context['clinic_id'] ?? null,
            'agent_name' => $this->getName()
        ]);
    }
}
