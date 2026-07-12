<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\HealthPermission;
use App\Models\ProfessionalPatient;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class ProfessionalManagementController extends Controller
{
    private const SENSITIVE_HEALTH_PERMISSIONS = [
        'psychology_session_notes' => 'psychology_session_notes',
        'restricted_notes' => 'restricted_notes',
    ];

    public function index()
    {
        $patient = auth()->user();

        $links = ProfessionalPatient::with(['professional.professionalProfile.profession'])
            ->where('user_id', $patient->id)
            ->where('status', 'Sim')
            ->get();

        $links->each(function (ProfessionalPatient $link): void {
            $permissions = $link->patient_permissions ?? [];

            $healthPermissions = HealthPermission::where('patient_id', $link->user_id)
                ->where('professional_id', $link->profissional_id)
                ->whereIn('data_type', array_values(self::SENSITIVE_HEALTH_PERMISSIONS))
                ->get()
                ->keyBy('data_type');

            foreach (self::SENSITIVE_HEALTH_PERMISSIONS as $permissionKey => $dataType) {
                if ($healthPermissions->has($dataType)) {
                    $permissions[$permissionKey] = in_array(
                        $healthPermissions[$dataType]->access_level,
                        ['view', 'edit'],
                        true
                    );
                }
            }

            $link->patient_permissions = $permissions;
        });

        return view('patient.my-professionals', compact('links'));
    }

    public function updatePermissions(Request $request, ProfessionalPatient $link)
    {
        if ((int) $link->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $permissions = collect($request->input('permissions', []))
            ->map(fn ($value) => $value === '1' || $value === 1 || $value === true)
            ->all();

        $link->update([
            'patient_permissions' => $permissions,
        ]);

        $this->syncSensitiveHealthPermissions($link, $permissions);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'PATIENT_UPDATED_PERMISSIONS',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $link->user_id,
                'professional_id' => $link->profissional_id,
                'permissions' => $permissions,
                'sensitive_permissions' => array_intersect_key($permissions, self::SENSITIVE_HEALTH_PERMISSIONS),
            ],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Permissoes atualizadas com sucesso!');
    }

    public function revoke(Request $request, ProfessionalPatient $link)
    {
        if ((int) $link->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $link->update([
            'status' => 'Não',
        ]);

        HealthPermission::where('patient_id', $link->user_id)
            ->where('professional_id', $link->profissional_id)
            ->update(['access_level' => 'none']);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'PATIENT_REVOKED_PROFESSIONAL_LINK',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'patient_id' => $link->user_id,
                'professional_id' => $link->profissional_id,
                'revoked_sensitive_data_types' => array_values(self::SENSITIVE_HEALTH_PERMISSIONS),
            ],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Vinculo revogado com sucesso. O profissional nao tem mais acesso aos seus dados.');
    }

    /**
     * @param array<string, bool> $permissions
     */
    private function syncSensitiveHealthPermissions(ProfessionalPatient $link, array $permissions): void
    {
        foreach (self::SENSITIVE_HEALTH_PERMISSIONS as $permissionKey => $dataType) {
            HealthPermission::updateOrCreate(
                [
                    'patient_id' => $link->user_id,
                    'professional_id' => $link->profissional_id,
                    'data_type' => $dataType,
                ],
                [
                    'access_level' => ($permissions[$permissionKey] ?? false) ? 'view' : 'none',
                    'is_confidential' => true,
                ]
            );
        }
    }
}
