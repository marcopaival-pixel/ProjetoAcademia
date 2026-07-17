<?php

namespace App\Services;

use App\Models\BodyAnalysis;
use App\Models\User;
use App\Services\AI\Agents\VisionAgent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BodyAnalysisProcessingService
{
    public function __construct(
        private readonly BodyPhotoValidationService $validator,
        private readonly AiCreditService $aiCredits,
        private readonly BodyAnalysisInterpretationService $interpreter,
        private readonly VisionAgent $visionAgent,
    ) {
    }

    public function store(User $user, UploadedFile $photo, string $viewType, ?array $landmarks = null, ?array $metrics = null): array
    {
        $photoValidation = $this->validator->validate($user, $photo);

        if (!($photoValidation['approved'] ?? false)) {
            return [
                'ok' => false,
                'status' => 422,
                'payload' => [
                    'success' => false,
                    'code' => 'photo_rejected',
                    'error' => implode(' ', $photoValidation['messages'] ?? ['A foto enviada nao foi aprovada.']),
                    'validation' => $photoValidation,
                ],
            ];
        }

        if (!$this->aiCredits->hasCredits($user, 'analyze_body_photo')) {
            return $this->creditError();
        }

        $path = $photo->store('body-analyses', 'public');
        $visionResult = $this->generateVisionAnalysis($user, $path, $metrics, $viewType);
        $aiSummary = $this->interpreter->interpret($metrics, $viewType, $visionResult['analysis'] ?? null);

        $analysis = BodyAnalysis::create([
            'user_id' => $user->id,
            'photo_path' => $path,
            'view_type' => $viewType,
            'landmarks' => $landmarks,
            'metrics' => $metrics,
            'ai_summary' => $aiSummary,
            'analysis_version' => $aiSummary['version'] ?? BodyAnalysisInterpretationService::VERSION,
            'vision_model' => $visionResult['model'] ?? null,
            'vision_confidence' => $visionResult['confidence'] ?? null,
            'vision_raw_payload' => $visionResult['raw_payload'] ?? null,
        ]);

        if (!$this->aiCredits->consume($user, 'analyze_body_photo', [
            'view_type' => $viewType,
            'analysis_id' => $analysis->id,
            'validation_status' => $photoValidation['status'] ?? null,
        ])) {
            Storage::disk('public')->delete($path);
            $analysis->delete();

            return $this->creditError();
        }

        return [
            'ok' => true,
            'status' => 200,
            'analysis' => $analysis,
            'summary' => $aiSummary,
        ];
    }

    public function payload(BodyAnalysis $analysis): array
    {
        $summary = $analysis->ai_summary ?? [];

        return [
            'id' => $analysis->id,
            'photo_url' => Storage::url($analysis->photo_path),
            'view_type' => $analysis->view_type,
            'landmarks' => $analysis->landmarks ?? [],
            'metrics' => $this->normalizeMetrics($analysis->metrics ?? []),
            'summary' => $summary['summary'] ?? '',
            'diet' => $summary['diet'] ?? '',
            'workout' => $summary['workout'] ?? '',
            'exercises' => $summary['exercises'] ?? [],
            'attention_points' => $summary['attention_points'] ?? [],
            'limitations' => $summary['limitations'] ?? [],
            'vision_summary' => $summary['vision_summary'] ?? null,
            'vision_model' => $analysis->vision_model,
            'vision_confidence' => $analysis->vision_confidence,
            'analysis_version' => $analysis->analysis_version,
            'created_at' => optional($analysis->created_at)->toISOString(),
        ];
    }

    public function comparePayload(BodyAnalysis $first, BodyAnalysis $second): array
    {
        $definitions = [
            ['key' => 'posture_score', 'label' => 'Postura', 'higher_is_better' => true],
            ['key' => 'asymmetry_shoulders', 'label' => 'Ombros', 'higher_is_better' => false],
            ['key' => 'asymmetry_hips', 'label' => 'Quadril', 'higher_is_better' => false],
            ['key' => 'head_forward_score', 'label' => 'Cabeca anteriorizada', 'higher_is_better' => false],
            ['key' => 'landmark_confidence', 'label' => 'Confianca', 'higher_is_better' => true],
        ];

        $firstMetrics = $this->normalizeMetrics($first->metrics ?? []);
        $secondMetrics = $this->normalizeMetrics($second->metrics ?? []);

        $metrics = array_map(function (array $definition) use ($firstMetrics, $secondMetrics) {
            $key = $definition['key'];
            $firstValue = $firstMetrics[$key] ?? null;
            $secondValue = $secondMetrics[$key] ?? null;
            $diff = is_numeric($firstValue) && is_numeric($secondValue)
                ? round((float) $secondValue - (float) $firstValue, 2)
                : null;

            return [
                'key' => $key,
                'label' => $definition['label'],
                'first' => $firstValue,
                'second' => $secondValue,
                'diff' => $diff,
                'higher_is_better' => $definition['higher_is_better'],
                'status' => $this->metricStatus($diff, $definition['higher_is_better']),
            ];
        }, $definitions);

        return [
            'first' => $this->payload($first),
            'second' => $this->payload($second),
            'metrics' => $metrics,
        ];
    }

    private function generateVisionAnalysis(User $user, string $path, ?array $metrics, string $viewType): ?array
    {
        if (config('services.openai.api_key') === '') {
            return null;
        }

        try {
            $promptPath = base_path('../ai-agents/body-analysis-agent.md');
            $prompt = File::exists($promptPath)
                ? File::get($promptPath)
                : 'Analise esta foto corporal para apoio postural. Retorne JSON com summary, attention_points, limitations, training_notes e confidence.';

            $result = $this->visionAgent->execute($user, $prompt, [
                'image_path' => Storage::disk('public')->path($path),
                'metrics' => $metrics,
                'view_type' => $viewType,
                'temperature' => 0.1,
                'max_tokens' => 900,
            ]);

            if (!($result['ok'] ?? false)) {
                Log::warning('Body analysis vision unavailable', ['error' => $result['error'] ?? null]);
                return null;
            }

            $analysis = $result['structured_data'] ?? json_decode($result['message'] ?? '{}', true) ?: null;
            if (!is_array($analysis)) {
                return null;
            }

            return [
                'analysis' => $analysis,
                'model' => $result['model'] ?? null,
                'confidence' => isset($analysis['confidence']) && is_numeric($analysis['confidence'])
                    ? (float) $analysis['confidence']
                    : null,
                'raw_payload' => [
                    'message' => $result['message'] ?? null,
                    'structured_data' => $result['structured_data'] ?? null,
                    'tokens' => $result['tokens'] ?? null,
                    'cost' => $result['cost'] ?? null,
                    'cached' => $result['cached'] ?? false,
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('Body analysis vision failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function normalizeMetrics(array $metrics): array
    {
        return collect($metrics)
            ->map(fn ($value) => is_numeric($value) ? (float) $value : $value)
            ->all();
    }

    private function metricStatus(?float $diff, bool $higherIsBetter): string
    {
        if ($diff === null || abs($diff) < 0.01) {
            return 'stable';
        }

        $improved = $higherIsBetter ? $diff > 0 : $diff < 0;

        return $improved ? 'improved' : 'worsened';
    }

    private function creditError(): array
    {
        return [
            'ok' => false,
            'status' => 403,
            'payload' => [
                'success' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos insuficientes para realizar a analise corporal.',
            ],
        ];
    }
}
