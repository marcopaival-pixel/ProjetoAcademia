<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Cache;

class AiCommercialAssistantService
{
    /**
     * Estima a probabilidade de fechamento do Lead (0 a 100).
     * Simulação heurística baseada no funil consultivo.
     */
    public function estimateProbability(Lead $lead): int
    {
        return Cache::remember("lead_{$lead->id}_probability", 3600, function () use ($lead) {
            $baseProb = 5;

            // Peso da etapa atual do funil
            $stageWeights = [
                'Lead' => 5,
                'Contato' => 10,
                'Qualificado' => 20,
                'Diagnóstico' => 35,
                'Demonstração' => 50,
                'Proposta' => 65,
                'Negociação' => 80,
                'Fechado' => 100,
                'Implantação' => 100,
                'Treinamento' => 100,
                'Cliente Ativo' => 100,
            ];

            $baseProb = $stageWeights[$lead->status] ?? $baseProb;

            if ($lead->status === 'Perdido') {
                return 0;
            }

            // Aumenta probabilidade se tiver valor estimado alto (mostra maturidade)
            if ($lead->valor_estimado > 500) {
                $baseProb += 5;
            }

            // Aumenta probabilidade baseada no número de interações
            $interactionCount = $lead->interactions()->count();
            if ($interactionCount > 5) {
                $baseProb += 10;
            } elseif ($interactionCount > 2) {
                $baseProb += 5;
            }

            // Penaliza se estiver muito tempo parado (ex: mais de 7 dias sem atualização)
            if ($lead->updated_at && $lead->updated_at->diffInDays(now()) > 7 && $lead->status !== 'Fechado') {
                $baseProb -= 15;
            }

            return (int) max(0, min(100, $baseProb));
        });
    }

    /**
     * Sugere a próxima ação para o vendedor baseado no contexto do Lead.
     */
    public function suggestNextAction(Lead $lead): string
    {
        if ($lead->status === 'Perdido') {
            return 'Nenhuma ação necessária.';
        }

        if ($lead->status === 'Fechado' || in_array($lead->status, ['Implantação', 'Treinamento', 'Cliente Ativo'])) {
            return 'Lead convertido. Acompanhar métricas de retenção.';
        }

        $daysSinceUpdate = $lead->updated_at ? $lead->updated_at->diffInDays(now()) : 0;

        if ($daysSinceUpdate > 7) {
            return 'Lead esfriando (sem atualização há mais de 7 dias). Sugestão: Enviar mensagem de reactivação e verificar interesse.';
        }

        return match ($lead->status) {
            'Lead' => 'Entrar em contato imediatamente para entender o contexto inicial.',
            'Contato' => 'Agendar uma call de qualificação (15 minutos).',
            'Qualificado' => 'Agendar o Diagnóstico. Coletar dados de alunos perdidos e inadimplência.',
            'Diagnóstico' => 'Apresentar a demonstração prática focada nas dores descobertas.',
            'Demonstração' => 'Enviar a proposta comercial personalizada.',
            'Proposta' => 'Fazer follow-up para sanar dúvidas da proposta.',
            'Negociação' => 'Negociar fechamento e quebrar objeções ("É caro", "Dá trabalho").',
            default => 'Acompanhar o cliente de perto.',
        };
    }

    /**
     * Gera um resumo do histórico de interações (Mock de NLP).
     */
    public function summarizeInteractions(Lead $lead): string
    {
        $interactions = $lead->interactions()->orderBy('data_contato', 'desc')->take(5)->get();
        
        if ($interactions->isEmpty()) {
            return "Nenhuma interação registrada ainda.";
        }

        $summary = "Últimos contatos indicam: ";
        foreach ($interactions as $index => $int) {
            $summary .= strtolower(substr($int->tipo_contato, 0, 10)) . " (" . $int->data_contato->format('d/m') . "), ";
        }

        return rtrim($summary, ', ') . ". Requer acompanhamento próximo.";
    }
}
