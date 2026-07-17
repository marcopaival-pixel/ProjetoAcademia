<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class WorkoutExtractorAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'workout_extractor';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $imageContent = $this->resolveImageContent($context);
            
            if (!$imageContent) {
                return [
                    'ok' => false,
                    'error' => 'Nenhuma imagem fornecida para o Workout Extractor Agent.'
                ];
            }

            $systemPrompt = File::get(base_path('../ai-agents/workout-extractor-agent.md'))
                . "\n\nINSTRUCOES ADICIONAIS DE ROBUSTEZ:\n"
                . "- Imagens de planilha, tabela ou ficha compacta devem ser lidas linha por linha.\n"
                . "- Se houver nomes de exercicios visiveis, nunca retorne exercises vazio.\n"
                . "- Se colunas pequenas estiverem parcialmente ilegíveis, extraia ao menos os nomes dos exercicios e deixe series, repeticoes, carga e intervalo como null.\n"
                . "- Nao rejeite uma ficha porque nao ha carga, descanso ou dia da semana.\n"
                . "- Em tabelas com multiplos blocos, leia de cima para baixo e da esquerda para direita.\n";

            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $message ?: 'Analise esta imagem e extraia os treinos estruturados.'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageContent
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'workout_import',
                context: array_merge($context, ['response_format' => ['type' => 'json_object']])
            );

            if ($response['ok']) {
                $response['extracted_data'] = json_decode($response['message'], true);

                if ($this->hasNoExercises($response['extracted_data'] ?? null)) {
                    $retry = $this->retryTableExtraction($user, $imageContent, $context);
                    if (($retry['ok'] ?? false) && ! $this->hasNoExercises($retry['extracted_data'] ?? null)) {
                        return $retry;
                    }

                    $response['ok'] = false;
                    $response['error'] = 'A ficha foi reconhecida, mas nenhum exercicio foi extraido. Tente uma foto mais proxima ou recorte somente a tabela do treino.';
                }
            }

            return $response;

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function retryTableExtraction(User $user, string $imageContent, array $context): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'Voce e um extrator OCR visual de fichas de treino em portugues.',
                    'A imagem ja foi validada como ficha de treino. Extraia exercicios visiveis.',
                    'Leia tabelas pequenas, planilhas e listas compactas linha por linha.',
                    'Se enxergar nomes de exercicios, retorne-os mesmo que series/repeticoes/carga estejam ilegiveis.',
                    'Retorne apenas JSON valido no formato: {"day": null, "workout_name": null, "workout_division": null, "exercises": [{"position": 1, "nome_exercicio": "...", "series": null, "repeticoes": null, "carga": null, "intervalo": null, "duracao": null, "tecnica_utilizada": null, "observacoes": null, "confidence_scores": {"nome_exercicio": 0.7, "series": 0.0, "repeticoes": 0.0, "carga": 0.0}}]}',
                ]),
            ],
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => 'Extraia os exercicios visiveis desta ficha. Nao retorne array vazio se houver qualquer nome de exercicio legivel.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageContent]],
                ],
            ],
        ];

        $retry = $this->aiProvider->call(
            user: $user,
            messages: $messages,
            agentName: $this->getName(),
            modelType: 'workout_import',
            context: array_merge($context, [
                'temperature' => 0,
                'max_tokens' => 2500,
                'response_format' => ['type' => 'json_object'],
                'retry_reason' => 'empty_exercises_after_valid_workout',
            ])
        );

        if ($retry['ok'] ?? false) {
            $retry['extracted_data'] = json_decode($retry['message'], true);
        }

        return $retry;
    }

    private function hasNoExercises(mixed $payload): bool
    {
        if (! is_array($payload)) {
            return true;
        }

        $exercises = $payload['exercises'] ?? $payload['exercicios'] ?? null;

        return ! is_array($exercises) || count($exercises) === 0;
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
