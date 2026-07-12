<?php

namespace App\Services;

use App\Models\HealthPermission;
use App\Models\User;
use Illuminate\Support\Str;

class MedicalRecordModuleManager
{
    private const COMMON_MODULES = ['summary', 'evolutions', 'documents', 'history'];

    private const PROFESSIONAL_MODULES = [
        'personal trainer' => ['assessments', 'trainings', 'evolutions', 'photos'],
        'nutricionista' => ['nutrition', 'evolutions', 'documents'],
        'fisioterapeuta' => ['functional_assessment', 'therapeutic_exercises', 'pain_tracking', 'evolutions'],
        'psicologo' => ['session_notes', 'restricted_notes', 'evolutions'],
        'medico' => ['diagnosis', 'prescriptions', 'reports', 'certificates', 'documents'],
        'biomedico' => ['aesthetic_protocols', 'photos', 'evolutions', 'documents'],
        'esteticista' => ['aesthetic_protocols', 'photos', 'evolutions', 'documents'],
    ];

    private const RESTRICTED_MODULE_DATA_TYPES = [
        'session_notes' => 'psychology_session_notes',
        'restricted_notes' => 'restricted_notes',
    ];

    private array $modules = [
        'summary' => ['label' => 'Resumo', 'description' => 'Dados cadastrais, contato, documentos e visão geral do acompanhamento.', 'icon' => 'fas fa-id-badge', 'color' => 'blue', 'route' => 'professional.patients.medical-records.summary', 'status' => 'active'],
        'evolutions' => ['label' => 'Evolução / Atendimentos', 'description' => 'Registro cronológico de consultas, sessões, condutas e orientações.', 'icon' => 'fas fa-notes-medical', 'color' => 'cyan', 'route' => 'professional.patients.medical-records.evolutions.index', 'status' => 'active'],
        'trainings' => ['label' => 'Treinos', 'description' => 'Plano atual, histórico de cargas, alterações e metas do aluno.', 'icon' => 'fas fa-dumbbell', 'color' => 'orange', 'route' => 'professional.patients.trainings.index', 'clinic_keys' => ['workout', 'treinos'], 'status' => 'active'],
        'nutrition' => ['label' => 'Plano alimentar', 'description' => 'Anamnese alimentar, orientações nutricionais e acompanhamento.', 'icon' => 'fas fa-apple-alt', 'color' => 'green', 'route' => 'professional.patients.medical-records.nutrition.index', 'clinic_keys' => ['nutrition', 'dietas'], 'status' => 'active'],
        'documents' => ['label' => 'Exames / Documentos', 'description' => 'PDFs, imagens, laudos, receitas externas e anexos clínicos.', 'icon' => 'fas fa-folder-open', 'color' => 'amber', 'route' => 'professional.patients.medical-records.documents', 'clinic_keys' => ['clinical_docs', 'prontuarios'], 'status' => 'active'],
        'prescriptions' => ['label' => 'Receitas', 'description' => 'Prescrições, medicamentos, suplementos e recomendações formais.', 'icon' => 'fas fa-prescription-bottle-alt', 'color' => 'emerald', 'route' => 'professional.patients.medical-records.prescriptions.index', 'clinic_keys' => ['prescriptions', 'prontuarios'], 'status' => 'active'],
        'reports' => ['label' => 'Laudos', 'description' => 'Laudos, pareceres técnicos e análises emitidas pelo profissional.', 'icon' => 'fas fa-file-medical-alt', 'color' => 'purple', 'route' => 'professional.patients.medical-records.reports.index', 'clinic_keys' => ['clinical_docs', 'prontuarios'], 'status' => 'active'],
        'certificates' => ['label' => 'Atestados', 'description' => 'Atestados e documentos formais com controle de emissão.', 'icon' => 'fas fa-file-contract', 'color' => 'pink', 'route' => 'professional.patients.medical-records.certificates.index', 'clinic_keys' => ['clinical_docs', 'prontuarios'], 'status' => 'active'],
        'history' => ['label' => 'Histórico', 'description' => 'Trilha de auditoria com data, hora e responsável por alterações.', 'icon' => 'fas fa-history', 'color' => 'zinc', 'route' => 'professional.patients.medical-records.history', 'status' => 'active'],
        'assessments' => ['label' => 'Avaliações', 'description' => 'Peso, IMC, composição corporal, medidas, testes e sinais vitais.', 'icon' => 'fas fa-weight', 'color' => 'teal', 'route' => 'professional.patients.medical-records.assessments.index', 'clinic_keys' => ['body_composition', 'avaliacoes'], 'status' => 'active'],
        'photos' => ['label' => 'Fotos de evolução', 'description' => 'Comparação visual lado a lado por data e protocolo.', 'icon' => 'fas fa-images', 'color' => 'indigo', 'route' => 'professional.patients.medical-records.photos.index', 'status' => 'active'],
        'diagnosis' => ['label' => 'Diagnósticos / CID', 'description' => 'Hipóteses, diagnósticos, CID e registro clínico estruturado.', 'icon' => 'fas fa-stethoscope', 'color' => 'red', 'route' => 'professional.patients.medical-records.summary', 'status' => 'active'],
        'session_notes' => ['label' => 'Registros de sessão', 'description' => 'Notas sensíveis de sessão com atenção especial a permissões.', 'icon' => 'fas fa-lock', 'color' => 'violet', 'route' => 'professional.patients.medical-records.session-notes.index', 'status' => 'restricted'],
        'restricted_notes' => ['label' => 'Observações restritas', 'description' => 'Conteúdo confidencial visível apenas a profissionais autorizados.', 'icon' => 'fas fa-user-shield', 'color' => 'rose', 'route' => null, 'status' => 'restricted'],
        'functional_assessment' => ['label' => 'Avaliação funcional', 'description' => 'Testes funcionais, ortopédicos e evolução terapêutica.', 'icon' => 'fas fa-walking', 'color' => 'lime', 'route' => null, 'status' => 'planned'],
        'therapeutic_exercises' => ['label' => 'Exercícios terapêuticos', 'description' => 'Condutas, séries terapêuticas e plano de reabilitação.', 'icon' => 'fas fa-heartbeat', 'color' => 'red', 'route' => 'professional.patients.medical-records.protocols.index', 'status' => 'active'],
        'pain_tracking' => ['label' => 'Diário de dor', 'description' => 'Registro de dor, localização, intensidade e recorrência.', 'icon' => 'fas fa-heart-pulse', 'color' => 'red', 'route' => 'professional.patients.medical-records.pain.index', 'clinic_keys' => ['pain_tracking'], 'status' => 'active'],
        'aesthetic_protocols' => ['label' => 'Protocolos estéticos', 'description' => 'Protocolos, sessões, produtos, cuidados e evolução estética.', 'icon' => 'fas fa-spa', 'color' => 'fuchsia', 'route' => 'professional.patients.medical-records.protocols.index', 'status' => 'active'],
    ];

    public function forProfessional(User $professional, User $patient): array
    {
        $professional->loadMissing(['professionalProfile.profession', 'professionalProfile.especialidade', 'clinic']);
        $patient->loadMissing('clinic');

        $keys = collect(self::COMMON_MODULES)
            ->merge($this->modulesForProfessional($professional))
            ->unique()
            ->values();

        $enabledClinicModules = $patient->clinic?->enabled_modules ?? $professional->clinic?->enabled_modules;

        return $keys
            ->filter(fn (string $key) => $this->isAllowedByClinic($key, $enabledClinicModules))
            ->map(fn (string $key) => $this->modulePayload($key, $professional, $patient))
            ->values()
            ->all();
    }

    public function navigationForProfessional(User $professional, User $patient): array
    {
        return collect($this->forProfessional($professional, $patient))
            ->filter(fn (array $module) => ! empty($module['route']) && ($module['status'] === 'active' || ($module['can_access'] ?? false)))
            ->map(fn (array $module) => [
                'route' => $module['route'],
                'label' => $module['label'],
                'icon' => $module['icon'],
            ])
            ->values()
            ->all();
    }

    public function clinicModuleOptions(): array
    {
        return collect($this->modules)
            ->reject(fn (array $module, string $key): bool => in_array($key, self::COMMON_MODULES, true))
            ->mapWithKeys(function (array $module, string $key): array {
                $clinicKeys = $module['clinic_keys'] ?? [$key];

                return collect($clinicKeys)
                    ->mapWithKeys(fn (string $clinicKey): array => [
                        $clinicKey => [
                            'label' => $module['label'],
                            'description' => $module['description'],
                            'icon' => $module['icon'],
                            'status' => $module['status'],
                        ],
                    ])
                    ->all();
            })
            ->sortKeys()
            ->all();
    }

    public function canAccessRestrictedModule(User $professional, User $patient, string $moduleKey, string $minimumLevel = 'view'): bool
    {
        $dataType = self::RESTRICTED_MODULE_DATA_TYPES[$moduleKey] ?? null;

        if ($dataType === null) {
            return true;
        }

        $allowedLevels = $minimumLevel === 'edit' ? ['edit'] : ['view', 'edit'];

        if (! $professional->getKey() || ! $patient->getKey()) {
            return false;
        }

        if ($patient->relationLoaded('healthPermissions')) {
            return $patient->getRelation('healthPermissions')
                ->contains(function (HealthPermission $permission) use ($professional, $dataType, $allowedLevels): bool {
                    return (int) $permission->getAttribute('professional_id') === (int) $professional->getKey()
                        && $permission->getAttribute('data_type') === $dataType
                        && in_array($permission->getAttribute('access_level'), $allowedLevels, true);
                });
        }

        return HealthPermission::query()
            ->where('patient_id', $patient->getKey())
            ->where('professional_id', $professional->getKey())
            ->where('data_type', $dataType)
            ->whereIn('access_level', $allowedLevels)
            ->exists();
    }

    private function modulePayload(string $key, User $professional, User $patient): array
    {
        $module = array_merge($this->modules[$key], ['key' => $key]);

        if ($module['status'] !== 'restricted') {
            return $module;
        }

        $canAccess = $this->canAccessRestrictedModule($professional, $patient, $key);

        return array_merge($module, [
            'can_access' => $canAccess,
            'data_type' => self::RESTRICTED_MODULE_DATA_TYPES[$key] ?? null,
            'route' => $canAccess ? $module['route'] : null,
            'access_message' => $canAccess
                ? 'Acesso liberado por permissao de saude.'
                : 'Acesso bloqueado ate o paciente autorizar este tipo de dado.',
        ]);
    }

    private function modulesForProfessional(User $professional): array
    {
        $profile = $professional->professionalProfile;
        $terms = [
            $profile?->specialty,
            $profile?->profession?->name ?? null,
            $profile?->especialidade?->nome ?? null,
        ];

        $normalized = collect($terms)
            ->filter()
            ->map(fn (string $term) => Str::of($term)->ascii()->lower()->value())
            ->implode(' ');

        foreach (self::PROFESSIONAL_MODULES as $needle => $modules) {
            if (str_contains($normalized, $needle)) {
                return $modules;
            }
        }

        return ['assessments', 'trainings', 'nutrition', 'prescriptions', 'reports', 'certificates'];
    }

    private function isAllowedByClinic(string $key, mixed $enabledClinicModules): bool
    {
        if (in_array($key, self::COMMON_MODULES, true)) {
            return true;
        }

        if (! is_array($enabledClinicModules) || $enabledClinicModules === []) {
            return true;
        }

        $clinicKeys = $this->modules[$key]['clinic_keys'] ?? [$key];

        return collect($clinicKeys)->contains(fn (string $clinicKey) => in_array($clinicKey, $enabledClinicModules, true));
    }
}
