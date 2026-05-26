<?php

namespace App\Services\Workout;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;

class OpenAIWorkoutParserService
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    /**
     * Converte o texto OCR em um JSON estruturado de exercícios.
     * 
     * @param User $user Usuário que está realizando a importação.
     * @param string $ocrText Texto extraído pelo OCR.
     * @return array Array de exercícios estruturados.
     */
    public function parse(User $user, string $ocrText): array
    {
        $prompt = <<<PROMPT
Analise o texto OCR de uma ficha de treino e retorne exclusivamente um JSON contendo uma lista de objetos com os seguintes campos:
- nome_exercicio (string)
- series (integer)
- repeticoes (integer ou string, ex: "10-12" ou 10)
- carga (float ou string, ex: "40kg" ou 40)
- observacoes (string ou null)

Se o texto estiver confuso, tente inferir os exercícios baseando-se em termos comuns de musculação.
Retorne APENAS o JSON, sem explicações ou blocos de código markdown.

Texto OCR:
{$ocrText}
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => 'Você é um especialista em musculação e análise de fichas de treino.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        $result = $this->aiProvider->call($user, $messages, 'workout_photo_parser', 'fast', [
            'temperature' => 0.1, // Baixa temperatura para extração estruturada
        ]);

        if (!$result['ok']) {
            throw new Exception("Falha na interpretação da IA: " . ($result['error'] ?? 'Erro desconhecido'));
        }

        $jsonContent = $this->cleanJson($result['message']);
        $decoded = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Falha ao decodificar JSON da IA: " . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Limpa o conteúdo retornado para garantir que seja um JSON válido.
     */
    private function cleanJson(string $content): string
    {
        // Remove blocos de código markdown se existirem
        $content = preg_replace('/```json\s?/', '', $content);
        $content = preg_replace('/```/', '', $content);
        return trim($content);
    }
}
