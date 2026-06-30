<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BodyAssessmentResource;
use App\Models\BodyAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = (int) min(max($request->integer('limit', 20), 1), 100);

        $query = BodyAssessment::query()
            ->where('user_id', $user->id)
            ->orderByDesc('assessment_date');

        if (! $user->hasPremiumAccess()) {
            $query->limit(1);
        } else {
            $query->limit($limit);
        }

        $assessments = $query->get();

        return $this->success([
            'assessments' => BodyAssessmentResource::collection($assessments)->resolve(),
        ], [
            'is_premium' => $user->hasPremiumAccess(),
            'count' => $assessments->count(),
        ]);
    }

    public function show(Request $request, BodyAssessment $assessment): JsonResponse
    {
        $this->authorize('view', $assessment);

        $user = $request->user();
        if (! $user->hasPremiumAccess()) {
            $latestId = BodyAssessment::where('user_id', $user->id)
                ->latest('assessment_date')
                ->latest('id')
                ->value('id');

            if ($assessment->id !== $latestId) {
                return $this->error(
                    'Histórico completo de avaliações é exclusivo Premium.',
                    403,
                    'premium_required'
                );
            }
        }

        return $this->success((new BodyAssessmentResource($assessment))->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $maxAssessments = $user->getPlanLimit('max_assessments');
        if ($maxAssessments > 0) {
            $count = BodyAssessment::where('user_id', $user->id)->count();
            if ($count >= $maxAssessments) {
                return $this->error(
                    "Limite de {$maxAssessments} avaliações do plano atingido.",
                    403,
                    'plan_limit_reached'
                );
            }
        }

        $data = $request->validate([
            'assessment_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric', 'min:20', 'max:500'],
            'bf_percent' => ['nullable', 'numeric', 'min:1', 'max:70'],
            'muscle_percent' => ['nullable', 'numeric', 'min:1', 'max:90'],
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
            'notes' => ['nullable', 'string', 'max:5000'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'integer'],
        ]);

        $data['user_id'] = $user->id;
        $data['status'] = 'approved';
        $data['created_by'] = 'patient';

        $assessment = BodyAssessment::create($data);

        $profile = $user->profile;
        if ($assessment->bf_percent === null && $profile && $profile->height_cm > 0) {
            $calcBf = \App\Services\Nutrition::calculateBodyFatPercent(
                $profile->sex,
                (float) $profile->height_cm,
                (float) $assessment->neck,
                (float) $assessment->waist,
                (float) $assessment->hips
            );

            if ($calcBf !== null) {
                $assessment->update(['bf_percent' => $calcBf]);
            }
        }

        $motor = app(\App\Services\IntelligenceMotorService::class);
        $healthScore = $motor->calculateHealthScore($user);
        $user->update(['health_score' => $healthScore]);

        if (! empty($data['weight_kg'])) {
            \App\Models\WeightEntry::updateOrCreate(
                ['user_id' => $user->id, 'weighed_at' => $data['assessment_date']],
                ['weight_kg' => $data['weight_kg']]
            );

            if ($profile && $profile->is_water_target_auto) {
                $newWaterTarget = \App\Services\Nutrition::calculateWaterTarget(
                    (float) $data['weight_kg'],
                    $profile->birth_date?->toDateString(),
                    $profile->sex,
                    $profile->activity_level,
                    $profile->climate ?? 'moderate'
                );
                $profile->update(['water_target_ml' => $newWaterTarget]);
            }
        }

        return $this->success((new BodyAssessmentResource($assessment->fresh()))->resolve(), status: 201);
    }
}
