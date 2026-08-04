<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssessmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $assessments = BodyAssessment::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('assessment_date')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => [
                'assessments' => $assessments->map(fn (BodyAssessment $assessment) => $this->payload($assessment))->values(),
            ],
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $assessments = BodyAssessment::where('user_id', $user->id)->orderBy('assessment_date')->get();
        $first = $assessments->first();
        $last = $assessments->last();
        $heightCm = $user->profile?->height_cm;
        $bmi = ($last?->weight_kg && $heightCm) ? round($last->weight_kg / (($heightCm / 100) ** 2), 1) : null;

        return response()->json([
            'data' => [
                'current_weight_kg' => $last?->weight_kg ? (float) $last->weight_kg : null,
                'initial_weight_kg' => $first?->weight_kg ? (float) $first->weight_kg : null,
                'target_weight_kg' => $user->profile?->target_weight_kg ? (float) $user->profile->target_weight_kg : null,
                'height_cm' => $heightCm ? (int) $heightCm : null,
                'bmi' => $bmi,
                'current_bf_percent' => $last?->bf_percent ? (float) $last->bf_percent : null,
                'current_muscle_percent' => $last?->muscle_percent ? (float) $last->muscle_percent : null,
                'last_assessment_date' => optional($last?->assessment_date)->toDateString(),
                'next_assessment_date' => null,
                'deltas' => [
                    'weight_kg' => ($first?->weight_kg && $last?->weight_kg) ? round($last->weight_kg - $first->weight_kg, 2) : null,
                    'bf_percent' => ($first?->bf_percent && $last?->bf_percent) ? round($last->bf_percent - $first->bf_percent, 2) : null,
                    'muscle_percent' => ($first?->muscle_percent && $last?->muscle_percent) ? round($last->muscle_percent - $first->muscle_percent, 2) : null,
                ],
                'goal_progress_percent' => null,
                'assessments_count' => $assessments->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'assessment_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric'],
            'bf_percent' => ['nullable', 'numeric'],
            'muscle_percent' => ['nullable', 'numeric'],
            'neck' => ['nullable', 'numeric'],
            'chest' => ['nullable', 'numeric'],
            'waist' => ['nullable', 'numeric'],
            'abdomen' => ['nullable', 'numeric'],
            'hips' => ['nullable', 'numeric'],
            'bicep_l' => ['nullable', 'numeric'],
            'bicep_r' => ['nullable', 'numeric'],
            'forearm_l' => ['nullable', 'numeric'],
            'forearm_r' => ['nullable', 'numeric'],
            'thigh_l' => ['nullable', 'numeric'],
            'thigh_r' => ['nullable', 'numeric'],
            'calf_l' => ['nullable', 'numeric'],
            'calf_r' => ['nullable', 'numeric'],
            'blood_pressure' => ['nullable', 'string', 'max:40'],
            'heart_rate' => ['nullable', 'integer', 'min:20', 'max:240'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $assessment = BodyAssessment::create($validated + [
            'user_id' => $request->user()->id,
            'created_by' => 'patient',
            'status' => 'approved',
        ]);

        return response()->json(['data' => $this->payload($assessment)], 201);
    }

    private function payload(BodyAssessment $assessment): array
    {
        return [
            'id' => $assessment->id,
            'assessment_date' => optional($assessment->assessment_date)->toDateString(),
            'weight_kg' => $assessment->weight_kg ? (float) $assessment->weight_kg : null,
            'bf_percent' => $assessment->bf_percent ? (float) $assessment->bf_percent : null,
            'muscle_percent' => $assessment->muscle_percent ? (float) $assessment->muscle_percent : null,
            'neck' => $assessment->neck ? (float) $assessment->neck : null,
            'chest' => $assessment->chest ? (float) $assessment->chest : null,
            'waist' => $assessment->waist ? (float) $assessment->waist : null,
            'abdomen' => $assessment->abdomen ? (float) $assessment->abdomen : null,
            'hips' => $assessment->hips ? (float) $assessment->hips : null,
            'bicep_l' => $assessment->bicep_l ? (float) $assessment->bicep_l : null,
            'bicep_r' => $assessment->bicep_r ? (float) $assessment->bicep_r : null,
            'forearm_l' => $assessment->forearm_l ? (float) $assessment->forearm_l : null,
            'forearm_r' => $assessment->forearm_r ? (float) $assessment->forearm_r : null,
            'thigh_l' => $assessment->thigh_l ? (float) $assessment->thigh_l : null,
            'thigh_r' => $assessment->thigh_r ? (float) $assessment->thigh_r : null,
            'calf_l' => $assessment->calf_l ? (float) $assessment->calf_l : null,
            'calf_r' => $assessment->calf_r ? (float) $assessment->calf_r : null,
            'blood_pressure' => $assessment->blood_pressure,
            'heart_rate' => $assessment->heart_rate ? (int) $assessment->heart_rate : null,
            'notes' => $assessment->notes,
            'status' => $assessment->status,
            'created_at' => optional($assessment->created_at)->toISOString(),
        ];
    }

    public function downloadPdf(Request $request, int $id): StreamedResponse|JsonResponse
    {
        $assessment = BodyAssessment::where('user_id', $request->user()->id)->find($id);

        if (! $assessment) {
            return response()->json(['message' => 'Avaliação não encontrada.'], 404);
        }

        if (empty($assessment->pdf_path)) {
            return response()->json(['message' => 'Nenhum PDF associado a esta avaliação.'], 404);
        }

        $disk = config('filesystems.default', 'local');
        if (! Storage::disk($disk)->exists($assessment->pdf_path)) {
            $diskHistorico = config('pdf.historico_disk', 'local');
            if (Storage::disk($diskHistorico)->exists($assessment->pdf_path)) {
                $disk = $diskHistorico;
            } else {
                return response()->json(['message' => 'Arquivo PDF físico não encontrado no servidor.'], 404);
            }
        }

        $filename = basename($assessment->pdf_path);

        return Storage::disk($disk)->download($assessment->pdf_path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
