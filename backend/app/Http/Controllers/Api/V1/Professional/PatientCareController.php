<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Api\V1\Professional\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Models\BodyAssessment;
use App\Models\EvolutionPhoto;
use App\Models\TrainingPlan;
use App\Services\BodyPhotoValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientCareController extends Controller
{
    use ResolvesProfessionalPatient;

    public function trainingPlans(Request $request, int $patientId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);
        $professionalId = $request->user()->id;

        $plans = TrainingPlan::query()
            ->where('user_id', $patient->id)
            ->where(function ($q) use ($professionalId) {
                $q->where('creator_id', $professionalId)
                    ->orWhere('professional_id', $professionalId);
            })
            ->withCount('exercises')
            ->latest()
            ->get()
            ->map(fn (TrainingPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'is_active' => (bool) $plan->is_active,
                'exercises_count' => $plan->exercises_count,
            ]);

        return response()->json(['data' => ['plans' => $plans]]);
    }

    public function trainingPlanDetail(Request $request, int $patientId, int $planId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);
        $plan = TrainingPlan::query()
            ->where('user_id', $patient->id)
            ->where('id', $planId)
            ->with(['exercises.catalogExercise', 'exercises.sets'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'is_active' => (bool) $plan->is_active,
                'exercises' => $plan->exercises->map(fn ($ex) => [
                    'id' => $ex->id,
                    'name' => $ex->catalogExercise?->name ?? $ex->exercise_name,
                    'sets' => $ex->sets->map(fn ($set) => [
                        'id' => $set->id,
                        'reps' => $set->reps,
                        'weight_kg' => $set->weight_kg,
                    ])->values(),
                ])->values(),
            ],
        ]);
    }

    public function createTrainingPlan(Request $request, int $patientId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan = TrainingPlan::create([
            'user_id' => $patient->id,
            'creator_id' => $request->user()->id,
            'professional_id' => $request->user()->id,
            'name' => $validated['name'],
            'goal' => $validated['goal'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'data' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'is_active' => (bool) $plan->is_active,
            ],
        ], 201);
    }

    public function assessments(Request $request, int $patientId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);

        $assessments = BodyAssessment::query()
            ->where('user_id', $patient->id)
            ->where('professional_id', $request->user()->id)
            ->latest('assessment_date')
            ->limit(50)
            ->get()
            ->map(fn (BodyAssessment $a) => [
                'id' => $a->id,
                'assessment_date' => optional($a->assessment_date)->toDateString(),
                'weight_kg' => $a->weight_kg,
                'bf_percent' => $a->bf_percent,
                'status' => $a->status,
            ]);

        return response()->json(['data' => ['assessments' => $assessments]]);
    }

    public function storeAssessment(Request $request, int $patientId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);

        $validated = $request->validate([
            'assessment_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric'],
            'bf_percent' => ['nullable', 'numeric'],
            'muscle_percent' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $assessment = BodyAssessment::create($validated + [
            'user_id' => $patient->id,
            'professional_id' => $request->user()->id,
            'created_by' => $request->user()->id,
            'status' => 'approved',
        ]);

        return response()->json(['data' => ['assessment' => $assessment]], 201);
    }

    public function evolutionPhotos(Request $request, int $patientId): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);

        $photos = EvolutionPhoto::where('user_id', $patient->id)
            ->latest('registered_date')
            ->get()
            ->map(fn (EvolutionPhoto $photo) => [
                'id' => $photo->id,
                'type' => $photo->type,
                'registered_date' => optional($photo->registered_date)->toDateString(),
                'weight_kg' => $photo->weight_kg,
            ]);

        return response()->json(['data' => ['photos' => $photos]]);
    }

    public function uploadEvolutionPhoto(Request $request, int $patientId, BodyPhotoValidationService $validator): JsonResponse
    {
        $patient = $this->assertProfessionalPatient($request, $patientId);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'type' => ['required', 'in:front,side,right_side,left_side,back,custom'],
            'registered_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric'],
        ]);

        $validation = $validator->validate($patient, $request->file('photo'));
        if (! ($validation['approved'] ?? false)) {
            return response()->json([
                'message' => implode(' ', $validation['messages'] ?? ['Foto não aprovada.']),
            ], 422);
        }

        $path = $request->file('photo')->store('evolution', 'public');
        $photo = EvolutionPhoto::create([
            'user_id' => $patient->id,
            'photo_path' => $path,
            'type' => $validated['type'],
            'registered_date' => $validated['registered_date'],
            'weight_kg' => $validated['weight_kg'] ?? null,
            'notes' => json_encode(['source' => 'professional_mobile_upload'], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(['data' => ['photo' => $photo]], 201);
    }
}
