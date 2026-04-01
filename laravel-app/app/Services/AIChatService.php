<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AIChatService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    private string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = (string) env('OPENAI_API_KEY', '');
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
     * Cria o prompt do sistema para contextualizar a IA
     */
    private function buildSystemPrompt(array $userMetrics): string
    {
        $metricsContext = '';
        
        if (!empty($userMetrics)) {
            $metricsContext = "Contexto do usuário:\n";
            if (isset($userMetrics['daily_calories'])) {
                $metricsContext .= "- Meta de calorias: {$userMetrics['daily_calories']} kcal\n";
            }
            if (isset($userMetrics['consumed_calories'])) {
                $metricsContext .= "- Calorias consumidas hoje: {$userMetrics['consumed_calories']} kcal\n";
            }
            if (isset($userMetrics['protein_goal'])) {
                $metricsContext .= "- Meta de proteína: {$userMetrics['protein_goal']}g\n";
            }
            if (isset($userMetrics['current_weight'])) {
                $metricsContext .= "- Peso atual: {$userMetrics['current_weight']}kg\n";
            }
            if (isset($userMetrics['goal_weight'])) {
                $metricsContext .= "- Peso objetivo: {$userMetrics['goal_weight']}kg\n";
            }
            if (isset($userMetrics['objective'])) {
                $metricsContext .= "- Objetivo: {$userMetrics['objective']}\n";
            }
        }

        return "Você é um assistente de nutrição especializado e amigável. "
            . "Você ajuda usuários com dúvidas sobre alimentos, macros, calorias e saúde.\n\n"
            . "Regras importantes:\n"
            . "- Seja sempre amigável e encorajador\n"
            . "- Forneça informações precisas sobre nutrição\n"
            . "- Se não souber algo com certeza, seja honesto e sugira consultar um nutricionista\n"
            . "- Responda de forma concisa e prática\n"
            . "- Use emojis apropriados quando fizer sentido\n"
            . "- Sempre que possível, dê dicas práticas e acionáveis\n"
            . "- Personalize respostas baseado no contexto do usuário\n\n"
            . $metricsContext;
    }
}
