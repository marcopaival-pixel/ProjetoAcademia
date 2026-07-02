<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\HealthAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalAlertController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $professional = $request->user();
        $limit = min((int) $request->query('limit', 20), 50);
        $unreadOnly = filter_var($request->query('unread_only', false), FILTER_VALIDATE_BOOL);

        $patientIds = $professional->patients()->pluck('users.id');

        $query = HealthAlert::query()
            ->with('user:id,name')
            ->whereIn('user_id', $patientIds)
            ->orderByDesc('created_at');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $alerts = $query->limit($limit)->get()->map(fn (HealthAlert $alert): array => [
            'id' => $alert->id,
            'patient_id' => $alert->user_id,
            'patient_name' => $alert->user?->name,
            'type' => $alert->type,
            'severity' => $alert->severity,
            'message' => $alert->message,
            'is_read' => (bool) $alert->is_read,
            'created_at' => $alert->created_at?->toIso8601String(),
        ])->values()->all();

        return $this->success(['alerts' => $alerts], ['count' => count($alerts)]);
    }

    public function markRead(Request $request, int $alert): JsonResponse
    {
        $professional = $request->user();
        $patientIds = $professional->patients()->pluck('users.id');

        $alertModel = HealthAlert::find($alert);

        if ($alertModel === null) {
            return $this->error('Alerta não encontrado.', 404, 'not_found');
        }

        if (! $patientIds->contains($alertModel->user_id)) {
            return $this->error('Alerta não pertence aos seus alunos.', 403, 'forbidden');
        }

        $alertModel->update(['is_read' => true]);

        return $this->success([
            'id' => $alertModel->id,
            'is_read' => true,
        ]);
    }
}
