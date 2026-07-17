<?php

namespace App\Services\AI\Agents;

use App\Models\User;

abstract class BaseAgent
{
    /**
     * Executa a lógica do agente
     * 
     * @param User $user Usuário que fez a solicitação
     * @param string $message Mensagem do usuário
     * @param array $context Contexto adicional
     * @return array Resposta formatada da IA
     */
    abstract public function execute(User $user, string $message, array $context = []): array;

    /**
     * Retorna o nome identificador do agente
     */
    abstract public function getName(): string;

    protected function conversationMessages(array $context): array
    {
        $history = $context['conversation_history'] ?? [];
        if (! is_array($history)) {
            return [];
        }

        return collect($history)
            ->filter(fn ($message) => in_array($message['role'] ?? null, ['user', 'assistant'], true))
            ->map(fn ($message) => [
                'role' => $message['role'],
                'content' => mb_substr((string) ($message['content'] ?? ''), 0, 1200),
            ])
            ->filter(fn ($message) => $message['content'] !== '')
            ->values()
            ->all();
    }

    protected function metricsPrompt(array $context): string
    {
        $metrics = $context['user_metrics'] ?? [];
        if (! is_array($metrics) || $metrics === []) {
            return '';
        }

        $lines = collect($metrics)
            ->filter(fn ($value) => is_scalar($value) || $value === null)
            ->map(fn ($value, $key) => '- ' . $key . ': ' . ($value ?? 'N/A'))
            ->values()
            ->all();

        return $lines ? "BIO-DATA CONSOLIDADO DO NEXSHAPE:\n" . implode("\n", $lines) : '';
    }

    protected function actionContractPrompt(): string
    {
        return implode("\n", [
            'Quando fizer sentido sugerir uma operacao no sistema, termine a resposta com um bloco JSON entre as marcas [NEXBOT_ACTION] e [/NEXBOT_ACTION].',
            'Use somente uma destas acoes: agendar, cancelar_agendamento, criar_treino, ajustar_treino, criar_dieta, ajustar_dieta.',
            'Formato: {"acao":"criar_treino","dados":{"name":"...","goal":"...","description":"..."}}.',
            'Nunca diga que uma acao foi executada antes da confirmacao do usuario.',
        ]);
    }

    protected function safetyGuardrailsPrompt(): string
    {
        return implode("\n", [
            'GUARDRAILS DE SEGURANCA:',
            '- Nao forneca diagnostico medico, prescricao medicamentosa, anabolizantes ou condutas clinicas de risco.',
            '- Nao recomende dieta extrema, jejum agressivo, restricao radical ou metas absolutas.',
            '- Em dor, lesao, exame alterado, tontura, falta de ar ou sintomas persistentes, oriente avaliacao profissional.',
            '- Em treino, adapte por objetivo, nivel e historico; priorize tecnica, progressao gradual e recuperacao.',
            '- Em nutricao, ofereca orientacao educativa e conservadora, considerando objetivo e consumo registrado.',
        ]);
    }
}
