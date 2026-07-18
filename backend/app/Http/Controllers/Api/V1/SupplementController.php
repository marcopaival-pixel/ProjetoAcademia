<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplement;
use App\Models\SupplementLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $supplements = Supplement::with('smartStack')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($supplement) {
                return [
                    'id' => $supplement->id,
                    'name' => $supplement->name,
                    'dosage' => $supplement->dosage,
                    'unit' => $supplement->unit,
                    'frequency' => $supplement->frequency,
                    'time_of_day' => $supplement->time_of_day,
                    'duration_days' => $supplement->duration_days,
                    'supplement_goal' => $supplement->supplement_goal,
                    'observations' => $supplement->observations,
                    'last_taken_at' => $supplement->last_taken_at,
                    'smart_stack' => $supplement->smartStack ? [
                        'id' => $supplement->smartStack->id,
                        'name' => $supplement->smartStack->name,
                    ] : null,
                ];
            });

        return response()->json([
            'data' => [
                'supplements' => $supplements
            ]
        ]);
    }

    public function log(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $supplement = Supplement::where('user_id', $user->id)->findOrFail($id);

        // Create log entry
        $log = SupplementLog::create([
            'user_id' => $user->id,
            'supplement_id' => $supplement->id,
            'taken_at' => now(),
            'empresa_id' => $user->empresa_id, // Usando a trait BelongsToUserCompany que pode requerer empresa_id
        ]);

        // Update last taken at
        $supplement->update([
            'last_taken_at' => now(),
        ]);

        return response()->json([
            'message' => 'Suplemento marcado como tomado.',
            'data' => [
                'id' => $log->id,
                'taken_at' => $log->taken_at,
                'supplement_id' => $supplement->id,
                'last_taken_at' => $supplement->last_taken_at,
            ]
        ], 201);
    }
}
