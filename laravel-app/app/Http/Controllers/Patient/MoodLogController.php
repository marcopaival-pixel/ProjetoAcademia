<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MoodLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoodLogController extends Controller
{
    /**
     * Retorna os últimos registros de humor do paciente autenticado.
     */
    public function index(): JsonResponse
    {
        $logs = MoodLog::where('user_id', Auth::id())
            ->visibleToPatient()
            ->orderByDesc('logged_at')
            ->limit(30)
            ->get(['id', 'logged_at', 'mood_score', 'energy_level', 'sleep_hours', 'stress_level', 'notes']);

        return response()->json(['ok' => true, 'data' => $logs]);
    }

    /**
     * Registra um novo log de humor pelo próprio paciente.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mood_score'    => 'required|integer|min:0|max:10',
            'energy_level'  => 'nullable|integer|min:0|max:10',
            'sleep_hours'   => 'nullable|numeric|min:0|max:24',
            'stress_level'  => 'nullable|integer|min:0|max:10',
            'notes'         => 'nullable|string|max:500',
            'logged_at'     => 'nullable|date|before_or_equal:today',
        ]);

        $logDate = $validated['logged_at'] ?? today()->toDateString();

        // Impede duplicata do mesmo dia
        $existing = MoodLog::where('user_id', Auth::id())
            ->where('logged_at', $logDate)
            ->where('is_confidential', false) // Só verifica registros do próprio paciente
            ->first();

        if ($existing) {
            // Atualiza o registro do dia se já existir
            $existing->update([
                'mood_score'   => $validated['mood_score'],
                'energy_level' => $validated['energy_level'] ?? $existing->energy_level,
                'sleep_hours'  => $validated['sleep_hours'] ?? $existing->sleep_hours,
                'stress_level' => $validated['stress_level'] ?? $existing->stress_level,
                'notes'        => $validated['notes'] ?? $existing->notes,
            ]);

            return response()->json([
                'ok'      => true,
                'message' => 'Registro de humor atualizado.',
                'data'    => $existing->fresh(),
            ]);
        }

        $log = MoodLog::create([
            'user_id'        => Auth::id(),
            'professional_id'=> null,
            'mood_score'     => $validated['mood_score'],
            'energy_level'   => $validated['energy_level'] ?? null,
            'sleep_hours'    => $validated['sleep_hours'] ?? null,
            'stress_level'   => $validated['stress_level'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'is_confidential'=> false, // Registro do próprio paciente — nunca confidencial
            'logged_at'      => $logDate,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Humor registrado com sucesso!',
            'data'    => $log,
        ], 201);
    }

    /**
     * Remove um registro de humor do paciente (somente os próprios, não confidenciais).
     */
    public function destroy(int $id): JsonResponse
    {
        $log = MoodLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('is_confidential', false)
            ->firstOrFail();

        $log->delete();

        return response()->json(['ok' => true, 'message' => 'Registro removido.']);
    }
}
