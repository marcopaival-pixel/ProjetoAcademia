<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ExerciseCatalog;
use App\Models\ExerciseSet;
use App\Models\EvolutionPhoto;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\WorkoutTargetArea;
use App\Services\BodyPhotoValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', TrainingPlan::class);

        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $query = TrainingPlan::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('creator_id', $user->id);
            })
            ->withCount('exercises')
            ->latest();

        if (! $isPremium) {
            $query->limit(3);
        }

        $plans = $query->get();

        return response()->json([
            'data' => $plans->map(fn (TrainingPlan $plan) => $this->planSummary($plan)),
            'meta' => [
                'is_premium' => $isPremium,
                'count' => $plans->count(),
                'has_professional_link' => $this->hasProfessionalLink($user),
                'can_create_own_workout' => $isPremium || $user->hasFeature('create_workout'),
            ],
        ]);
    }

    public function show(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('view', $trainingPlan);

        $user = $request->user();
        if ($user->isResourceOverLimit('workouts', $trainingPlan->id)) {
            return response()->json([
                'message' => 'Plano bloqueado pelo limite do plano atual.',
            ], 403);
        }

        $trainingPlan->load(['exercises.catalogExercise', 'exercises.sets']);

        return response()->json([
            'data' => $this->planDetail($trainingPlan),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', TrainingPlan::class);

        $user = $request->user();
        if (! $user->hasFeature('create_workout')) {
            return response()->json(['message' => 'Seu plano atual não permite a criação de planilhas de treino.'], 403);
        }

        $this->normalizeMultipartPayload($request);
        $validated = $this->validatePlanPayload($request);
        $targetPhotos = $this->validateAndStoreTargetPhotos($request);

        $plan = DB::transaction(function () use ($user, $validated, $targetPhotos): TrainingPlan {
            $plan = TrainingPlan::create($this->planAttributes($user->id, $validated));
            $this->syncExercises($plan, $validated['exercises']);
            $this->syncTargetAreas($user->id, $plan, $validated['target_areas'] ?? [], $targetPhotos);

            return $plan;
        });

        $plan->loadCount('exercises');

        return response()->json(['data' => $this->planSummary($plan)], 201);
    }

    public function update(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('update', $trainingPlan);

        if ($request->user()->isResourceOverLimit('workouts', $trainingPlan->id)) {
            return response()->json(['message' => 'Plano bloqueado pelo limite do plano atual.'], 403);
        }

        $validated = $this->validatePlanPayload($request);

        DB::transaction(function () use ($request, $trainingPlan, $validated): void {
            $trainingPlan->update($this->planAttributes($request->user()->id, $validated, false));
            $trainingPlan->exercises()->delete();
            $this->syncExercises($trainingPlan, $validated['exercises']);
        });

        $trainingPlan->loadCount('exercises');

        return response()->json(['data' => $this->planSummary($trainingPlan)]);
    }

    public function destroy(Request $request, TrainingPlan $trainingPlan): JsonResponse
    {
        $this->authorize('delete', $trainingPlan);

        if ($request->user()->isResourceOverLimit('workouts', $trainingPlan->id)) {
            return response()->json(['message' => 'Plano bloqueado pelo limite do plano atual.'], 403);
        }

        $trainingPlan->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    public function exerciseCatalog(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $exercises = ExerciseCatalog::query()
            ->with('muscles')
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('muscle_group', 'like', "%{$search}%")
                        ->orWhere('equipment', 'like', "%{$search}%");
                });
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => [
                'exercises' => $exercises->map(fn (ExerciseCatalog $exercise): array => [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'muscle_group' => $exercise->muscle_group,
                    'equipment' => $exercise->equipment,
                    'difficulty' => $exercise->difficulty,
                    'muscles' => $exercise->muscles->pluck('name')->values()->all(),
                ])->values()->all(),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function planSummary(TrainingPlan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'plan_label' => $plan->plan_label,
            'goal' => $plan->goal,
            'status' => $plan->status,
            'is_active' => (bool) $plan->is_active,
            'exercises_count' => $plan->exercises_count ?? $plan->exercises()->count(),
            'created_at' => $plan->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function planDetail(TrainingPlan $plan): array
    {
        return array_merge($this->planSummary($plan), [
            'description' => $plan->description,
            'frequency' => $plan->frequency,
            'difficulty' => $plan->difficulty,
            'days_of_week' => $plan->days_of_week,
            'exercises' => $plan->exercises->map(function (TrainingPlanExercise $exercise): array {
                /** @var ExerciseCatalog|null $catalog */
                $catalog = $exercise->catalogExercise;

                return [
                    'id' => $exercise->id,
                    'exercise_id' => $catalog?->id,
                    'position' => $exercise->position,
                    'name' => $exercise->custom_name ?? $catalog?->name,
                    'muscle_group' => $catalog?->muscle_group,
                    'notes' => $exercise->notes,
                    'sets' => $exercise->sets->map(fn (ExerciseSet $set): array => [
                        'id' => $set->id,
                        'set_number' => $set->set_number,
                        'reps_target' => $set->reps_target,
                        'rest_seconds' => $set->rest_seconds,
                        'rpe_target' => $set->rpe_target,
                        'set_type' => $set->set_type,
                    ])->values()->all(),
                ];
            })->values()->all(),
        ]);
    }

    private function hasProfessionalLink($user): bool
    {
        if (method_exists($user, 'professionals')) {
            return $user->professionals()->exists();
        }

        if (method_exists($user, 'professionalLinks')) {
            return $user->professionalLinks()->exists();
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePlanPayload(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'plan_label' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:5000',
            'frequency' => 'nullable|integer|min:1|max:7',
            'difficulty' => 'nullable|string|max:20',
            'estimated_duration' => 'nullable|integer|min:1',
            'student_profile' => 'nullable|string|max:30',
            'split_type' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'string|max:20',
            'total_volume' => 'nullable|numeric|min:0',
            'muscles_worked' => 'nullable|array',
            'muscles_worked.*' => 'string|max:80',
            'target_areas' => 'nullable|array',
            'target_areas.*.name' => 'required_with:target_areas|string|max:100',
            'target_areas.*.id' => 'nullable|integer|exists:muscles,id',
            'is_template' => 'nullable|boolean',
            'exercises' => 'required|array|min:1',
            'exercises.*.id' => 'required|exists:exercises_catalog,id',
            'exercises.*.notes' => 'nullable|string|max:1000',
            'exercises.*.sets' => 'required|array|min:1',
            'exercises.*.sets.*.type' => 'nullable|string|max:20',
            'exercises.*.sets.*.reps' => 'nullable|integer|min:0|max:999',
            'exercises.*.sets.*.weight' => 'nullable|numeric|min:0|max:9999',
            'exercises.*.sets.*.rest' => 'nullable|integer|min:0|max:9999',
            'exercises.*.sets.*.rpe' => 'nullable|integer|min:1|max:10',
            'exercises.*.sets.*.cadence' => 'nullable|string|max:20',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function planAttributes(int $userId, array $validated, bool $creating = true): array
    {
        $attributes = [
            'name' => $validated['name'],
            'plan_label' => $validated['plan_label'] ?? null,
            'goal' => $validated['goal'] ?? null,
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'student_profile' => $validated['student_profile'] ?? null,
            'split_type' => $validated['split_type'] ?? null,
            'status' => $validated['status'] ?? 'Ativo',
            'days_of_week' => $validated['days_of_week'] ?? [],
            'total_volume' => $validated['total_volume'] ?? 0,
            'muscles_worked' => $validated['muscles_worked'] ?? [],
            'is_template' => (bool) ($validated['is_template'] ?? false),
        ];

        if ($creating) {
            $attributes['user_id'] = $userId;
            $attributes['creator_id'] = $userId;
        }

        return $attributes;
    }

    /**
     * @param  array<int, array<string, mixed>>  $exercises
     */
    private function syncExercises(TrainingPlan $plan, array $exercises): void
    {
        foreach ($exercises as $index => $exerciseData) {
            $planExercise = TrainingPlanExercise::create([
                'training_plan_id' => $plan->id,
                'exercise_id' => $exerciseData['id'],
                'position' => $index,
                'notes' => $exerciseData['notes'] ?? null,
            ]);

            foreach ($exerciseData['sets'] as $setIndex => $setData) {
                $planExercise->sets()->create([
                    'set_number' => $setIndex + 1,
                    'set_type' => $setData['type'] ?? 'work',
                    'reps_target' => $setData['reps'] ?? 0,
                    'weight_target' => $setData['weight'] ?? 0,
                    'rest_seconds' => $setData['rest'] ?? 60,
                    'rpe_target' => $setData['rpe'] ?? null,
                    'cadence' => $setData['cadence'] ?? null,
                ]);
            }
        }
    }

    private function normalizeMultipartPayload(Request $request): void
    {
        foreach (['days_of_week', 'muscles_worked', 'target_areas', 'exercises'] as $key) {
            if (! $request->filled($key) || is_array($request->input($key))) {
                continue;
            }

            $decoded = json_decode((string) $request->input($key), true);
            if (is_array($decoded)) {
                $request->merge([$key => $decoded]);
            }
        }

        foreach (['frequency', 'estimated_duration'] as $key) {
            if ($request->filled($key)) {
                $request->merge([$key => (int) $request->input($key)]);
            }
        }

        if ($request->filled('total_volume')) {
            $request->merge(['total_volume' => (float) $request->input('total_volume')]);
        }

        if ($request->filled('is_template')) {
            $request->merge(['is_template' => filter_var($request->input('is_template'), FILTER_VALIDATE_BOOL)]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function validateAndStoreTargetPhotos(Request $request): array
    {
        $request->validate([
            'target_photos' => 'nullable|array',
            'target_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $stored = [];
        $validator = app(BodyPhotoValidationService::class);

        foreach ((array) $request->file('target_photos', []) as $type => $photo) {
            if (! $photo) {
                continue;
            }

            $validation = $validator->validate($request->user(), $photo);
            if (!($validation['approved'] ?? false)) {
                abort(response()->json([
                    'error' => [
                        'message' => implode(' ', $validation['messages'] ?? ['Uma das fotos enviadas nao foi aprovada.']),
                        'validation' => $validation,
                    ],
                ], 422));
            }

            $stored[] = [
                'type' => (string) $type,
                'path' => $photo->store('target_areas', 'public'),
                'validation' => $validation,
            ];
        }

        return $stored;
    }

    /**
     * @param  array<int, array<string, mixed>>  $targetAreas
     * @param  array<int, array<string, mixed>>  $photos
     */
    private function syncTargetAreas(int $userId, TrainingPlan $plan, array $targetAreas, array $photos): void
    {
        if (empty($targetAreas) && empty($photos)) {
            return;
        }

        $referencePhotoPath = $photos[0]['path'] ?? null;
        $referenceValidation = $photos[0]['validation'] ?? null;

        foreach ($photos as $photo) {
            EvolutionPhoto::firstOrCreate(
                [
                    'user_id' => $userId,
                    'photo_path' => $photo['path'],
                ],
                [
                    'type' => $this->normalizeEvolutionPhotoType($photo['type'] ?? 'custom'),
                    'registered_date' => now()->toDateString(),
                    'notes' => json_encode([
                        'source' => 'android_training_plan_target_selection',
                        'training_plan_id' => $plan->id,
                        'original_type' => $photo['type'] ?? 'custom',
                        'validation' => $photo['validation'] ?? null,
                    ], JSON_UNESCAPED_UNICODE),
                ]
            );
        }

        foreach ($targetAreas as $target) {
            WorkoutTargetArea::create([
                'user_id' => $userId,
                'training_plan_id' => $plan->id,
                'target_area' => $target['name'],
                'muscle_id' => $target['id'] ?? null,
                'reference_photo_path' => $referencePhotoPath,
                'photo_validation_status' => $referenceValidation['status'] ?? null,
                'photo_validation_data' => $referenceValidation ? array_merge($referenceValidation, [
                    'photos' => $photos,
                ]) : null,
            ]);
        }
    }

    private function normalizeEvolutionPhotoType(string $type): string
    {
        return match ($type) {
            'front', 'back', 'right_side', 'left_side' => $type,
            default => 'custom',
        };
    }
}
