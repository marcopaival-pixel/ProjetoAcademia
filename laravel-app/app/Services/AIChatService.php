<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AIChatService
{
    private string $apiKey;

    private string $apiUrl;

    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.api_key', '');
        $this->apiUrl = (string) config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->model = (string) config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Envia mensagem ao assistente IA nutritivo
     * 
     * @param string $userMessage Mensagem do usuário
     * @param array<string, mixed> $userMetrics Métricas do usuário (calorias, macros, etc)
     * @param array<array{role: string, content: string}> $conversationHistory Histórico da conversa
     * @return array{ok: bool, message?: string, error?: string}
     */
    public function chat(string $userMessage, array $userMetrics = [], array $conversationHistory = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'ok' => false,
                'error' => 'Chave OpenAI não configurada',
            ];
        }

        try {
            // Constrói o contexto do sistema
            $systemPrompt = $this->buildSystemPrompt($userMetrics);

            // Adiciona a mensagem do usuário ao histórico
            $messages = $conversationHistory;
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage,
            ];

            // Faz a requisição à API OpenAI
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        ...$messages,
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                return [
                    'ok' => false,
                    'error' => $error['error']['message'] ?? 'Erro na API OpenAI',
                ];
            }

            $data = $response->json();
            $assistantMessage = $data['choices'][0]['message']['content'] ?? '';

            return [
                'ok' => true,
                'message' => $assistantMessage,
            ];
        } catch (Exception $e) {
            return [
                'ok' => false,
                'error' => 'Erro ao comunicar com IA: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cria o prompt do sistema para contextualizar a IA (NexBot High-Performance Coach)
     */
    private function buildSystemPrompt(array $userMetrics): string
    {
        $metricsContext = '';
         
        if (!empty($userMetrics)) {
            $metricsContext = "### DADOS REAIS DO USUÁRIO (Sincronizados em tempo real):\n";
            $metricsContext .= "- Nome: " . ($userMetrics['name'] ?? 'Atleta') . "\n";
            $metricsContext .= "- Objetivo: " . ($userMetrics['objective'] ?? 'Geral') . "\n";
            $metricsContext .= "- Sexo Bio: " . ($userMetrics['biological_sex'] ?? 'N/A') . "\n";
             
            if (isset($userMetrics['daily_calories_target'])) {
                $metricsContext .= "- Meta Calórica: {$userMetrics['daily_calories_target']} kcal\n";
                $metricsContext .= "- Consumo Hoje: " . ($userMetrics['consumed_calories_today'] ?? 0) . " kcal\n";
            }
            if (isset($userMetrics['protein_target'])) {
                $metricsContext .= "- Meta de Proteína: {$userMetrics['protein_target']}g\n";
            }
            if (isset($userMetrics['water_target_ml'])) {
                $metricsContext .= "- Meta de Água: {$userMetrics['water_target_ml']}ml\n";
                $metricsContext .= "- Água Ingerida: " . ($userMetrics['water_consumed_ml'] ?? 0) . "ml\n";
            }
            if (isset($userMetrics['last_workout_name'])) {
                $metricsContext .= "- Último Treino: {$userMetrics['last_workout_name']} ({$userMetrics['last_workout_date']})\n";
            }
        }

        return "Você é o NexBot, um Coach de Alta Performance e Especialista em Biohacking. "
            . "Sua função é fornecer respostas extremamente técnicas, porém motivadoras e acionáveis.\n\n"
            . "DIRETRIZES DE PENSAMENTO:\n"
            . "1. Analise proativamente os dados de consumo de calorias e água. Se o usuário estiver longe da meta, dê um 'puxão de orelha' motivador.\n"
            . "2. Ao falar de treinos, relacione com o último treino registrado se fizer sentido.\n"
            . "3. Use um tom de voz 'Premium': educado, autoritário no conhecimento científico e focado em resultados.\n"
            . "4. Formate suas respostas usando Markdown: use Negrito para termos importantes e Tabelas ou Listas para planos alimentares ou sugestões.\n"
            . "5. Seja conciso. Não perca tempo com 'Espero que esteja bem'. Vá direto ao ponto com valor.\n\n"
            . "CONVENÇÕES:\n"
            . "- Use emojis de performance: ⚡ 🔥 🧪 🧬 🏆 🥗\n"
            . "- Sempre encerre com uma pergunta provocativa ou uma 'Missão do Dia' rápida.\n\n"
            . "### CONTEXTO DO USUÁRIO ATUAL:\n"
            . $metricsContext;
    }
}
