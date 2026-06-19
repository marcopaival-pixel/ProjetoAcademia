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

        return $this->success((new BodyAssessmentResource($assessment))->resolve(), status: 201);
    }
}
