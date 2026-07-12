<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Api\V1\Concerns\FormatsTrainingPlans;
use App\Http\Controllers\Api\V1\Concerns\ResolvesProfessionalPatient;
use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPatientTrainingController extends Controller
{
    use FormatsApiResponses;
    use FormatsTrainingPlans;
    use ResolvesProfessionalPatient;

    public function index(Request $request, int $patient): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        $plans = TrainingPlan::query()
            ->where('user_id', $patient)
            ->withCount('exercises')
            ->latest()
            ->get()
            ->map(fn (TrainingPlan $plan) => $this->planSummary($plan))
            ->values()
            ->all();

        return $this->success(['plans' => $plans], ['count' => count($plans)]);
    }

    public function show(Request $request, int $patient, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->linkedPatient($request, $patient);

        if ((int) $trainingPlan->user_id !== $patient) {
            return $this->error('Plano não pertence a este aluno.', 404, 'not_found');
        }

        $this->authorize('view', $trainingPlan);

        $trainingPlan->load(['exercises.catalogExercise', 'exercises.sets']);

        return $this->success($this->planDetail($trainingPlan));
    }

    public function store(Request $request, int $patient): JsonResponse
    {
        $linked = $this->linkedPatient($request, $patient);
        $professional = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'plan_label' => ['nullable', 'string', 'max:10'],
            'goal' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'frequency' => ['nullable', 'integer', 'min:1', 'max:7'],
            'protocol_id' => ['nullable', 'integer', 'exists:clinic_protocols,id'],
            'difficulty' => ['nullable', 'string', 'max:20'],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'student_profile' => ['nullable', 'string', 'max:30'],
            'split_type' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'string', 'max:20'],
            'days_of_week' => ['nullable', 'array'],
            'total_volume' => ['nullable', 'numeric'],
            'muscles_worked' => ['nullable', 'array'],
            'is_template' => ['nullable', 'boolean'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.id' => ['required_with:exercises', 'integer', 'exists:exercises_catalog,id'],
            'exercises.*.notes' => ['nullable', 'string', 'max:1000'],
            'exercises.*.sets' => ['required_with:exercises', 'array', 'min:1'],
            'exercises.*.sets.*.type' => ['nullable', 'string', 'max:20'],
            'exercises.*.sets.*.reps' => ['nullable', 'integer', 'min:0', 'max:999'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'exercises.*.sets.*.rest' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'exercises.*.sets.*.rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'exercises.*.sets.*.cadence' => ['nullable', 'string', 'max:20'],
        ]);

        if (! empty($validated['protocol_id'])) {
            $this->assertPatientTrainingPlanLimits($linked);
            $plan = $this->applyProtocol($professional, $linked, (int) $validated['protocol_id']);

            return $this->success($this->planSummary($plan->loadCount('exercises')), status: 201);
        }

        $this->assertPatientTrainingPlanLimits($linked);

        $plan = TrainingPlan::create([
            'user_id' => $linked->id,
            'creator_id' => $professional->id,
            'professional_id' => $professional->id,
            'name' => $validated['name'],
            'plan_label' => $validated['plan_label'] ?? null,
            'goal' => $validated['goal'] ?? null,
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'student_profile' => $validated['student_profile'] ?? null,
            'split_type' => $validated['split_type'] ?? null,
            'days_of_week' => $validated['days_of_week'] ?? null,
            'total_volume' => $validated['total_volume'] ?? 0,
            'muscles_worked' => $validated['muscles_worked'] ?? null,
            'is_template' => (bool) ($validated['is_template'] ?? false),
            'status' => $validated['status'] ?? 'Ativo',
            'is_active' => true,
        ]);

        foreach (($validated['exercises'] ?? []) as $index => $exerciseData) {
            $planExercise = TrainingPlanExercise::create([
                'training_plan_id' => $plan->id,
                'exercise_id' => $exerciseData['id'],
                'position' => $index,
                'notes' => $exerciseData['notes'] ?? null,
            ]);

            foreach ($exerciseData['sets'] as $setIndex => $setData) {
                $planExercise->sets()->create([
                    'set_number' => $setIndex + 1,
                    'reps_target' => $setData['reps'] ?? 0,
                    'weight_target' => $setData['weight'] ?? 0,
                    'rest_seconds' => $setData['rest'] ?? 60,
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                    'set_type' => $setData['type'] ?? 'work',
                ]);
            }
        }

        return $this->success($this->planSummary($plan->loadCount('exercises')), status: 201);
    }

    private function applyProtocol(User $professional, User $patient, int $protocolId): TrainingPlan
    {
        $protocol = ClinicProtocol::query()->findOrFail($protocolId);

        if ((int) $protocol->academy_company_id !== (int) $professional->academy_company_id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Acesso negado ao protocolo.');
        }

        return TrainingPlan::create([
            'user_id' => $patient->id,
            'creator_id' => $professional->id,
            'professional_id' => $professional->id,
            'name' => $protocol->name,
            'description' => 'Protocolo aplicado: '.($protocol->description ?? '')."\n\n".($protocol->protocol ?? ''),
            'goal' => $protocol->objective,
            'frequency' => (int) ($protocol->frequency ?? 0) ?: null,
            'estimated_duration' => (int) ($protocol->duration ?? 0) ?: null,
            'status' => 'Ativo',
            'is_active' => true,
            'is_template' => false,
        ]);
    }

    private function assertPatientTrainingPlanLimits(User $patient): void
    {
        $maxWorkouts = $patient->getPlanLimit('max_workouts');
        if ($maxWorkouts > 0) {
            $planCount = TrainingPlan::where('user_id', $patient->id)->count();
            if ($planCount >= $maxWorkouts) {
                throw new \Illuminate\Auth\Access\AuthorizationException(
                    "O aluno atingiu o limite de {$maxWorkouts} planos de treino do plano."
                );
            }
        }
    }
}
