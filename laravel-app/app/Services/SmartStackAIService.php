<?php

namespace App\Services;

use App\Models\User;
use App\Models\SmartStack;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmartStackAIService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.api_key', '');
        $this->apiUrl = (string) config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->model = (string) config('services.openai.model', 'gpt-4o');
    }

    /**
     * Sugere um Smart Stack baseado nos dados do usuário.
     */
    public function suggestStack(User $user, string $goal = null): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'IA não configurada'];
        }

        $context = $this->getUserContext($user, $goal);

        try {
            $systemPrompt = "Você é o NexShape AI - Especialista em Suplementação e Biohacking. "
                . "Sua tarefa é sugerir um 'Smart Stack' de suplementos baseado nos dados do usuário. "
                . "Retorne APENAS um objeto JSON válido com a seguinte estrutura: "
                . "{ 'stack_name': string, 'goal': string, 'supplements': [ { 'name': string, 'dosage': string, 'unit': string, 'frequency': string, 'time_of_day': string, 'goal': string, 'observations': string } ] }";

            $response = Http::withToken($this->apiKey)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => "Gere um stack para: " . json_encode($context)],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (!$response->successful()) {
                throw new Exception('Erro na API OpenAI');
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '{}';
            $data = json_decode($content, true);

            return ['success' => true, 'suggestion' => $data];
        } catch (Exception $e) {
            Log::error('SmartStackAI Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Analisa um stack existente em busca de alertas ou melhorias.
     */
    public function analyzeStack(SmartStack $stack): array
    {
        // Lógica similar para análise e alertas
        return ['alerts' => []];
    }

    private function getUserContext(User $user, string $goal = null): array
    {
        $user->load(['profile', 'assessments']);
        $profile = $user->profile;
        $latestAss = $user->assessments()->orderBy('assessment_date', 'desc')->first();

        return [
            'age' => $profile ? \Carbon\Carbon::parse($profile->birth_date)->age : 'Desconhecida',
            'weight' => $latestAss ? $latestAss->weight_kg : ($profile->weight_kg ?? 'N/A'),
            'sex' => $profile ? ($profile->sex === 'M' ? 'Masculino' : 'Feminino') : 'N/A',
            'goal' => $goal ?? ($profile->goal ?? 'Saúde Geral'),
            'activity_level' => $profile->activity_level ?? 'Moderado',
        ];
    }
}
