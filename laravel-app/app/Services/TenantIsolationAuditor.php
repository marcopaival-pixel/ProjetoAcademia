<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

class TenantIsolationAuditor
{
    private const TENANT_TRAITS = [
        'App\Models\Traits\BelongsToCompany',
        'App\Models\Traits\HasClinic',
        'App\Models\Traits\FillsTenantColumns',
        'App\Models\Traits\FiltersByProfessional',
        'App\Models\Traits\FiltersByProfessionalOwner',
        'App\Models\Traits\FiltersByRepresentative',
        'App\Models\Traits\ScopesByTrainingPlanClinic',
        'App\Models\Traits\BelongsToOmniCompany',
        'App\Models\Traits\BelongsToUserCompany',
        'App\Models\Traits\BelongsToScopedParent',
    ];

    /** Models de plataforma (sem isolamento por design). */
    private const GLOBAL_ALLOWLIST = [
        'AcademyCompany',
        'Clinic',
        'Plan',
        'Role',
        'Permission',
        'Menu',
        'DeployRelease',
        'AdminEntity',
        'AdminField',
        'AdminSetting',
        'AdminLog',
        'SystemSetting',
        'SystemError',
        'ExerciseCatalog',
        'Muscle',
        'MuscleGroup',
        'KnowledgeCategory',
        'KnowledgeArticle',
        'AppFeature',
        'FeatureLimit',
        'AiFeatureCost',
        'UpgradePopup',
        'Organization',
        'Especialidade',
        'Announcement',
        'AppLaunchLead',
        'KanbanTask',
        'Lead',
        'PaymentWebhookLog',
        'CommunicationGroup',
        'LeadInteraction',
        'CouponUsage',
        'GeneratedReport',
        'AgendaSetting',
        'AiCreditPackage',
        'SupplementCatalog',
        'ActiveRestRoutine',
        'MarketingBanner',
        'MarketingBannerTarget',
        'CommunitySticker',
        'OmniBotStep',
        'OmniBotOption',
        'OnboardingStep',
        'ApiIntegration',
        'ApiIntegrationLog',
        'BibliotecaInteligente',
        'Nutrient',
        'Food',
        'PlanFeature',
        'PlanPermission',
        'RolePermission',
        'Profession',
        'PrescriptionTemplate',
        'MealTemplate',
        'MealTemplateItem',
        'Contract',
        'ConfiguracaoEmail',
        'LogEnvioEmail',
        'SubscriptionLog',
        'CommercialProposal',
        'Goal',
        'WithdrawalRequest',
        'ProfessionalAvailability',
        'ProfessionalPlan',
        'Photo',
        'AppointmentWaitlist',
        'SocialPostQueue',
        'EmailTemplate',
        'EmailProvider',
        'Coupon',
        'Representative',
        'TrainingModule',
        'TrainingLesson',
        // Logs e observabilidade (plataforma; acesso restrito a admin)
        'ApiAccessLog',
        'AuthAuditLog',
        'ClientErrorLog',
        'PdfDeliveryLog',
        'RepresentativeAudit',
        // Configuração global de gateways (admin; sem clinic_id)
        'PaymentSetting',
        'OmniCompany',
        'CreditoPacote',
        'DeviceToken',
        // Shop sub-resources & Platform models (isolated via parent or global)
        'InternalEmail',
        'ShopCartItem',
        'ShopCouponUsage',
        'ShopOrderItem',
        'ShopPointsTransaction',
        'ShopProductImage',
        'ShopWishlist',
    ];

    /**
     * @return array{isolated: list<string>, global_ok: list<string>, missing: list<array{class: string, table: string|null, columns: list<string>}>}
     */
    public function audit(): array
    {
        $isolated = [];
        $globalOk = [];
        $missing = [];

        foreach (glob(app_path('Models') . '/*.php') as $file) {
            $base = basename($file, '.php');
            if ($base === 'User') {
                continue;
            }

            $class = "App\\Models\\{$base}";
            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }

            $model = new $class;
            if (! method_exists($model, 'getTable')) {
                continue;
            }

            $table = $model->getTable();
            $usesTenantTrait = $this->usesTenantTrait($reflection);

            if (in_array($base, self::GLOBAL_ALLOWLIST, true)) {
                $globalOk[] = $class;
                continue;
            }

            if ($usesTenantTrait) {
                $isolated[] = $class;
                continue;
            }

            $columns = $this->tenantColumnsOnTable($table);
            $missing[] = [
                'class' => $class,
                'table' => $table,
                'columns' => $columns,
                'has_db_columns' => $columns !== [],
            ];
        }

        usort($missing, fn ($a, $b) => strcmp($a['class'], $b['class']));

        return [
            'isolated' => $isolated,
            'global_ok' => $globalOk,
            'missing' => $missing,
        ];
    }

    private function usesTenantTrait(ReflectionClass $reflection): bool
    {
        foreach (self::TENANT_TRAITS as $trait) {
            if ($reflection->isSubclassOf($trait) || in_array($trait, class_uses_recursive($reflection->getName()), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function tenantColumnsOnTable(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $found = [];
        foreach (['academy_company_id', 'clinic_id', 'company_id', 'tenant_id'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $found[] = $col;
            }
        }

        return $found;
    }
}
