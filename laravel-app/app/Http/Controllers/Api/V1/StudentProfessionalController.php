<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProfessionalController extends Controller
{
    use FormatsApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $links = \App\Models\ProfessionalPatient::with(['professional.professionalProfile', 'professional.branding'])
            ->where('user_id', $user->id)
            ->where('status', 'Sim')
            ->get();

        $professionals = $links->map(function (\App\Models\ProfessionalPatient $link) {
            $professional = $link->professional;
            if (!$professional) {
                return null;
            }

            $profile = $professional->professionalProfile;
            $branding = $professional->branding;

            // Fetch and map sensitive permissions
            $permissions = $link->patient_permissions ?? [];
            $healthPermissions = \App\Models\HealthPermission::where('patient_id', $link->user_id)
                ->where('professional_id', $link->profissional_id)
                ->whereIn('data_type', ['psychology_session_notes', 'restricted_notes'])
                ->get()
                ->keyBy('data_type');

            foreach (['psychology_session_notes', 'restricted_notes'] as $dataType) {
                if ($healthPermissions->has($dataType)) {
                    $permissions[$dataType] = in_array(
                        $healthPermissions[$dataType]->access_level,
                        ['view', 'edit'],
                        true
                    );
                } else {
                    $permissions[$dataType] = (bool) ($permissions[$dataType] ?? false);
                }
            }

            return [
                'id' => $professional->id,
                'link_id' => $link->id,
                'name' => $professional->name,
                'email' => $professional->email,
                'specialty' => $profile ? ($profile->specialty ?? null) : null,
                'service_types' => $profile ? ($profile->service_types ?? []) : [],
                'branding' => [
                    'clinic_name' => $branding ? ($branding->clinic_name ?? null) : null,
                    'primary_color' => $branding ? ($branding->primary_color ?? null) : null,
                ],
                'permissions' => $permissions,
            ];
        })->filter()->values()->all();

        return $this->success(['professionals' => $professionals]);
    }

    public function updatePermissions(Request $request, \App\Models\ProfessionalPatient $link): JsonResponse
    {
        if ((int) $link->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.psychology_session_notes' => ['required', 'boolean'],
            'permissions.restricted_notes' => ['required', 'boolean'],
        ]);

        $permissions = $validated['permissions'];

        $link->update([
            'patient_permissions' => $permissions,
        ]);

        $this->syncSensitiveHealthPermissions($link, $permissions);

        \App\Models\AdminLog::create([
            'user_id' => $request->user()->id,
            'action' => 'PATIENT_UPDATED_PERMISSIONS',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $link->user_id,
                'professional_id' => $link->profissional_id,
                'permissions' => $permissions,
                'sensitive_permissions' => array_intersect_key($permissions, [
                    'psychology_session_notes' => true,
                    'restricted_notes' => true,
                ]),
            ],
            'created_at' => now(),
        ]);

        return $this->success([
            'message' => 'Permissões atualizadas com sucesso.',
            'permissions' => $permissions,
        ]);
    }

    public function revoke(Request $request, \App\Models\ProfessionalPatient $link): JsonResponse
    {
        if ((int) $link->user_id !== (int) $request->user()->id) {
            return $this->error('Acesso negado.', 403, 'forbidden');
        }

        $link->update([
            'status' => 'Não',
        ]);

        \App\Models\HealthPermission::where('patient_id', $link->user_id)
            ->where('professional_id', $link->profissional_id)
            ->update(['access_level' => 'none']);

        \App\Models\AdminLog::create([
            'user_id' => $request->user()->id,
            'action' => 'PATIENT_REVOKED_PROFESSIONAL_LINK',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $link->user_id,
                'professional_id' => $link->profissional_id,
            ],
            'created_at' => now(),
        ]);

        return $this->success([
            'message' => 'Vínculo revogado com sucesso.',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = User::whereHas('roles', function($q) {
                $q->where('name', 'professional');
            })
            ->whereHas('professionalProfile', function($q) {
                $q->where('is_public', true);
            })
            ->with(['professionalProfile.profession', 'profile']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('specialty')) {
            $query->whereHas('professionalProfile', function($sub) use ($request) {
                $sub->where('specialty', 'like', "%{$request->specialty}%");
            });
        }

        if ($request->filled('service_type')) {
            $query->whereHas('professionalProfile', function($sub) use ($request) {
                $sub->whereJsonContains('service_types', $request->service_type);
            });
        }

        $professionals = $query->paginate(12);

        $mapped = collect($professionals->items())->map(function(User $professional) {
            $profile = $professional->professionalProfile;
            return [
                'id' => $professional->id,
                'name' => $professional->name,
                'email' => $professional->email,
                'specialty' => $profile ? ($profile->specialty ?? null) : null,
                'service_types' => $profile ? ($profile->service_types ?? []) : [],
                'profession' => $profile && $profile->profession ? $profile->profession->name : null,
            ];
        });

        return $this->success([
            'professionals' => $mapped,
            'meta' => [
                'current_page' => $professionals->currentPage(),
                'last_page' => $professionals->lastPage(),
                'total' => $professionals->total(),
            ]
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        $user = $request->user();
        $requests = \App\Models\ProfessionalPatientRequest::with('professional.professionalProfile')
            ->where('patient_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function(\App\Models\ProfessionalPatientRequest $req) {
                return [
                    'id' => $req->id,
                    'professional_id' => $req->professional_id,
                    'professional_name' => $req->professional?->name,
                    'message' => $req->message,
                    'status' => $req->status,
                    'created_at' => $req->created_at?->toIso8601String(),
                ];
            });

        return $this->success(['requests' => $requests]);
    }

    public function storeRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $patient = $request->user();
        $professionalId = $validated['professional_id'];

        if ($patient->professionals()->where('users.id', $professionalId)->exists()) {
            return $this->error('Você já está vinculado a este profissional.', 400, 'already_linked');
        }

        if (\App\Models\ProfessionalPatientRequest::where('patient_id', $patient->id)
            ->where('professional_id', $professionalId)
            ->where('status', 'pending')
            ->exists()) {
            return $this->error('Você já possui uma solicitação pendente para este profissional.', 400, 'request_pending');
        }

        $linkRequest = \App\Models\ProfessionalPatientRequest::create([
            'patient_id' => $patient->id,
            'professional_id' => $professionalId,
            'message' => $validated['message'] ?? null,
            'status' => 'pending'
        ]);

        return $this->success([
            'message' => 'Solicitação de vínculo enviada com sucesso.',
            'request' => [
                'id' => $linkRequest->id,
                'status' => $linkRequest->status,
            ]
        ], status: 201);
    }

    private function syncSensitiveHealthPermissions(\App\Models\ProfessionalPatient $link, array $permissions): void
    {
        $sensitivePermissions = [
            'psychology_session_notes' => 'psychology_session_notes',
            'restricted_notes' => 'restricted_notes',
        ];

        foreach ($sensitivePermissions as $permissionKey => $dataType) {
            $value = $permissions[$permissionKey] ?? false;
            $accessLevel = $value ? 'view' : 'none';

            \App\Models\HealthPermission::updateOrCreate(
                [
                    'patient_id' => $link->user_id,
                    'professional_id' => $link->profissional_id,
                    'data_type' => $dataType,
                ],
                [
                    'access_level' => $accessLevel,
                    'updated_by' => auth()->id() ?? $link->user_id,
                ]
            );
        }
    }
}
