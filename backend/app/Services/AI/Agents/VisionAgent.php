<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Models\AIVisionLog;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class VisionAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'vision';
    }

    /**
     * Executa a análise de visão
     * O contexto deve conter 'image_path' ou 'image_base64'
     */
    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $isBatch = !empty($context['images']) && is_array($context['images']);
            $imageContent = null;
            $imageHash = null;

            if (!$isBatch) {
                $imageContent = $this->resolveImageContent($context);
                if (!$imageContent) {
                    return [
                        'ok' => false,
                        'error' => 'Nenhuma imagem fornecida para o Vision Agent.'
                    ];
                }

                // 1. Tentar Cache por Hash (MD5)
                $imageHash = md5($imageContent);
                $cached = $this->checkCache($imageHash);
                if ($cached) {
                    return [
                        'ok' => true,
                        'message' => json_encode($cached->extracted_data),
                        'structured_data' => array_merge($cached->extracted_data, ['document_type' => $cached->document_type]),
                        'tokens' => 0,
                        'cost' => 0,
                        'model' => 'cache_hit',
                        'cached' => true
                    ];
                }
            }

            $systemPrompt = File::get(base_path('../ai-agents/vision-agent.md'));

            $contentBlocks = [
                [
                    'type' => 'text',
                    'text' => $message ?: 'Analise esta imagem e extraia os dados conforme suas instruções.'
                ]
            ];

            if ($isBatch) {
                foreach ($context['images'] as $imgData) {
                    $resolved = $this->resolveImageAtPath($imgData['path'] ?? '');
                    if ($resolved) {
                        $dayText = isset($imgData['day']) ? " (Treino do dia: " . $imgData['day'] . ")" : "";
                        $contentBlocks[] = [
                            'type' => 'text',
                            'text' => "--- Nova Ficha/Imagem de treino{$dayText} ---"
                        ];
                        $contentBlocks[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $resolved
                            ]
                        ];
                    }
                }
                // Generate a unified imageHash for batch just as md5 of concatenated paths
                $imageHash = md5(json_encode(array_column($context['images'], 'path')));
            } else {
                $contentBlocks[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => $imageContent
                    ]
                ];
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $contentBlocks
                ]
            ];

            $response = $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'main', // GPT-4o suporta visão
                context: array_merge($context, ['response_format' => ['type' => 'json_object']])
            );

            if ($response['ok']) {
                $response['structured_data'] = json_decode($response['message'], true);
                
                // Registro de Auditoria Granular
                $this->logVisionExecution($user, $response, array_merge($context, ['image_hash' => $imageHash, 'image_path' => $isBatch ? json_encode(array_column($context['images'], 'path')) : ($context['image_path'] ?? null)]));
            }

            return $response;

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Resolve o conteúdo da imagem para o formato esperado pela API (URL ou Base64 data URI)
     */
    private function resolveImageContent(array $context): ?string
    {
        if (!empty($context['image_url'])) {
            return $context['image_url'];
        }

        if (!empty($context['image_base64'])) {
            return $context['image_base64'];
        }

        if (!empty($context['image_path']) && File::exists($context['image_path'])) {
            $type = pathinfo($context['image_path'], PATHINFO_EXTENSION);
            $data = File::get($context['image_path']);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return null;
    }

    /**
     * Registra os detalhes granulares da execução de visão
     */
    private function logVisionExecution(User $user, array $response, array $context): void
    {
        try {
            $data = $response['structured_data'] ?? [];
            
            AIVisionLog::create([
                'user_id' => $user->id,
                'clinic_id' => $context['clinic_id'] ?? $user->clinic_id,
                'academy_company_id' => $context['academy_company_id'] ?? $user->academy_company_id,
                'document_type' => $data['document_type'] ?? 'unknown',
                'confidence' => $data['confidence'] ?? 0,
                'image_path' => $context['image_path'] ?? null,
                'image_hash' => $context['image_hash'] ?? null,
                'extracted_data' => $data['extracted_data'] ?? null,
                'warnings' => $data['warnings'] ?? null,
                'model_name' => $response['model'] ?? 'unknown',
                'total_tokens' => $response['tokens'] ?? 0,
                'cost_usd' => $response['cost'] ?? 0,
                'execution_time_ms' => $response['execution_time_ms'] ?? 0,
            ]);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao logar AIVisionLog: " . $e->getMessage());
        }
    }

    /**
     * Verifica se já processamos esta imagem anteriormente
     */
    private function checkCache(string $hash): ?AIVisionLog
    {
        return AIVisionLog::where('image_hash', $hash)
            ->where('confidence', '>', 0.8) // Só reaproveitar se a confiança foi alta
            ->latest()
            ->first();
    }

    private function resolveImageAtPath(string $path): ?string
    {
        if (File::exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = File::get($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}
