<?php

namespace App\Http\Controllers\Api\V1\Professional;

use App\Http\Controllers\Controller;
use App\Models\HealthAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();
        $patientIds = $professional->patients()->where('pacientes.status', 'Sim')->pluck('users.id');

        $query = HealthAlert::query()
            ->whereIn('user_id', $patientIds)
            ->with('user:id,name')
            ->latest();

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        $limit = min(50, max(1, (int) $request->query('limit', 20)));

        $alerts = $query->limit($limit)->get()->map(fn (HealthAlert $alert) => [
            'id' => $alert->id,
            'user_id' => $alert->user_id,
            'patient_name' => $alert->user?->name,
            'type' => $alert->type,
            'severity' => $alert->severity,
            'message' => $alert->message,
            'is_read' => (bool) $alert->is_read,
            'created_at' => optional($alert->created_at)->toIso8601String(),
        ]);

        return response()->json(['data' => ['alerts' => $alerts]]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $patientIds = $request->user()->patients()->where('pacientes.status', 'Sim')->pluck('users.id');

        $alert = HealthAlert::query()
            ->whereIn('user_id', $patientIds)
            ->findOrFail($id);

        $alert->update(['is_read' => true]);

        return response()->json(['message' => 'Alerta marcado como lido.']);
    }
}
