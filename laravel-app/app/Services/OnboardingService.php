<?php

namespace App\Services;

use App\Models\AcademyCompany;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OnboardingService
{
    public function __construct(
        private readonly TenantStorageService $storageService
    ) {}

    /**
     * Inicia um novo processo de onboarding para uma empresa.
     */
    public function start(string $accountType): AcademyCompany
    {
        return AcademyCompany::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Nova Conta',
            'slug' => 'nova-conta-' . Str::random(6),
            'account_type' => $accountType,
            'onboarding_status' => 'pending',
            'current_onboarding_step' => 2, // Passo 1 (Seleção de tipo) já foi feito
            'is_active' => false,
        ]);
    }

    /**
     * Salva os dados de uma etapa e avança para a próxima.
     */
    public function saveStep(AcademyCompany $company, int $step, array $data): AcademyCompany
    {
        $state = $company->onboarding_state ?? [];
        $state["step_{$step}"] = $data;

        $updateData = ['onboarding_state' => $state];

        // Mapear dados específicos para colunas da tabela se necessário
        if ($step === 2) { // Dados Empresariais
            $updateData['name'] = $data['name'] ?? $company->name;
            $updateData['legal_name'] = $data['legal_name'] ?? $company->legal_name;
            $updateData['tax_id'] = $data['tax_id'] ?? $company->tax_id;
            $updateData['state_registration'] = $data['state_registration'] ?? $company->state_registration;
            $updateData['municipal_registration'] = $data['municipal_registration'] ?? $company->municipal_registration;
            $updateData['slug'] = Str::slug($data['name']);
        }

        if ($step === 3) { // Contato
            $updateData['phone'] = $data['phone'] ?? $company->phone;
            $updateData['whatsapp'] = $data['whatsapp'] ?? $company->whatsapp;
            $updateData['responsible_email'] = $data['email'] ?? $company->responsible_email;
            $updateData['website'] = $data['website'] ?? $company->website;
            $updateData['instagram'] = $data['instagram'] ?? $company->instagram;
        }

        if ($step === 4) { // Endereço
            $updateData['zip_code'] = $data['zip_code'] ?? $company->zip_code;
            $updateData['street'] = $data['street'] ?? $company->street;
            $updateData['number'] = $data['number'] ?? $company->number;
            $updateData['city'] = $data['city'] ?? $company->city;
            $updateData['state'] = $data['state'] ?? $company->state;
            $updateData['country'] = $data['country'] ?? $company->country;
            $updateData['address'] = ($data['street'] ?? '') . ', ' . ($data['number'] ?? '') . ' - ' . ($data['city'] ?? '');
        }

        if ($step === 5) { // Admin Account
            $this->createAdminUser($company, $data);
        }

        if ($step === 6) { // Configurações
            $updateData['primary_color'] = $data['primary_color'] ?? $company->primary_color;
            $updateData['language'] = $data['language'] ?? $company->language;
            $updateData['currency'] = $data['currency'] ?? $company->currency;
            $updateData['timezone'] = $data['timezone'] ?? $company->timezone;
            
            if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($company->logo_path) {
                    $this->storageService->delete($company->logo_path);
                }
                $updateData['logo_path'] = $this->storageService->store($company, $data['logo'], 'logos');
            }

            if (isset($data['pdf_settings'])) {
                $updateData['pdf_settings'] = array_merge($company->pdf_settings ?? [], $data['pdf_settings']);
            }
        }

        $updateData['current_onboarding_step'] = min($step + 1, 7);
        
        if ($step === 7) {
            $updateData['onboarding_status'] = 'completed';
            $updateData['is_active'] = true;
        }

        $company->update($updateData);

        // Registrar no log de auditoria do onboarding
        \App\Models\ClinicOnboardingStep::updateOrCreate(
            ['academy_company_id' => $company->id, 'step_key' => "step_{$step}"],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'data' => $data
            ]
        );

        return $company;
    }

    /**
     * Cria o usuário administrador da clínica.
     */
    private function createAdminUser(AcademyCompany $company, array $data): User
    {
        return DB::transaction(function () use ($company, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'academy_company_id' => $company->id,
                'is_active' => true,
            ]);

            // Atribuir papel de administrador da clínica (manager ou admin_clinic)
            // Verificar qual papel existe no sistema
            $role = \App\Models\Role::where('name', 'manager')->first() 
                 ?? \App\Models\Role::where('name', 'admin')->first();
            
            if ($role) {
                $user->roles()->attach($role->id);
            }

            return $user;
        });
    }

    /**
     * Verifica duplicidade de documentos ou emails.
     */
    public function checkDuplicity(string $type, string $value, ?int $excludeCompanyId = null): bool
    {
        return match ($type) {
            'tax_id' => AcademyCompany::where('tax_id', $value)->where('id', '!=', $excludeCompanyId)->exists(),
            'email' => User::where('email', $value)->exists(),
            'slug' => AcademyCompany::where('slug', $value)->where('id', '!=', $excludeCompanyId)->exists(),
            default => false,
        };
    }
}
