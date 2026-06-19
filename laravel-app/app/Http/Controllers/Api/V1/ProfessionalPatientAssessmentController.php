<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BodyAssessmentResource;
use App\Models\BodyAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPatientAssessmentController extends Controller
{
    use FormatsApiResponses;
    use ResolvesProfessionalPatient;

    public function index(Request $request, int $patient): JsonResponse
    {
        $this->linkedPatient($request, $patient);
        $limit = (int) min(max($request->integer('limit', 20), 1), 100);

        $assessments = BodyAssessment::query()
            ->where('user_id', $patient)
            ->orderByDesc('assessment_date')
            ->limit($limit)
            ->get();

        return $this->success([
            'assessments' => BodyAssessmentResource::collection($assessments)->resolve(),
        ], ['count' => $assessments->count()]);
    }

    public function store(Request $request, int $patient): JsonResponse
    {
        $this->linkedPatient($request, $patient);
        $professional = $request->user();

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
            'notes' => ['nullable', 'string', 'max:5000'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'heart_rate' => ['nullable', 'integer'],
        ]);

        $data['user_id'] = $patient;
        $data['professional_id'] = $professional->id;
        $data['status'] = 'approved';
        $data['created_by'] = 'professional';

        $assessment = BodyAssessment::create($data);

        return $this->success((new BodyAssessmentResource($assessment))->resolve(), status: 201);
    }
}
