<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ExerciseCatalog;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\WorkoutImportLog;
use App\Services\AI\OrchestratorService;
use App\Services\SecureFileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutImportController extends Controller
{
    public function __construct(private OrchestratorService $orchestrator) {}

    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:10240'],
        ]);

        $user = $request->user();
        if (! $user->isAdministrator() && ! $user->hasPremiumAccess()) {
            return response()->json(['message' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        $log = WorkoutImportLog::create([
            'user_id' => $user->id,
            'status' => 'processing',
        ]);

        try {
            $path = app(SecureFileService::class)->storeSensitiveFile($request->file('photo'), 'workout_imports');
            $log->update(['image_path' => $path]);

            $absolutePath = storage_path('app/private/'.$path);
            if (! file_exists($absolutePath)) {
                $absolutePath = storage_path('app/public/'.$path);
            }

            $result = $this->orchestrator->run($user, 'Importação de ficha de treino por foto.', [
                'image_path' => $absolutePath,
                'intent' => 'workout_sheet',
                'clinic_id' => $user->clinic_id,
                'clinicId' => $user->academy_company_id,
                'feature_code' => 'generate_workout',
            ]);

            if (($result['status'] ?? null) === 'error') {
                throw new Exception($result['error'] ?? 'Falha no processamento da imagem.');
            }

            $exercises = $result['vision_data']['extracted_data'] ?? [];
            if (empty($exercises) || ! is_array($exercises)) {
                throw new Exception('Não foi possível identificar exercícios na imagem. Certifique-se de que a foto está legível.');
            }

            $normalized = collect($exercises)->map(fn ($exercise) => [
                'nome_exercicio' => (string) ($exercise['nome_exercicio'] ?? $exercise['name'] ?? ''),
                'series' => (string) ($exercise['series'] ?? '3'),
                'repeticoes' => (string) ($exercise['repeticoes'] ?? $exercise['reps'] ?? '12'),
                'carga' => (string) ($exercise['carga'] ?? $exercise['weight'] ?? '0'),
                'observacoes' => (string) ($exercise['observacoes'] ?? $exercise['notes'] ?? ''),
            ])->filter(fn ($exercise) => $exercise['nome_exercicio'] !== '')->values();

            $log->update([
                'raw_ocr_text' => json_encode($result['vision_data'] ?? []),
                'structured_json' => $normalized->all(),
                'status' => 'completed',
            ]);

            return response()->json([
                'data' => [
                    'exercises' => $normalized->all(),
                    'log_id' => $log->id,
                ],
            ]);
        } catch (Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workout_name' => ['required', 'string', 'max:255'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.nome_exercicio' => ['required', 'string', 'max:255'],
            'exercises.*.series' => ['nullable'],
            'exercises.*.repeticoes' => ['nullable'],
            'exercises.*.carga' => ['nullable'],
            'exercises.*.observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        if (! $user->isAdministrator() && ! $user->hasPremiumAccess()) {
            return response()->json(['message' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        $plan = DB::transaction(function () use ($user, $validated) {
            $plan = TrainingPlan::create([
                'user_id' => $user->id,
                'creator_id' => $user->id,
                'name' => $validated['workout_name'],
                'created_by_ai' => true,
                'is_active' => true,
                'status' => 'Ativo',
            ]);

            foreach ($validated['exercises'] as $index => $exerciseData) {
                $catalog = ExerciseCatalog::where('name', 'like', '%'.$exerciseData['nome_exercicio'].'%')->first();

                $planExercise = TrainingPlanExercise::create([
                    'training_plan_id' => $plan->id,
                    'exercise_id' => $catalog?->id,
                    'custom_name' => $exerciseData['nome_exercicio'],
                    'position' => $index,
                    'notes' => $exerciseData['observacoes'] ?? null,
                ]);

                $seriesCount = max(1, min(12, (int) ($exerciseData['series'] ?? 3)));
                $reps = (int) preg_replace('/\D+/', '', (string) ($exerciseData['repeticoes'] ?? 12));
                $weight = (float) str_replace(',', '.', preg_replace('/[^\d,\.]/', '', (string) ($exerciseData['carga'] ?? 0)));

                for ($i = 1; $i <= $seriesCount; $i++) {
                    ExerciseSet::create([
                        'training_plan_exercise_id' => $planExercise->id,
                        'set_number' => $i,
                        'reps_target' => $reps ?: 12,
                        'weight_target' => $weight,
                        'rest_seconds' => 60,
                        'set_type' => 'work',
                    ]);
                }
            }

            return $plan;
        });

        return response()->json([
            'data' => [
                'message' => 'Treino importado e cadastrado com sucesso.',
                'plan_id' => $plan->id,
            ],
        ]);
    }
}
