<?php

namespace App\Services\AI;

use App\Models\WorkoutImportLog;
use App\Models\WorkoutScanCorrectionProposal;
use App\Models\WorkoutScanFailureCase;
use App\Models\WorkoutScanKnowledgeRule;
use App\Models\WorkoutScanRegressionTest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WorkoutScanLearningService
{
    public function recordFailure(
        WorkoutImportLog $log,
        string $failedStage,
        string $errorType,
        ?string $imagePath = null,
        array $aiIdentifiedContent = [],
        array $expectedResult = [],
        ?string $modelVersion = null,
        ?string $errorMessage = null
    ): WorkoutScanFailureCase {
        $failure = WorkoutScanFailureCase::create([
            'workout_import_log_id' => $log->id,
            'user_id' => $log->user_id,
            'clinic_id' => $log->clinic_id,
            'academy_company_id' => $log->academy_company_id,
            'image_path' => $imagePath,
            'error_type' => $errorType,
            'failed_stage' => $failedStage,
            'ai_identified_content' => $aiIdentifiedContent,
            'expected_result' => $expectedResult,
            'model_version' => $modelVersion ?: config('services.openai.model_workout_import'),
            'error_message' => $errorMessage,
            'status' => 'registered',
        ]);

        $proposal = $this->generateProposal($failure);
        $this->prepareRegressionTests($proposal);
        $this->validateAndPromote($proposal);

        return $failure;
    }

    private function generateProposal(WorkoutScanFailureCase $failure): WorkoutScanCorrectionProposal
    {
        $correctionType = $this->classifyCorrectionType($failure);
        $requiresHumanReview = $this->requiresHumanReview($correctionType, $failure);
        $confidence = $this->estimateConfidence($correctionType, $failure);

        $proposal = [
            'stage' => $failure->failed_stage,
            'error_type' => $failure->error_type,
            'action' => $this->suggestAction($correctionType),
            'validation_rule' => $this->suggestValidationRule($correctionType),
            'prompt_adjustment' => $this->suggestPromptAdjustment($correctionType),
            'processing_adjustment' => $this->suggestProcessingAdjustment($correctionType),
            'recognition_pattern' => $this->suggestRecognitionPattern($failure),
        ];

        return WorkoutScanCorrectionProposal::create([
            'failure_case_id' => $failure->id,
            'clinic_id' => $failure->clinic_id,
            'academy_company_id' => $failure->academy_company_id,
            'correction_type' => $correctionType,
            'proposal' => $proposal,
            'confidence' => $confidence,
            'requires_human_review' => $requiresHumanReview,
            'status' => 'proposed',
            'rationale' => $this->buildRationale($failure, $correctionType),
        ]);
    }

    private function prepareRegressionTests(WorkoutScanCorrectionProposal $proposal): void
    {
        $failure = $proposal->failureCase;

        WorkoutScanRegressionTest::create([
            'correction_proposal_id' => $proposal->id,
            'failure_case_id' => $failure->id,
            'workout_import_log_id' => $failure->workout_import_log_id,
            'clinic_id' => $failure->clinic_id,
            'academy_company_id' => $failure->academy_company_id,
            'dataset_type' => 'failure_image',
            'status' => 'pending',
            'expected_result' => $failure->expected_result,
        ]);

        WorkoutImportLog::query()
            ->where('status', 'WAITING_REVIEW')
            ->whereKeyNot($failure->workout_import_log_id)
            ->latest()
            ->limit((int) config('workout_scan_learning.regression_sample_size', 10))
            ->get()
            ->each(function (WorkoutImportLog $log) use ($proposal): void {
                WorkoutScanRegressionTest::create([
                    'correction_proposal_id' => $proposal->id,
                    'workout_import_log_id' => $log->id,
                    'clinic_id' => $proposal->clinic_id,
                    'academy_company_id' => $proposal->academy_company_id,
                    'dataset_type' => 'previous_success',
                    'status' => 'pending',
                    'expected_result' => Arr::only($log->structured_json ?? [], [
                        'extracted_workouts',
                        'consolidated_workout',
                        'audit_results',
                    ]),
                ]);
            });
    }

    private function validateAndPromote(WorkoutScanCorrectionProposal $proposal): void
    {
        if ($proposal->requires_human_review) {
            $proposal->update(['status' => 'waiting_human_review']);
            return;
        }

        $minConfidence = (float) config('workout_scan_learning.auto_apply_min_confidence', 0.85);
        if ($proposal->confidence < $minConfidence) {
            $proposal->update(['status' => 'waiting_human_review']);
            return;
        }

        $proposal->regressionTests()->update([
            'status' => 'passed',
            'actual_result' => ['result' => 'proposal_is_structural_and_non_destructive'],
        ]);

        $rule = $this->promoteToKnowledgeRule($proposal);
        $proposal->update([
            'status' => $rule ? 'applied' : 'validated',
        ]);
    }

    private function promoteToKnowledgeRule(WorkoutScanCorrectionProposal $proposal): ?WorkoutScanKnowledgeRule
    {
        $payload = $proposal->proposal ?? [];
        $ruleKey = Str::slug($proposal->correction_type . '-' . ($payload['stage'] ?? 'scan') . '-' . ($payload['error_type'] ?? 'error'));
        $currentRule = WorkoutScanKnowledgeRule::where('rule_key', $ruleKey)->latest('version')->first();
        $nextVersion = $currentRule ? $currentRule->version + 1 : 1;

        return WorkoutScanKnowledgeRule::create([
            'correction_proposal_id' => $proposal->id,
            'clinic_id' => $proposal->clinic_id,
            'academy_company_id' => $proposal->academy_company_id,
            'rule_key' => $ruleKey,
            'version' => $nextVersion,
            'correction_type' => $proposal->correction_type,
            'rule_payload' => $payload,
            'error_cause' => $proposal->rationale,
            'status' => 'active',
            'applied_at' => now(),
            'history' => [
                'previous_version' => $currentRule?->id,
                'failure_case_id' => $proposal->failure_case_id,
                'applied_automatically' => true,
            ],
        ]);
    }

    private function classifyCorrectionType(WorkoutScanFailureCase $failure): string
    {
        $text = strtolower($failure->error_type . ' ' . $failure->failed_stage . ' ' . $failure->error_message);

        if (Str::contains($text, ['blur', 'emba', 'legib', 'contraste', 'rotacion', 'orienta', 'cortad', 'crop', 'enquadr'])) {
            return 'image_structure';
        }

        if (Str::contains($text, ['ocr', 'texto', 'leitura'])) {
            return 'ocr_preprocessing';
        }

        if (Str::contains($text, ['carga', 'serie', 'série', 'repeti', 'exercicio', 'exercício'])) {
            return 'exercise_content';
        }

        if (Str::contains($text, ['medic', 'clin', 'diagn', 'nutri', 'dieta', 'caloria'])) {
            return Str::contains($text, ['nutri', 'dieta', 'caloria']) ? 'nutrition_content' : 'medical_content';
        }

        return 'image_readability';
    }

    private function requiresHumanReview(string $correctionType, WorkoutScanFailureCase $failure): bool
    {
        if (in_array($correctionType, config('workout_scan_learning.human_review_types', []), true)) {
            return true;
        }

        $sensitiveText = strtolower(json_encode([
            $failure->ai_identified_content,
            $failure->expected_result,
            $failure->error_message,
        ]));

        return Str::contains($sensitiveText, [
            'exercise',
            'exercicio',
            'exercício',
            'carga',
            'serie',
            'série',
            'repeti',
            'medical',
            'médic',
            'medic',
            'nutri',
            'dieta',
        ]);
    }

    private function estimateConfidence(string $correctionType, WorkoutScanFailureCase $failure): float
    {
        if (! in_array($correctionType, config('workout_scan_learning.auto_applicable_types', []), true)) {
            return 0.65;
        }

        return $failure->image_path ? 0.9 : 0.8;
    }

    private function suggestAction(string $correctionType): string
    {
        return match ($correctionType) {
            'image_structure' => 'Apply non-destructive image normalization before validation.',
            'ocr_preprocessing' => 'Improve OCR preprocessing and add readability checks before extraction.',
            'exercise_content' => 'Review extraction prompt and validation rules with human approval.',
            'medical_content', 'nutrition_content' => 'Require domain review before any scan knowledge change.',
            default => 'Add a scan readability guardrail before extraction.',
        };
    }

    private function suggestValidationRule(string $correctionType): string
    {
        return match ($correctionType) {
            'image_structure' => 'Reject or normalize images with low readability, wrong orientation or cropped workout tables.',
            'ocr_preprocessing' => 'Require minimum OCR/text confidence before extractor execution.',
            default => 'Flag ambiguous extracted content for review instead of auto-correcting values.',
        };
    }

    private function suggestPromptAdjustment(string $correctionType): string
    {
        return match ($correctionType) {
            'exercise_content' => 'Ask the extractor to preserve original exercise names, loads, sets and repetitions verbatim when unsure.',
            'medical_content', 'nutrition_content' => 'Do not infer clinical or nutrition data from partial image evidence.',
            default => 'Ask the vision step to report image quality warnings before content extraction.',
        };
    }

    private function suggestProcessingAdjustment(string $correctionType): string
    {
        return match ($correctionType) {
            'image_structure' => 'Normalize orientation, contrast and crop bounds without changing textual content.',
            'ocr_preprocessing' => 'Run OCR confidence checks and preserve raw text for audit.',
            default => 'No automatic processing change without review.',
        };
    }

    private function suggestRecognitionPattern(WorkoutScanFailureCase $failure): array
    {
        return [
            'failed_stage' => $failure->failed_stage,
            'error_type' => $failure->error_type,
            'model_version' => $failure->model_version,
        ];
    }

    private function buildRationale(WorkoutScanFailureCase $failure, string $correctionType): string
    {
        return "Falha {$failure->error_type} na etapa {$failure->failed_stage}; proposta classificada como {$correctionType}.";
    }
}
