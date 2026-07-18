<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalPatient;
use App\Models\ProfessionalPatientRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalConnectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $connections = ProfessionalPatient::with('professional.profile')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($conn) {
                return [
                    'id' => $conn->id,
                    'professional_id' => $conn->profissional_id,
                    'professional_name' => $conn->professional?->name,
                    'professional_specialty' => $conn->professional?->profile?->specialty ?? 'Profissional',
                    'status' => $conn->status,
                    'permissions' => $conn->patient_permissions ?? [],
                ];
            });

        return response()->json([
            'data' => [
                'professionals' => $connections,
            ]
        ]);
    }

    public function requestConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        // Verifica se ja tem vinculo ativo
        $exists = ProfessionalPatient::where('user_id', $user->id)
            ->where('profissional_id', $validated['professional_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Você já possui vínculo com este profissional.'], 422);
        }

        $pending = ProfessionalPatientRequest::firstOrCreate([
            'patient_id' => $user->id,
            'professional_id' => $validated['professional_id'],
            'status' => 'pending',
        ], [
            'message' => $validated['message'] ?? null,
        ]);

        return response()->json([
            'message' => 'Solicitação de vínculo enviada com sucesso.',
            'data' => [
                'id' => $pending->id,
                'status' => $pending->status,
            ]
        ], 201);
    }

    public function updatePermissions(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
        ]);

        $connection = ProfessionalPatient::where('user_id', $request->user()->id)->findOrFail($id);

        // Mesclar permissoes atuais com as novas
        $current = $connection->patient_permissions ?? [];
        $newPermissions = array_merge($current, $validated['permissions']);

        $connection->update([
            'patient_permissions' => $newPermissions,
        ]);

        return response()->json([
            'message' => 'Permissões atualizadas com sucesso.',
            'data' => [
                'permissions' => $connection->patient_permissions,
            ]
        ]);
    }
}
