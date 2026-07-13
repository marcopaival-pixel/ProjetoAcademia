<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use App\Services\PanelAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $panels = $this->resolvePanels();

        $activePatientId = $request->attributes->get('active_patient_id');

        if ($activePatientId === null && $this->hasRole(['aluno', 'paciente'])) {

            $activePatientId = (int) $this->id;

        }

        return [

            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            /**
             * Roles normalizadas para o novo ecossistema.
             * O array é garantido mesmo que o usuário não tenha roles.
             */
            'roles' => collect($this->getRoleNames())->map(function($role) {
                if (in_array($role, ['aluno', 'athlete', 'paciente'])) {
                    return 'student';
                }
                return $role;
            })->unique()->values()->all(),

            'is_premium' => (bool) $this->hasPremiumAccess(),

            'is_student' => $this->hasRole(['aluno', 'paciente']),

            'is_professional' => $this->isProfessional() || $this->isAdministrator() || $this->hasRole(['admin', 'clinic_admin']),

            'vinculos' => $this->professionals()
                ->wherePivot('status', 'Sim')
                ->get()
                ->map(fn($via) => [
                    'id' => $via->id,
                    'name' => $via->name,
                    'specialty' => $via->professionalProfile?->especialidade?->nome ?? 'Profissional',
                ]),

            'student_status' => $this->professionals()->wherePivot('status', 'Sim')->exists() ? 'vinculado' : 'independente',

            'access_contexts' => $this->resolveAccessContexts(),

            'panels' => $panels,

            'active_patient_id' => $activePatientId,

            'clinic_id' => $this->clinic_id,

            'academy_company_id' => $this->academy_company_id,

            'status' => $this->status,

            'branding' => $this->resolveBranding(),

            /**
             * Tenants (Organizations) ao qual este usuário pertence.
             * Permite ao app saber em quais centros esportivos o usuário está vinculado.
             */
            'organizations' => $this->whenLoaded('organizations', function () {
                return $this->organizations->map(function (\App\Models\Organization $org) {
                    /** @var \Illuminate\Database\Eloquent\Relations\Pivot|null $pivot */
                    $pivot = $org->getRelationValue('pivot');

                    return [
                        'id' => $org->id,
                        'name' => $org->name,
                        'type' => $org->type,
                        'role' => $pivot?->getAttribute('role'),
                    ];
                })->values()->all();
            }),

            'profile' => $this->resolveProfileData(),

        ];

    }

    /**
     * @return list<string>
     */
    private function resolvePanels(): array
    {

        $panels = [];

        $service = app(PanelAccessService::class);

        foreach ([

            PanelAccessService::PANEL_STUDENT,

            PanelAccessService::PANEL_PATIENT,

            PanelAccessService::PANEL_PROFESSIONAL,

            PanelAccessService::PANEL_ADMIN,

            PanelAccessService::PANEL_REPRESENTATIVE,

        ] as $panel) {

            if ($service->userCanUsePanel($this->resource, $panel)) {
                $panels[] = $panel;
            }

        }

        return $panels;

    }

    /**
     * @return array<string, mixed>
     */
    private function resolveBranding(): array
    {

        $defaults = [

            'primary_color' => '#6366f1',

            'accent_color' => '#a855f7',

            'clinic_name' => 'NexShape',

        ];

        if ($this->hasRole(['aluno', 'paciente'])) {

            /** @var \App\Models\User|null $professional */
            $professional = $this->professionals()
                ->with('branding')
                ->wherePivot('status', 'Sim')
                ->first();

            if ($professional && ($professional->branding ?? null)) {

                return array_merge($defaults, [

                    'primary_color' => $professional->branding->primary_color ?? $defaults['primary_color'],

                    'accent_color' => $professional->branding->accent_color ?? $defaults['accent_color'],

                    'clinic_name' => $professional->branding->clinic_name ?? $defaults['clinic_name'],

                ]);

            }

        }

        if ($this->isProfessional() && $this->branding) {

            return array_merge($defaults, [

                'primary_color' => $this->branding->primary_color ?? $defaults['primary_color'],

                'accent_color' => $this->branding->accent_color ?? $defaults['accent_color'],

                'clinic_name' => $this->branding->clinic_name ?? $defaults['clinic_name'],

            ]);

        }

        return $defaults;

    }

    /**
     * @return array<string, mixed>
     */
    private function resolveProfileData(): array
    {
        $profile = $this->profile;
        $latestWeight = $this->weightEntries()
            ->orderByDesc('weighed_at')
            ->first();

        return [
            'birth_date' => $profile?->birth_date?->format('Y-m-d'),
            'sex' => $profile?->sex,
            'height_cm' => $profile?->height_cm,
            'current_weight_kg' => $latestWeight ? (float) $latestWeight->weight_kg : null,
            'target_weight_kg' => $profile?->target_weight_kg !== null ? (float) $profile->target_weight_kg : null,
            'activity_level' => $profile?->activity_level,
            'climate' => $profile?->climate,
            'goal' => $profile?->goal,
            'daily_calorie_target' => $profile?->daily_calorie_target,
            'water_target_ml' => $profile?->water_target_ml,
            'is_water_target_auto' => (bool) ($profile?->is_water_target_auto ?? false),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveAccessContexts(): array
    {
        $contexts = [];

        if ($this->hasRole(['aluno', 'paciente'])) {
            if ($this->hasRole('aluno')) {
                $contexts[] = [
                    'type' => 'personal',
                    'id' => 'personal',
                    'label' => 'Meu painel',
                ];
            }

            $linkedProfessionals = $this->professionals()->wherePivot('status', 'Sim')->get();
            foreach ($linkedProfessionals as $prof) {
                $contexts[] = [
                    'type' => 'professional',
                    'id' => (string) $prof->id,
                    'label' => 'Acompanhamento com ' . $prof->name,
                ];
            }

            foreach ($this->organizations as $org) {
                $contexts[] = [
                    'type' => 'clinic',
                    'id' => (string) $org->id,
                    'label' => $org->name,
                ];
            }
        }

        return $contexts;
    }
}
