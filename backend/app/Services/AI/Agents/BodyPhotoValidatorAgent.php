<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class BodyPhotoValidatorAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'body_photo_validator';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $imageContent = $this->resolveImageContent($context);

            if (!$imageContent) {
                return [
                    'ok' => false,
                    'error' => 'Nenhuma imagem fornecida para o Body Photo Validator Agent.',
                ];
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => File::get(base_path('../ai-agents/body-photo-validator-agent.md')),
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $message ?: 'Valide esta imagem para evolucao corporal.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageContent,
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'fast',
                context: array_merge($context, [
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1,
                    'max_tokens' => 900,
                ])
            );

            if ($response['ok']) {
                $response['validation_data'] = json_decode($response['message'], true);
            }

            return $response;
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

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
}
