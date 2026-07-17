<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AIOrchestratorLog;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIProviderService
{
    private string $apiKey;
    private string $apiUrl;
    private array $models;

    public function __construct(
        private AiGovernanceRegistry $governance
    )
    {
        $this->apiKey = (string) config('services.openai.api_key', '');
        $this->apiUrl = (string) config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->models = [
            'main' => config('services.openai.model_main', 'gpt-4o'),
            'fast' => config('services.openai.model_fast', 'gpt-4o-mini'),
            'workout_import' => config('services.openai.model_workout_import', config('services.openai.model_main', 'gpt-5.6')),
        ];
    }

    /**
     * Envia um prompt para a OpenAI e registra a execução.
     */
    public function call(User $user, array $messages, string $agentName, string $modelType = 'main', array $context = []): array
    {
        $startTime = microtime(true);
        $modelName = $context['model_override'] ?? ($this->models[$modelType] ?? $this->models['main']);
        $clinicId = $context['clinic_id'] ?? $user->clinic_id ?? null;

        try {
            $this->governance->assertAgent($agentName);
            $agentRegistry = $this->governance->agentFor($agentName);
            if ($agentRegistry) {
                $context = array_merge([
                    'agent_key' => $agentRegistry->agent_key,
                    'prompt_version' => $agentRegistry->prompt_version,
                    'schema_version' => $agentRegistry->schema_version,
                    'reasoning_effort' => $agentRegistry->reasoning_effort,
                    'requires_audit' => $agentRegistry->requires_audit,
                    'auditor_agent_key' => $agentRegistry->auditor_agent_key,
                ], $context);
            }

            if (empty($this->apiKey)) {
                throw new Exception("OpenAI API Key não configurada.");
            }

            $payload = array_filter([
                'model' => $modelName,
                'messages' => $messages,
                'temperature' => $context['temperature'] ?? 0.7,
                'max_tokens' => $context['max_tokens'] ?? 2000,
                'response_format' => $context['response_format'] ?? null,
                'seed' => $context['seed'] ?? null,
                'store' => $context['store'] ?? null,
            ], fn ($value) => $value !== null);

            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post($this->apiUrl, $payload);

            $executionTime = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $errorMsg = $response->json()['error']['message'] ?? $response->body();
                $this->logExecution($user, $clinicId, $agentName, $modelName, 0, 0, 0, 0, $executionTime, 'error', $errorMsg, $context);
                throw new Exception("Erro na API OpenAI: " . $errorMsg);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            $usage = $data['usage'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0];

            $inputTokens = $usage['prompt_tokens'];
            $outputTokens = $usage['completion_tokens'];
            $totalTokens = $usage['total_tokens'];
            $cost = $this->calculateCost($modelName, $inputTokens, $outputTokens);

            $this->logExecution(
                $user, 
                $clinicId, 
                $agentName, 
                $modelName, 
                $inputTokens, 
                $outputTokens, 
                $totalTokens, 
                $cost, 
                $executionTime, 
                'success',
                null,
                $context,
                $messages,
                $content
            );

            return [
                'ok' => true,
                'message' => $content,
                'tokens' => $totalTokens,
                'cost' => $cost,
                'model' => $modelName,
                'execution_time_ms' => $executionTime
            ];

        } catch (Exception $e) {
            Log::error("AIProviderService Exception: " . $e->getMessage());
            
            // Log de erro se ainda não foi logado
            if (!isset($executionTime)) {
                $executionTime = (int) ((microtime(true) - $startTime) * 1000);
                $this->logExecution($user, $clinicId, $agentName, $modelName, 0, 0, 0, 0, $executionTime, 'error', $e->getMessage(), $context);
            }

            return [
                'ok' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function structuredCall(User $user, array $messages, string $agentName, string $modelName, string $schemaName, array $schema, array $context = []): array
    {
        return $this->call(
            user: $user,
            messages: $messages,
            agentName: $agentName,
            modelType: 'main',
            context: array_merge($context, [
                'model_override' => $modelName,
                'temperature' => $context['temperature'] ?? 0.2,
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => $schemaName,
                        'schema' => $schema,
                        'strict' => true,
                    ],
                ],
            ])
        );
    }

    public function generateResponse(string $systemPrompt, string $userMessage, array $options = []): array
    {
        $user = User::find((int) ($options['user_id'] ?? 0));

        if (! $user) {
            return ['ok' => false, 'error' => 'Usuario nao encontrado para chamada de IA governada.'];
        }

        return $this->call(
            user: $user,
            messages: [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            agentName: (string) ($options['agent_name'] ?? 'support'),
            modelType: (string) ($options['model'] ?? 'main'),
            context: $options
        );
    }

    /**
     * Registra a execução no banco de dados.
     */
    private function logExecution(
        User $user, 
        $clinicId, 
        string $agentName, 
        string $modelName, 
        int $input, 
        int $output, 
        int $total, 
        float $cost, 
        int $time, 
        string $status,
        $errorMessage = null,
        array $context = [],
        array $messages = [],
        $responseContent = null
    ): void {
        try {
            AIOrchestratorLog::create([
                'user_id' => $user->id,
                'clinic_id' => $clinicId,
                'agent_name' => $agentName,
                'model_name' => $modelName,
                'user_message' => (function () use ($messages) {
                    $lastUser = collect($messages)->where('role', 'user')->last();

                    return is_array($lastUser) ? (string) ($lastUser['content'] ?? 'N/A') : 'N/A';
                })(),
                'ai_response' => $responseContent,
                'input_tokens' => $input,
                'output_tokens' => $output,
                'total_tokens' => $total,
                'cost_usd' => $cost,
                'execution_time_ms' => $time,
                'status' => $status,
                'context' => $context,
                'error_message' => $errorMessage
            ]);
        } catch (Exception $e) {
            Log::error("Falha ao salvar log do Orchestrator: " . $e->getMessage());
        }
    }

    /**
     * Calcula o custo aproximado da chamada.
     */
    private function calculateCost(string $model, int $input, int $output): float
    {
        // Preços por 1M tokens (Maio 2024 - GPT-4o e GPT-4o-mini)
        $prices = [
            'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
        ];

        // Normalização simplificada do nome do modelo para encontrar o preço
        $priceKey = 'gpt-4o-mini'; // Default barato
        foreach (array_keys($prices) as $key) {
            if (str_contains(strtolower($model), $key)) {
                $priceKey = $key;
                break;
            }
        }

        $p = $prices[$priceKey];
        $inputCost = ($input / 1000000) * $p['input'];
        $outputCost = ($output / 1000000) * $p['output'];

        return (float) ($inputCost + $outputCost);
    }
}
