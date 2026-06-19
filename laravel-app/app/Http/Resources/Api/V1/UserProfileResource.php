<?php



namespace App\Http\Resources\Api\V1;



use App\Services\PanelAccessService;

use Illuminate\Http\Request;

use Illuminate\Http\Resources\Json\JsonResource;



/** @mixin \App\Models\User */

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

            'roles' => $this->getRoleNames(),

            'is_premium' => (bool) $this->hasPremiumAccess(),

            'is_student' => $this->hasRole(['aluno', 'paciente']),

            'is_professional' => $this->isProfessional(),

            'panels' => $panels,

            'active_patient_id' => $activePatientId,

            'clinic_id' => $this->clinic_id,

            'academy_company_id' => $this->academy_company_id,

            'status' => $this->status,

            'branding' => $this->resolveBranding(),

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

            $professional = $this->professionals()

                ->with('branding')

                ->wherePivot('status', 'Sim')

                ->first();



            if ($professional?->branding) {

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

}

