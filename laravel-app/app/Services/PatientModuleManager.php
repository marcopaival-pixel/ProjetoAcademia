<?php

namespace App\Services;

use App\Models\User;
use App\Models\PatientModule;
use App\Models\TrainingPlan;
use App\Models\BodyAssessment;
use App\Models\PatientDocument;
use App\Models\MedicalPrescription;
use App\Models\PainRecord;
use App\Models\FoodEntry;
use Illuminate\Support\Facades\Cache;

class PatientModuleManager
{
    /**
     * Define os módulos disponíveis e as tabelas/regras para auto-descoberta.
     */
    protected array $modulesMeta = [
        'workout' => [
            'name' => 'Treinos',
            'icon' => 'dumbbell',
            'color' => 'orange',
            'route' => 'patient.plans.index',
        ],
        'nutrition' => [
            'name' => 'Minha Dieta',
            'icon' => 'apple-alt',
            'color' => 'green',
            'route' => 'patient.treatment-plan',
        ],
        'clinical_docs' => [
            'name' => 'Exames e Documentos',
            'icon' => 'file-medical',
            'color' => 'blue',
            'route' => 'patient.documents',
        ],
        'prescriptions' => [
            'name' => 'Receitas',
            'icon' => 'prescription',
            'color' => 'teal',
            'route' => 'patient.prescriptions',
        ],
        'pain_tracking' => [
            'name' => 'Diário de Dor',
            'icon' => 'heartbeat',
            'color' => 'red',
            'route' => 'patient.evolution', // Ou rota de dor específica
        ],
        'body_composition' => [
            'name' => 'Composição Corporal',
            'icon' => 'weight',
            'color' => 'cyan',
            'route' => 'patient.evolution',
        ]
    ];

    /**
     * Obtém todos os módulos ativos para o paciente.
     * Retorna um array associativo dos metadados dos módulos ativos.
     */
    public function getActiveModules(User $patient): array
    {
        $cacheKey = "patient_active_modules_{$patient->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($patient) {
            $discoveredKeys = [];

            // 1. Auto-descoberta baseada em dados
            if (TrainingPlan::where('user_id', $patient->id)->where('is_active', true)->exists()) {
                $discoveredKeys[] = 'workout';
            }

            // Para dieta, podemos verificar planos de tratamento ou registros de refeições
            if ($patient->treatmentPlans()->where('is_active', true)->exists() || FoodEntry::where('user_id', $patient->id)->exists()) {
                $discoveredKeys[] = 'nutrition';
            }

            if (PatientDocument::where('patient_id', $patient->id)->exists()) {
                $discoveredKeys[] = 'clinical_docs';
            }

            if ($patient->medicalPrescriptions()->exists()) {
                $discoveredKeys[] = 'prescriptions';
            }

            if (PainRecord::where('user_id', $patient->id)->exists()) {
                $discoveredKeys[] = 'pain_tracking';
            }

            if (BodyAssessment::where('user_id', $patient->id)->exists()) {
                $discoveredKeys[] = 'body_composition';
            }

            // 2. Persistir/Atualizar descobertas na tabela `patient_modules`
            foreach ($discoveredKeys as $key) {
                PatientModule::updateOrCreate(
                    ['patient_id' => $patient->id, 'module_key' => $key],
                    ['auto_discovered' => true]
                );
            }

            // 2.5 Filtrar módulos permitidos na clínica
            $clinic = $patient->clinic_id ? \App\Models\Clinic::find($patient->clinic_id) : null;
            $clinicEnabledModules = $clinic ? $clinic->enabled_modules : null;

            // 3. Mesclar com as permissões/overrides do banco
            $dbModules = PatientModule::where('patient_id', $patient->id)->get()->keyBy('module_key');

            $activeModules = [];
            foreach ($this->modulesMeta as $key => $meta) {
                // Se a clínica desabilitou este módulo globalmente, ele não deve aparecer
                if (is_array($clinicEnabledModules) && !in_array($key, $clinicEnabledModules)) {
                    continue;
                }

                $dbModule = $dbModules->get($key);
                
                // Habilitado se estiver explicitamente ativado no DB, OU (não configurado no DB mas auto-descoberto)
                $isEnabled = $dbModule ? $dbModule->is_enabled : in_array($key, $discoveredKeys);

                if ($isEnabled) {
                    $activeModules[$key] = array_merge($meta, [
                        'key' => $key,
                        'is_custom' => $dbModule ? !$dbModule->auto_discovered : false
                    ]);
                }
            }

            return $activeModules;
        });
    }

    /**
     * Habilita ou desabilita manualmente um módulo para um paciente.
     */
    public function setModuleStatus(User $patient, string $moduleKey, bool $enabled): void
    {
        PatientModule::updateOrCreate(
            ['patient_id' => $patient->id, 'module_key' => $moduleKey],
            ['is_enabled' => $enabled, 'auto_discovered' => false]
        );

        Cache::forget("patient_active_modules_{$patient->id}");
    }
}
