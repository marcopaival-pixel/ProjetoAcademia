<?php

namespace App\Http\Controllers;

use App\Models\WorkoutImportLog;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseCatalog;
use App\Services\AI\OrchestratorService;
use App\Services\MonetizationService;
use App\Services\SecureFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class WorkoutPhotoImportController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator,
        private MonetizationService $monetization
    ) {}

    /**
     * Exibe a tela de importação de treino por foto.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verifica acesso premium
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        
        $history = WorkoutImportLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('workouts.import-photo', [
            'access' => $access,
            'history' => $history
        ]);
    }

    /**
     * Processa a foto enviada (OCR + IA).
     */
    public function process(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        
        // Validação extra de segurança para Premium
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        if (!$access['allowed']) {
            return response()->json(['error' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        $log = WorkoutImportLog::create([
            'user_id' => $user->id,
            'status' => 'processing',
        ]);

        try {
            // 1. Upload seguro
            $secureFiles = app(SecureFileService::class);
            $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'workout_imports');
            $log->update(['image_path' => $path]);

            $absolutePath = storage_path('app/private/'.$path);
            if (! file_exists($absolutePath)) {
                $absolutePath = storage_path('app/public/'.$path);
            }

            // 2. Processamento via Orquestrador (Visão + Intenção)
            $result = $this->orchestrator->run($user, 'Importação de ficha de treino por foto.', [
                'image_path' => $absolutePath,
                'intent' => 'workout_sheet',
                'clinic_id' => $user->clinic_id,
                'clinicId' => $user->academy_company_id,
                'feature_code' => 'generate_workout',
            ]);

            if ($result['status'] === 'error') {
                throw new Exception($result['error'] ?? 'Falha no processamento da imagem.');
            }

            $exercises = $result['vision_data']['extracted_data'] ?? [];
            
            if (empty($exercises)) {
                throw new Exception("Não foi possível identificar exercícios na imagem. Certifique-se de que a foto está legível.");
            }

            $log->update([
                'raw_ocr_text' => json_encode($result['vision_data'] ?? []),
                'structured_json' => $exercises,
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'exercises' => $exercises,
                'log_id' => $log->id
            ]);

        } catch (Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Salva o treino revisado pelo usuário.
     */
    public function save(Request $request)
    {
        $request->validate([
            'workout_name' => 'required|string|max:255',
            'exercises' => 'required|array|min:1',
            'exercises.*.nome_exercicio' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            return DB::transaction(function () use ($request, $user) {
                // Cria o plano de treino
                $plan = TrainingPlan::create([
                    'user_id' => $user->id,
                    'creator_id' => $user->id,
                    'name' => $request->workout_name,
                    'created_by_ai' => true,
                    'is_active' => true,
                    'status' => 'active',
                ]);

                foreach ($request->exercises as $index => $exData) {
                    // Busca exercício no catálogo (match aproximado)
                    $catalogEx = ExerciseCatalog::where('name', 'like', '%' . $exData['nome_exercicio'] . '%')->first();

                    $planEx = TrainingPlanExercise::create([
                        'training_plan_id' => $plan->id,
                        'exercise_id' => $catalogEx?->id,
                        'custom_name' => $exData['nome_exercicio'],
                        'position' => $index,
                        'notes' => $exData['observacoes'] ?? null,
                    ]);

                    // Adiciona as séries (sets)
                    $seriesCount = is_numeric($exData['series']) ? (int) $exData['series'] : 3;
                    
                    // Tratamento de repetições (pode ser "10-12" ou número)
                    $reps = $exData['repeticoes'] ?? 12;
                    $weight = $exData['carga'] ?? 0;

                    for ($i = 1; $i <= $seriesCount; $i++) {
                        ExerciseSet::create([
                            'training_plan_exercise_id' => $planEx->id,
                            'set_number' => $i,
                            'reps_target' => $reps,
                            'weight_target' => $weight,
                            'rest_seconds' => 60,
                            'set_type' => 'work',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Treino importado e cadastrado com sucesso!',
                    'redirect' => route('progression.plans.show', $plan->id)
                ]);
            });

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Falha ao salvar treino: ' . $e->getMessage()
            ], 500);
        }
    }
}
