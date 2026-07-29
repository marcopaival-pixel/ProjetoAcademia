<?php

namespace App\Http\Controllers\Api\V1\Patient;

use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $professionalId = (int) $request->attributes->get('active_patient_link')->professional_id;

        $assessments = BodyAssessment::query()
            ->where('user_id', $user->id)
            ->where('professional_id', $professionalId)
            ->where('status', 'approved')
            ->latest('assessment_date')
            ->limit(30)
            ->get()
            ->map(fn (BodyAssessment $a) => [
                'id' => $a->id,
                'assessment_date' => optional($a->assessment_date)->toDateString(),
                'weight_kg' => $a->weight_kg,
                'bf_percent' => $a->bf_percent,
                'muscle_percent' => $a->muscle_percent,
            ]);

        return response()->json(['data' => ['assessments' => $assessments]]);
    }
}
