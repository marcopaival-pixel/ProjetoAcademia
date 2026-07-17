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
        private MonetizationService $monetization,
        private \App\Services\AI\WorkoutImportOrchestrator $importOrchestrator,
        private \App\Services\AiCreditService $aiCredits
    ) {}

    /**
     * Exibe a tela de importação de treino por foto.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verifica acesso premium
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            $access = ['allowed' => true];
        }

        // Verifica saldo de créditos de IA
        // Garante que Premium com carteira zerada seja inicializado antes de bloquear
        if ($access['allowed'] && !$user->isAdministrator()) {
            $wallet = $this->aiCredits->getWallet($user); // auto-inicializa se necessário
            $hasCredits = $this->aiCredits->hasCredits($user, 'workout_import_photo');
            if (!$hasCredits) {
                $access = [
                    'allowed' => false,
                    'reason'  => 'Créditos de IA insuficientes. Você precisa de 50 créditos para importar um treino.',
                    'action'  => 'popup'
                ];
            }
        }

        $history = WorkoutImportLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Passa dados da carteira IA para a view (badge + popover + tela de sucesso)
        $wallet = $this->aiCredits->getWallet($user);
        $plan = $user->plan;
        $planMonthlyImports = $plan ? (int) floor(($plan->ai_credits ?? 0) / 50) : 0;
        $usedImports = $planMonthlyImports - (int) floor($wallet->monthly_allowance / 50);
        $remainingImports = (int) floor($wallet->balance / 50);
        $renewalDate = $wallet->renewal_date?->format('d/m/Y') ?? '—';
        $renewsInDays = $wallet->renewal_date ? now()->diffInDays($wallet->renewal_date, false) : null;

        return view('workouts.import-photo', [
            'access'            => $access,
            'history'           => $history,
            'wallet'            => $wallet,
            'planMonthlyImports'=> $planMonthlyImports,
            'usedImports'       => max(0, $usedImports),
            'remainingImports'  => max(0, $remainingImports),
            'renewalDate'       => $renewalDate,
            'renewsInDays'      => $renewsInDays,
        ]);
    }

    /**
     * Valida se a foto enviada é uma ficha de treino válida.
     */
    public function validatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            $access = ['allowed' => true];
        }
        if (!$access['allowed']) {
            return response()->json(['error' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        if (!$user->isAdministrator() && !$this->aiCredits->hasCredits($user, 'workout_import_photo')) {
            return response()->json(['error' => 'Você não possui créditos de IA suficientes. Adquira mais créditos para continuar.'], 403);
        }

        try {
            $secureFiles = app(SecureFileService::class);
            $path = $secureFiles->storeSensitiveFile($request->file('photo'), 'workout_imports');

            $absolutePath = storage_path('app/private/'.$path);
            if (! file_exists($absolutePath)) {
                $absolutePath = storage_path('app/public/'.$path);
            }

            $validatorAgent = app(\App\Services\AI\Agents\WorkoutValidatorAgent::class);
            $result = $validatorAgent->execute($user, 'Valide se esta imagem é uma ficha de treino.', [
                'image_path' => $absolutePath,
                'clinic_id' => $user->clinic_id,
                'clinicId' => $user->academy_company_id,
            ]);

            if (!$result['ok']) {
                throw new Exception($result['error'] ?? 'Falha na validação da imagem pela IA.');
            }

            $validation = $result['validation_data'] ?? [];

            return response()->json([
                'success' => true,
                'is_workout' => (bool)($validation['is_workout'] ?? false),
                'confidence' => (float)($validation['confidence'] ?? 0.0),
                'document_type' => (string)($validation['document_type'] ?? 'unknown'),
                'reason' => (string)($validation['reason'] ?? 'Não foi possível determinar a razão.'),
                'detected_elements' => $validation['detected_elements'] ?? [],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $this->cleanErrorMessage($e->getMessage())
            ], 422);
        }
    }

    /**
     * Valida múltiplas fotos em lote.
     */
    public function validatePhotos(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|max:7',
            'photos.*' => 'required|image|max:10240',
        ]);

        $user = Auth::user();
        
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            $access = ['allowed' => true];
        }
        if (!$access['allowed']) {
            return response()->json(['error' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        if (!$user->isAdministrator() && !$this->aiCredits->hasCredits($user, 'workout_import_photo')) {
            return response()->json(['error' => 'Você não possui créditos de IA suficientes. Adquira mais créditos para continuar.'], 403);
        }

        try {
            $secureFiles = app(SecureFileService::class);
            $validatorAgent = app(\App\Services\AI\Agents\WorkoutValidatorAgent::class);
            
            $results = [];
            $overallValid = true;

            foreach ($request->file('photos') as $index => $photoFile) {
                $path = $secureFiles->storeSensitiveFile($photoFile, 'workout_imports');

                $absolutePath = storage_path('app/private/'.$path);
                if (! file_exists($absolutePath)) {
                    $absolutePath = storage_path('app/public/'.$path);
                }

                $result = $validatorAgent->execute($user, 'Valide se esta imagem é uma ficha de treino.', [
                    'image_path' => $absolutePath,
                    'clinic_id' => $user->clinic_id,
                    'clinicId' => $user->academy_company_id,
                ]);

                if (!$result['ok']) {
                    $results[] = [
                        'image_id' => $index + 1,
                        'is_workout' => false,
                        'confidence' => 0.0,
                        'document_type' => 'unknown',
                        'detected_day' => null,
                        'reason' => $result['error'] ?? 'Falha na validação da imagem.',
                        'detected_elements' => [],
                        'image_path' => $path
                    ];
                    $overallValid = false;
                    continue;
                }

                $validation = $result['validation_data'] ?? [];
                $isWorkout = (bool)($validation['is_workout'] ?? false);

                $results[] = [
                    'image_id' => $index + 1,
                    'is_workout' => $isWorkout,
                    'confidence' => (float)($validation['confidence'] ?? 0.0),
                    'document_type' => (string)($validation['document_type'] ?? 'unknown'),
                    'detected_day' => $validation['detected_day'] ?? null,
                    'reason' => (string)($validation['reason'] ?? 'Não foi possível determinar a razão.'),
                    'detected_elements' => $validation['detected_elements'] ?? [],
                    'image_path' => $path
                ];

                if (!$isWorkout) {
                    $overallValid = false;
                }
            }

            return response()->json([
                'success' => true,
                'workout_valid' => $overallValid,
                'images' => $results
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $this->cleanErrorMessage($e->getMessage())
            ], 422);
        }
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
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            $access = ['allowed' => true];
        }
        if (!$access['allowed']) {
            return response()->json(['error' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        if (!$user->isAdministrator() && !$this->aiCredits->hasCredits($user, 'workout_import_photo')) {
            return response()->json(['error' => 'Você não possui créditos de IA suficientes. Adquira mais créditos para continuar.'], 403);
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
                'error' => $this->cleanErrorMessage($e->getMessage())
            ], 422);
        }
    }

    /**
     * Processa múltiplas fotos de treino em lote (OCR + IA).
     */
    public function processPhotos(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:7',
            'images.*.image_path' => 'required|string',
            'images.*.day' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        $access = $this->monetization->checkAccess($user, 'workout_import_photo');
        if ($user->isAdministrator() || $user->hasPremiumAccess()) {
            $access = ['allowed' => true];
        }
        if (!$access['allowed']) {
            return response()->json(['error' => 'Esta funcionalidade está disponível apenas para usuários Premium.'], 403);
        }

        if (!$user->isAdministrator() && !$this->aiCredits->hasCredits($user, 'workout_import_photo')) {
            return response()->json(['error' => 'Você não possui créditos de IA suficientes. Adquira mais créditos para continuar.'], 403);
        }

        $imagePaths = array_column($request->images, 'image_path');

        $log = WorkoutImportLog::create([
            'user_id' => $user->id,
            'image_path' => json_encode($imagePaths),
            'status' => 'processing',
        ]);

        try {
            $imagesContext = [];
            foreach ($request->images as $img) {
                $p = $img['image_path'];
                $absolutePath = storage_path('app/private/'.$p);
                if (!file_exists($absolutePath)) {
                    $absolutePath = storage_path('app/public/'.$p);
                }
                $imagesContext[] = [
                    'path' => $absolutePath,
                    'day' => $img['day'] ?? null
                ];
            }

            // Chamamos o Orquestrador passando as imagens
            $result = $this->orchestrator->run($user, 'Analise as imagens enviadas e extraia os exercícios de cada uma. Para cada exercício extraído, inclua obrigatoriamente a propriedade "day" (com o dia correspondente indicado em cada imagem, ex: "segunda-feira", "terça-feira", etc.) no objeto de cada exercício dentro de "extracted_data".', [
                'images' => $imagesContext,
                'intent' => 'workout_sheet',
                'clinic_id' => $user->clinic_id,
                'clinicId' => $user->academy_company_id,
                'feature_code' => 'generate_workout',
            ]);

            if ($result['status'] === 'error') {
                throw new Exception($result['error'] ?? 'Falha no processamento das imagens.');
            }

            $exercises = $result['vision_data']['extracted_data'] ?? [];
            
            if (empty($exercises)) {
                throw new Exception("Não foi possível identificar exercícios nas imagens. Certifique-se de que as fotos estão legíveis.");
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
                'error' => $this->cleanErrorMessage($e->getMessage())
            ], 422);
        }
    }

    public function save(Request $request)
    {
        $request->validate([
            'workout_name' => 'required|string|max:100',
            'exercises' => 'required|array|min:1',
            'exercises.*.nome_exercicio' => 'required|string|max:120',
            'exercises.*.series' => 'nullable|string|max:2',
            'exercises.*.repeticoes' => 'nullable|string|max:20',
            'exercises.*.carga' => 'nullable|string|max:20',
            'exercises.*.observacoes' => 'nullable|string|max:500',
            'exercises.*.day' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        try {
            return DB::transaction(function () use ($request, $user) {
                // Coleta todos os dias únicos dos exercícios
                $days = [];
                foreach ($request->exercises as $exData) {
                    if (!empty($exData['day'])) {
                        $days[] = strtolower($exData['day']);
                    }
                }
                $days = array_values(array_unique($days));

                // Cria o plano de treino
                $plan = TrainingPlan::create([
                    'user_id' => $user->id,
                    'creator_id' => $user->id,
                    'name' => $request->workout_name,
                    'created_by_ai' => true,
                    'is_active' => true,
                    'status' => 'active',
                    'days_of_week' => !empty($days) ? $days : null,
                ]);

                foreach ($request->exercises as $index => $exData) {
                    // Busca exercício no catálogo (match aproximado)
                    $catalogEx = $this->resolveCatalogExercise($exData['nome_exercicio']);

                    $notes = $exData['observacoes'] ?? null;
                    if (!empty($exData['day'])) {
                        $dayText = "[" . ucfirst($exData['day']) . "]";
                        $notes = $notes ? $dayText . " " . $notes : $dayText;
                    }

                    $planEx = TrainingPlanExercise::create([
                        'training_plan_id' => $plan->id,
                        'exercise_id' => $catalogEx->id,
                        'custom_name' => $exData['nome_exercicio'],
                        'position' => $index,
                        'notes' => $notes,
                    ]);

                    // Adiciona as séries (sets)
                    $seriesCount = is_numeric($exData['series']) ? max(1, min(12, (int) $exData['series'])) : 3;
                    
                    // Tratamento de repetições (pode ser "10-12" ou número)
                    $reps = $this->parseImportedRepsTarget($exData['repeticoes'] ?? null);
                    $weight = $this->parseImportedWeightTarget($exData['carga'] ?? null);

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

                // Consome 1 crédito (50 tokens de IA) do usuário
                $this->aiCredits->consume($user, 'workout_import_photo', [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'exercises_count' => count($request->exercises)
                ]);

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

    /**
     * Inicializa a sessão de importação orquestrada (Web/Android).
     */
    public function orchInitialize(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1|max:7',
            'photos.*' => 'required|image|max:10240',
        ]);

        $user = Auth::user();

        try {
            $log = $this->importOrchestrator->initializeSession($user, $request->file('photos'));
            
            return response()->json([
                'success' => true,
                'uuid' => $log->image_path,
                'status' => $log->status,
                'session' => $log->structured_json
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Inicia/continua a validação individual de imagens.
     */
    public function orchValidate(Request $request, $uuid)
    {
        $log = WorkoutImportLog::where('image_path', $uuid)->firstOrFail();

        try {
            $this->importOrchestrator->runValidation($log);
            $log->refresh();

            return response()->json([
                'success' => true,
                'status' => $log->status,
                'session' => $log->structured_json,
                'error_message' => $log->error_message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Substitui uma imagem inválida detectada por ID.
     */
    public function orchSubstitute(Request $request, $uuid)
    {
        $request->validate([
            'image_id' => 'required|integer',
            'photo' => 'required|image|max:10240',
        ]);

        $log = WorkoutImportLog::where('image_path', $uuid)->firstOrFail();

        try {
            $this->importOrchestrator->substituteImage(
                $log, 
                (int) $request->input('image_id'), 
                $request->file('photo')
            );
            $log->refresh();

            return response()->json([
                'success' => true,
                'status' => $log->status,
                'session' => $log->structured_json
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Executa extração, consolidação e auditoria dos treinos.
     */
    public function orchProcess(Request $request, $uuid)
    {
        $log = WorkoutImportLog::where('image_path', $uuid)->firstOrFail();

        try {
            $this->importOrchestrator->runExtractionAndConsolidation($log);
            $log->refresh();

            return response()->json([
                'success' => true,
                'status' => $log->status,
                'session' => $log->structured_json,
                'error_message' => $log->error_message,
            ]);
        } catch (Exception $e) {
            $rawMessage = $e->getMessage();
            $message = $this->cleanErrorMessage($e->getMessage());
            $isAiServiceError = str_contains(strtolower($rawMessage), 'openai')
                || str_contains(strtolower($rawMessage), 'api key')
                || str_contains(strtolower($rawMessage), 'api_key')
                || str_contains(strtolower($rawMessage), 'quota')
                || str_contains(strtolower($rawMessage), 'billing')
                || str_contains(strtolower($rawMessage), 'token');

            return response()->json([
                'success' => false,
                'status' => $isAiServiceError ? 'AI_SERVICE_ERROR' : 'EXTRACTION_FAILED',
                'error_type' => $isAiServiceError ? 'ai_service' : 'processing',
                'error' => $message,
                'error_message' => $message,
            ], 422);
        }
    }

    /**
     * Retorna o status atual de uma sessão de importação.
     */
    public function orchStatus($uuid)
    {
        $log = WorkoutImportLog::where('image_path', $uuid)->firstOrFail();

        return response()->json([
            'success' => true,
            'status' => $log->status,
            'session' => $log->structured_json,
            'error_message' => $log->error_message
        ]);
    }

    private function cleanErrorMessage(string $message): string
    {
        $lowered = strtolower($message);
        if (str_contains($lowered, 'openai') || 
            str_contains($lowered, 'api key') || 
            str_contains($lowered, 'api_key') ||
            str_contains($lowered, 'quota') || 
            str_contains($lowered, 'exceeded') ||
            str_contains($lowered, 'incorrect api') ||
            str_contains($lowered, 'sk-proj') ||
            str_contains($lowered, 'billing') ||
            str_contains($lowered, 'token')) {
            return 'O serviço de inteligência artificial está temporariamente indisponível. Por favor, tente novamente mais tarde.';
        }
        return $message;
    }

    private function resolveCatalogExercise(string $name): ExerciseCatalog
    {
        $cleanName = trim($name) ?: 'Exercicio importado';

        $catalogEx = ExerciseCatalog::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($cleanName)])
            ->first();

        if ($catalogEx) {
            return $catalogEx;
        }

        $catalogEx = ExerciseCatalog::query()
            ->where('name', 'like', '%' . $cleanName . '%')
            ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$cleanName])
            ->first();

        if ($catalogEx) {
            return $catalogEx;
        }

        return ExerciseCatalog::create([
            'name' => $cleanName,
            'muscle_group' => $this->inferMuscleGroup($cleanName),
            'equipment' => $this->inferEquipment($cleanName),
            'difficulty' => 'beginner',
            'instructions' => 'Exercicio criado automaticamente a partir da importacao por IA. Revise o cadastro quando necessario.',
            'is_active' => true,
        ]);
    }

    private function inferMuscleGroup(string $exerciseName): string
    {
        $name = mb_strtolower($exerciseName);

        return match (true) {
            str_contains($name, 'supino'), str_contains($name, 'peito'), str_contains($name, 'crucifixo'), str_contains($name, 'voador') => 'Peito',
            str_contains($name, 'puxada'), str_contains($name, 'remada'), str_contains($name, 'barra fixa'), str_contains($name, 'dorsal') => 'Costas',
            str_contains($name, 'agachamento'), str_contains($name, 'leg'), str_contains($name, 'cadeira extensora'), str_contains($name, 'quadriceps') => 'Pernas',
            str_contains($name, 'mesa flexora'), str_contains($name, 'stiff'), str_contains($name, 'posterior') => 'Posterior',
            str_contains($name, 'panturrilha') => 'Panturrilha',
            str_contains($name, 'rosca'), str_contains($name, 'biceps') => 'Biceps',
            str_contains($name, 'triceps'), str_contains($name, 'testa'), str_contains($name, 'corda') => 'Triceps',
            str_contains($name, 'desenvolvimento'), str_contains($name, 'elevacao lateral'), str_contains($name, 'ombro') => 'Ombros',
            str_contains($name, 'abdominal'), str_contains($name, 'prancha') => 'Abdomen',
            default => 'Geral',
        };
    }

    private function inferEquipment(string $exerciseName): ?string
    {
        $name = mb_strtolower($exerciseName);

        return match (true) {
            str_contains($name, 'barra') => 'Barra',
            str_contains($name, 'halter') => 'Halteres',
            str_contains($name, 'maquina'), str_contains($name, 'leg'), str_contains($name, 'cadeira'), str_contains($name, 'mesa') => 'Maquina',
            str_contains($name, 'polia'), str_contains($name, 'crossover'), str_contains($name, 'corda') => 'Polia',
            default => null,
        };
    }

    private function parseImportedRepsTarget(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return 12;
        }

        if (is_numeric($value)) {
            return max(1, min(999, (int) $value));
        }

        $text = mb_strtolower((string) $value);

        if (str_contains($text, 'falha') || str_contains($text, 'max')) {
            return null;
        }

        preg_match_all('/\d+/', $text, $matches);
        $numbers = array_map('intval', $matches[0] ?? []);

        if (empty($numbers)) {
            return null;
        }

        $target = max($numbers);

        return max(1, min(999, $target));
    }

    private function parseImportedWeightTarget(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return max(0, min(999999.99, (float) $value));
        }

        $text = mb_strtolower((string) $value);

        if (
            str_contains($text, 'corporal') ||
            str_contains($text, 'livre') ||
            str_contains($text, 'sem carga') ||
            str_contains($text, 'a definir')
        ) {
            return null;
        }

        if (preg_match('/\d+(?:[,.]\d+)?/', $text, $match) !== 1) {
            return null;
        }

        $number = (float) str_replace(',', '.', $match[0]);

        return max(0, min(999999.99, $number));
    }

    public function clearHistory()
    {
        $user = Auth::user();
        WorkoutImportLog::where('user_id', $user->id)->delete();

        if (! request()->expectsJson()) {
            return redirect()
                ->route('progression.plans.import-photo')
                ->with('success', 'Histórico de importações limpo com sucesso!');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Histórico de importações limpo com sucesso!'
        ]);
    }
}
