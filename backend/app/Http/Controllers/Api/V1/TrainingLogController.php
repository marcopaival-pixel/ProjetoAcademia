<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LoadLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrainingLogController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'training_plan_exercise_id' => ['required', 'integer'],
            'exercise_id' => ['required', 'integer'],
            'log_date' => ['required', 'date'],
            'set_number' => ['required', 'integer', 'min:1'],
            'reps_done' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'to_failure' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $log = LoadLog::create($validated + [
            'user_id' => $request->user()->id,
            'to_failure' => (bool) ($validated['to_failure'] ?? false),
        ]);

        return response()->json([
            'data' => [
                'id' => $log->id,
                'training_plan_exercise_id' => (int) $log->training_plan_exercise_id,
                'exercise_id' => (int) $log->exercise_id,
                'log_date' => optional($log->log_date)->toDateString(),
                'set_number' => (int) $log->set_number,
                'reps_done' => (int) $log->reps_done,
                'weight_kg' => $log->weight_kg ? (float) $log->weight_kg : null,
                'rpe' => $log->rpe ? (int) $log->rpe : null,
                'to_failure' => (bool) $log->to_failure,
                'one_rm' => $log->one_rm ? (float) $log->one_rm : null,
            ],
        ], 201);
    }

    /**
     * Sincronização offline em lote de LoadLogs do Android.
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logs' => ['required', 'array'],
            'logs.*.local_id' => ['nullable', 'string'],
            'logs.*.training_plan_exercise_id' => ['required', 'integer'],
            'logs.*.exercise_id' => ['required', 'integer'],
            'logs.*.log_date' => ['required', 'date'],
            'logs.*.set_number' => ['required', 'integer', 'min:1'],
            'logs.*.reps_done' => ['required', 'integer', 'min:0'],
            'logs.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'logs.*.rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'logs.*.to_failure' => ['nullable', 'boolean'],
            'logs.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $savedLogs = [];
        $syncedIds = [];

        DB::beginTransaction();
        try {
            foreach ($validated['logs'] as $logData) {
                $log = LoadLog::create([
                    'user_id' => $user->id,
                    'training_plan_exercise_id' => $logData['training_plan_exercise_id'],
                    'exercise_id' => $logData['exercise_id'],
                    'log_date' => $logData['log_date'],
                    'set_number' => $logData['set_number'],
                    'reps_done' => $logData['reps_done'],
                    'weight_kg' => $logData['weight_kg'] ?? null,
                    'rpe' => $logData['rpe'] ?? null,
                    'to_failure' => (bool) ($logData['to_failure'] ?? false),
                    'notes' => $logData['notes'] ?? null,
                ]);

                $savedLogs[] = $log;
                
                $syncedIds[] = [
                    'local_id' => $logData['local_id'] ?? null,
                    'remote_id' => $log->id,
                ];
            }
            
            DB::commit();

            return response()->json([
                'message' => count($savedLogs) . ' logs sincronizados com sucesso.',
                'synced' => $syncedIds,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao sincronizar exercise-logs: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno ao sincronizar os logs de treino.'], 500);
        }
    }
}
