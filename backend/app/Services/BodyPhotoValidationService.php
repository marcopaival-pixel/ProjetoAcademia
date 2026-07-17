<?php

namespace App\Services;

use App\Models\User;
use App\Services\AI\Agents\BodyPhotoValidatorAgent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class BodyPhotoValidationService
{
    public function validate(User $user, UploadedFile $photo): array
    {
        $local = $this->localChecks($photo);
        if (!$local['approved']) {
            return $local;
        }

        if (config('services.openai.api_key') === '') {
            if (app()->environment('production')) {
                return $this->blockedAiUnavailable($local);
            }

            return $this->approvedFallback($local);
        }

        try {
            $result = app(BodyPhotoValidatorAgent::class)->execute(
                $user,
                'Valide esta foto de referencia corporal antes de permitir que ela seja salva no historico do usuario.',
                ['image_path' => $photo->getRealPath()]
            );

            if (!($result['ok'] ?? false)) {
                Log::warning('Body photo AI validation unavailable', ['error' => $result['error'] ?? null]);
                return app()->environment('production')
                    ? $this->blockedAiUnavailable($local)
                    : $this->approvedFallback($local);
            }

            return $this->normalizeAiResult($result['structured_data'] ?? [], $local);
        } catch (\Throwable $e) {
            Log::warning('Body photo AI validation failed', ['error' => $e->getMessage()]);
            return app()->environment('production')
                ? $this->blockedAiUnavailable($local)
                : $this->approvedFallback($local);
        }
    }

    private function localChecks(UploadedFile $photo): array
    {
        $messages = [];
        $mime = $photo->getMimeType();

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $messages[] = 'Envie uma imagem JPG, PNG ou WebP.';
        }

        $size = @getimagesize($photo->getRealPath());
        $width = $size[0] ?? 0;
        $height = $size[1] ?? 0;

        if ($width < 640 || $height < 640) {
            $messages[] = 'A imagem está com baixa resolução. Envie uma foto mais nítida.';
        }

        return [
            'approved' => empty($messages),
            'status' => empty($messages) ? 'passed_local_checks' : 'rejected',
            'messages' => $messages,
            'checks' => [
                'is_photo' => empty($messages),
                'quality' => empty($messages) ? 'good' : 'low_resolution',
                'width' => $width,
                'height' => $height,
            ],
            'classification' => [
                'view' => 'unknown',
                'framing' => 'unknown',
            ],
            'confidence' => empty($messages) ? 0.45 : 0,
            'source' => 'local',
        ];
    }

    private function approvedFallback(array $local): array
    {
        return array_merge($local, [
            'approved' => true,
            'status' => 'approved_without_ai',
            'messages' => [],
            'warnings' => ['Validação básica concluída. A validação por IA não está disponível neste ambiente.'],
        ]);
    }

    private function blockedAiUnavailable(array $local): array
    {
        return array_merge($local, [
            'approved' => false,
            'status' => 'ai_validation_unavailable',
            'messages' => ['Validação por IA indisponível. Tente novamente em instantes.'],
            'warnings' => ['Em produção, fotos corporais exigem validação por IA antes do armazenamento.'],
        ]);
    }

    private function normalizeAiResult(array $data, array $local): array
    {
        $payload = $data['extracted_data'] ?? $data;
        $approved = (bool) ($payload['approved'] ?? false);
        $messages = $payload['messages'] ?? $payload['problems'] ?? [];

        if (!is_array($messages)) {
            $messages = [$messages];
        }

        return [
            'approved' => $approved,
            'status' => $approved ? 'approved' : 'rejected',
            'messages' => array_values(array_filter($messages)),
            'checks' => array_merge($local['checks'], $payload['checks'] ?? []),
            'classification' => array_merge($local['classification'], $payload['classification'] ?? []),
            'confidence' => (float) ($payload['confidence'] ?? $data['confidence'] ?? 0),
            'warnings' => $payload['warnings'] ?? $data['warnings'] ?? [],
            'next_suggestions' => $payload['next_suggestions'] ?? [],
            'source' => 'ai',
        ];
    }
}
