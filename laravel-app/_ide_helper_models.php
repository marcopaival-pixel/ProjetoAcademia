<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $role
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIChat whereUserId($value)
 */
	class AIChat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $agent_name
 * @property string|null $model_name
 * @property string $user_message
 * @property string|null $ai_response
 * @property int $total_tokens
 * @property int $input_tokens
 * @property int $output_tokens
 * @property float $cost_usd
 * @property int $execution_time_ms
 * @property string $status
 * @property array<array-key, mixed>|null $context
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereAgentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereCostUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereExecutionTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereInputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereOutputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereTotalTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIOrchestratorLog whereUserMessage($value)
 */
	class AIOrchestratorLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $orchestrator_log_id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $document_type
 * @property float $confidence
 * @property string|null $image_path
 * @property string|null $image_hash
 * @property array<array-key, mixed>|null $extracted_data
 * @property array<array-key, mixed>|null $warnings
 * @property string|null $model_name
 * @property int $total_tokens
 * @property numeric $cost_usd
 * @property int $execution_time_ms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\AIOrchestratorLog|null $orchestratorLog
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereCostUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereExecutionTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereExtractedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereImageHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereOrchestratorLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereTotalTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AIVisionLog whereWarnings($value)
 */
	class AIVisionLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $uuid
 * @property string $name
 * @property string $slug
 * @property string|null $logo_path
 * @property string $primary_color
 * @property string $accent_color
 * @property string|null $legal_name
 * @property string|null $tax_id
 * @property string|null $mercadopago_user_id ID da conta do vendedor no Mercado Pago
 * @property numeric $platform_fee_percent Porcentagem retida pela plataforma
 * @property numeric $platform_fee_fixed Taxa fixa retida pela plataforma
 * @property string|null $account_type
 * @property string|null $state_registration
 * @property string|null $municipal_registration
 * @property string|null $responsible_name
 * @property string|null $responsible_email
 * @property string|null $phone
 * @property string|null $whatsapp
 * @property string|null $website
 * @property string|null $instagram
 * @property string|null $address
 * @property string|null $street
 * @property string|null $number
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $language
 * @property string|null $currency
 * @property string|null $timezone
 * @property string|null $zip_code
 * @property array<array-key, mixed>|null $pdf_settings
 * @property bool $is_active
 * @property string $onboarding_status
 * @property array<array-key, mixed>|null $onboarding_state
 * @property int $current_onboarding_step
 * @property bool $shared_medical_records
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $health_score 0-100 score of company health
 * @property string|null $churn_risk_level Baixo, Médio, Alto
 * @property array<array-key, mixed>|null $health_reasons
 * @property \Illuminate\Support\Carbon|null $last_health_calculated_at
 * @property-read \App\Models\ConfiguracaoEmail|null $configuracaoEmail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClinicOnboardingStep> $onboardingSteps
 * @property-read int|null $onboarding_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PdfTemplate> $pdfTemplates
 * @property-read int|null $pdf_templates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $professionals
 * @property-read int|null $professionals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AcademyUnit> $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereChurnRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereCurrentOnboardingStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereHealthReasons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereHealthScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereLastHealthCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereLegalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereMercadopagoUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereMunicipalRegistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereOnboardingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereOnboardingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany wherePdfSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany wherePlatformFeeFixed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany wherePlatformFeePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereResponsibleEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereResponsibleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereSharedMedicalRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereStateRegistration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyCompany whereZipCode($value)
 */
	class AcademyCompany extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property string $name
 * @property string|null $code
 * @property array<array-key, mixed>|null $settings
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\AcademyCompany $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PdfTemplate> $pdfTemplates
 * @property-read int|null $pdf_templates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademyUnit whereUpdatedAt($value)
 */
	class AcademyUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $active_rest_routine_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\ActiveRestRoutine $routine
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite whereActiveRestRoutineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestFavorite whereUserId($value)
 */
	class ActiveRestFavorite extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $active_rest_routine_id
 * @property int|null $duration_spent
 * @property int|null $feedback_score
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereActiveRestRoutineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereDurationSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereFeedbackScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestLog whereUserId($value)
 */
	class ActiveRestLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $category
 * @property string $duration
 * @property string $intensity
 * @property string $recommended_level
 * @property string|null $thumbnail
 * @property string|null $guide_image
 * @property string|null $video_id
 * @property string $benefit
 * @property bool $is_premium
 * @property array<array-key, mixed> $exercises
 * @property array<array-key, mixed> $execution_steps
 * @property array<array-key, mixed>|null $tips
 * @property array<array-key, mixed>|null $common_errors
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereBenefit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereCommonErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereExecutionSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereExercises($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereGuideImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereIntensity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereIsPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereRecommendedLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActiveRestRoutine whereVideoId($value)
 */
	class ActiveRestRoutine extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $admin_user_id
 * @property int $clinic_id
 * @property string $motivo_acesso
 * @property string|null $descricao
 * @property \Illuminate\Support\Carbon $data_hora_entrada
 * @property \Illuminate\Support\Carbon|null $data_hora_saida
 * @property string|null $ip
 * @property string|null $duracao_acesso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $adminUser
 * @property-read \App\Models\Clinic $clinic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereDataHoraEntrada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereDataHoraSaida($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereDescricao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereDuracaoAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereMotivoAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminClinicAccessLog whereUpdatedAt($value)
 */
	class AdminClinicAccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $table_name
 * @property string $model_class
 * @property string|null $description
 * @property string $icon
 * @property string $category
 * @property bool $is_active
 * @property int $sort_order
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdminField> $fields
 * @property-read int|null $fields_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereModelClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminEntity withoutTrashed()
 */
	class AdminEntity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $admin_entity_id
 * @property string $name
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property bool $is_readonly
 * @property bool $is_searchable
 * @property bool $is_filterable
 * @property bool $is_sortable
 * @property bool $is_visible_list
 * @property bool $is_visible_form
 * @property string|null $default_value
 * @property string|null $placeholder
 * @property string|null $help_text
 * @property string|null $validation_rules
 * @property array<array-key, mixed>|null $options
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminEntity|null $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereAdminEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereHelpText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsFilterable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsReadonly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsSearchable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsSortable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsVisibleForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereIsVisibleList($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminField whereValidationRules($value)
 */
	class AdminField extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $payload
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminLog whereUserId($value)
 */
	class AdminLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $label
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereValue($value)
 */
	class AdminSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSetting whereValue($value)
 */
	class AgendaSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $credits
 * @property numeric $price
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPackage whereUpdatedAt($value)
 */
	class AiCreditPackage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $package_name
 * @property int $credits_amount
 * @property numeric $price
 * @property string $payment_status
 * @property string|null $payment_method
 * @property string|null $payment_id
 * @property string|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereCreditsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog wherePackageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditPurchaseLog whereUserId($value)
 */
	class AiCreditPurchaseLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int $credits
 * @property int $balance_before
 * @property int $balance_after
 * @property string|null $feature_code
 * @property string|null $reference_id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereBalanceBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereFeatureCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditTransaction whereUserId($value)
 */
	class AiCreditTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $action_type
 * @property int $credits_consumed
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $response_cache_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereCreditsConsumed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereResponseCacheKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditUsageLog whereUserId($value)
 */
	class AiCreditUsageLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $balance
 * @property int $monthly_allowance
 * @property int $extra_credits
 * @property \Illuminate\Support\Carbon|null $renewal_date
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereExtraCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereMonthlyAllowance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereRenewalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiCreditWallet whereUserId($value)
 */
	class AiCreditWallet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $feature_code
 * @property string $feature_name
 * @property int $credits_required
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereCreditsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereFeatureCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereFeatureName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiFeatureCost whereUpdatedAt($value)
 */
	class AiFeatureCost extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $feature
 * @property int $credits_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereCreditsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiUsage whereUserId($value)
 */
	class AiUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $content
 * @property string $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereUpdatedAt($value)
 */
	class Announcement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $request_id
 * @property int|null $user_id
 * @property int|null $token_id
 * @property string $method
 * @property string $path
 * @property int|null $status_code
 * @property int|null $duration_ms
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereDurationMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiAccessLog whereUserId($value)
 */
	class ApiAccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $base_url
 * @property string|null $api_key
 * @property string|null $secret_key
 * @property int $timeout
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $type_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereBaseUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegration whereUpdatedAt($value)
 */
	class ApiIntegration extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $api_name
 * @property string $endpoint
 * @property int $status_code
 * @property int|null $response_time_ms
 * @property array<array-key, mixed>|null $request_payload
 * @property array<array-key, mixed>|null $response_payload
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereApiName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereRequestPayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereResponsePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereResponseTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiIntegrationLog whereUpdatedAt($value)
 */
	class ApiIntegrationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $event_type
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppBannerMetric whereUserId($value)
 */
	class AppBannerMetric extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $category
 * @property string|null $description
 * @property bool $is_active
 * @property bool $show_lock
 * @property bool $show_badge
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeatureLimit> $limits
 * @property-read int|null $limits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereShowBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereShowLock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppFeature whereUpdatedAt($value)
 */
	class AppFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $user_id
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppLaunchLead whereUserId($value)
 */
	class AppLaunchLead extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int|null $professional_id
 * @property \Illuminate\Support\Carbon $requested_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereRequestedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppointmentWaitlist whereUpdatedAt($value)
 */
	class AppointmentWaitlist extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $action
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $email
 * @property string $event
 * @property string $guard
 * @property bool $success
 * @property string|null $ip
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereGuard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthAuditLog whereUserId($value)
 */
	class AuthAuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $modulo
 * @property string|null $categoria
 * @property string $tipo_item
 * @property string $titulo
 * @property string|null $descricao
 * @property string|null $pergunta
 * @property string|null $palavras_chave
 * @property string|null $conteudo
 * @property string $origem
 * @property string $visibilidade
 * @property string $status
 * @property string|null $versao
 * @property int $uso_count
 * @property int|null $created_by
 * @property int|null $parent_id
 * @property int $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BibliotecaInteligente> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User|null $creator
 * @property-read BibliotecaInteligente|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereConteudo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereDescricao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereModulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereOrigem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente wherePalavrasChave($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente wherePergunta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereTipoItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereUsoCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereVersao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BibliotecaInteligente whereVisibilidade($value)
 */
	class BibliotecaInteligente extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property int $user_id
 * @property string $photo_path
 * @property string $view_type
 * @property array<array-key, mixed>|null $landmarks
 * @property array<array-key, mixed>|null $metrics
 * @property array<array-key, mixed>|null $ai_summary
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereAiSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereLandmarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAnalysis whereViewType($value)
 */
	class BodyAnalysis extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $academy_company_id
 * @property int|null $clinic_id
 * @property int $user_id
 * @property int|null $professional_id
 * @property float|null $weight_kg
 * @property float|null $bf_percent
 * @property float|null $muscle_percent
 * @property numeric|null $icw_l
 * @property numeric|null $ecw_l
 * @property numeric|null $dry_lean_mass_kg
 * @property numeric|null $body_fat_mass_kg
 * @property numeric|null $segmental_lean_arm_l
 * @property numeric|null $segmental_lean_arm_r
 * @property numeric|null $segmental_lean_leg_l
 * @property numeric|null $segmental_lean_leg_r
 * @property numeric|null $segmental_lean_trunk
 * @property int|null $visceral_fat_level
 * @property int|null $basal_metabolic_rate
 * @property numeric|null $phase_angle
 * @property numeric|null $neck
 * @property numeric|null $chest
 * @property numeric|null $waist
 * @property numeric|null $abdomen
 * @property numeric|null $hips
 * @property numeric|null $bicep_l
 * @property numeric|null $bicep_r
 * @property numeric|null $forearm_l
 * @property numeric|null $forearm_r
 * @property numeric|null $thigh_l
 * @property numeric|null $thigh_r
 * @property numeric|null $calf_l
 * @property numeric|null $calf_r
 * @property string|null $blood_pressure
 * @property int|null $heart_rate
 * @property \Illuminate\Support\Carbon $assessment_date
 * @property string|null $notes
 * @property array<array-key, mixed>|null $ai_suggestions
 * @property string $status
 * @property string $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User|null $professional
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereAbdomen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereAiSuggestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereAssessmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBasalMetabolicRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBfPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBicepL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBicepR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBloodPressure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereBodyFatMassKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereCalfL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereCalfR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereChest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereDryLeanMassKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereEcwL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereForearmL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereForearmR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereHeartRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereHips($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereIcwL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereMusclePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereNeck($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment wherePhaseAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereSegmentalLeanArmL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereSegmentalLeanArmR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereSegmentalLeanLegL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereSegmentalLeanLegR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereSegmentalLeanTrunk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereThighL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereThighR($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereVisceralFatLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereWaist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BodyAssessment whereWeightKg($value)
 */
	class BodyAssessment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $type
 * @property string $message
 * @property string|null $stack
 * @property string|null $url
 * @property string|null $user_agent
 * @property string|null $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientErrorLog whereUserId($value)
 */
	class ClientErrorLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $academy_company_id
 * @property string $name
 * @property string $slug
 * @property string|null $logo_path
 * @property string $primary_color
 * @property string|null $custom_domain
 * @property bool $is_active
 * @property array<array-key, mixed>|null $enabled_modules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $representative_id
 * @property string|null $representative_code_used Código usado na contratação
 * @property numeric $applied_discount_rate Percentual de desconto aplicado
 * @property \Illuminate\Support\Carbon|null $sale_date
 * @property string|null $plan_name
 * @property string $sale_status
 * @property string $commission_type
 * @property numeric $commission_value
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BodyAssessment> $assessments
 * @property-read int|null $assessments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Especialidade> $especialidades
 * @property-read int|null $especialidades_count
 * @property-read string $logo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patient> $patients
 * @property-read int|null $patients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingPlan> $trainingPlans
 * @property-read int|null $training_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereAppliedDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereCommissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereCommissionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereEnabledModules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic wherePlanName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereRepresentativeCodeUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereSaleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereSaleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic whereUpdatedAt($value)
 */
	class Clinic extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property string $step_key
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\AcademyCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereStepKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicOnboardingStep whereUpdatedAt($value)
 */
	class ClinicOnboardingStep extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int $especialidade_id
 * @property string $type
 * @property string $name
 * @property string|null $description
 * @property string|null $objective
 * @property string|null $protocol
 * @property string|null $frequency
 * @property string|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\AcademyCompany $company
 * @property-read \App\Models\Especialidade $specialty
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereEspecialidadeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereObjective($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClinicProtocol whereUpdatedAt($value)
 */
	class ClinicProtocol extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lead_id
 * @property int $plan_id
 * @property numeric $valor
 * @property numeric $desconto
 * @property \Illuminate\Support\Carbon $validade
 * @property string $status
 * @property string $token
 * @property string|null $observacoes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $representative_id
 * @property string|null $clinic_name
 * @property string|null $clinic_cnpj
 * @property string|null $clinic_city
 * @property string|null $clinic_state
 * @property string|null $clinic_phone
 * @property string|null $clinic_contact
 * @property int|null $clinic_id
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read mixed $valor_final
 * @property-read \App\Models\Lead|null $lead
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\ReferralCode|null $referralCode
 * @property-read \App\Models\User|null $representative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereClinicState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereDesconto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereObservacoes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereValidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommercialProposal whereValor($value)
 */
	class CommercialProposal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $representative_id
 * @property int|null $clinic_id
 * @property int $user_id Usuário que realizou o pagamento
 * @property int|null $payment_id
 * @property int|null $subscription_id
 * @property numeric $base_amount Valor base do pagamento
 * @property string $commission_type
 * @property numeric $commission_rate Taxa aplicada no momento
 * @property numeric $commission_amount Valor final da comissão
 * @property numeric $paid_amount
 * @property numeric $pending_amount
 * @property string $status PENDENTE, DISPONIVEL, PAGO, CANCELADO
 * @property \Illuminate\Support\Carbon|null $available_at Data em que a comissão ficará disponível para saque
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User $representative
 * @property-read \App\Models\Subscription|null $subscription
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WithdrawalRequest> $withdrawalRequests
 * @property-read int|null $withdrawal_requests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereAvailableAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereBaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereCommissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission wherePendingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Commission withoutTrashed()
 */
	class Commission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_private
 * @property bool $allow_self_join
 * @property bool $is_active
 * @property bool $can_members_send_messages
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $moderators
 * @property-read int|null $moderators_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $pendingMembers
 * @property-read int|null $pending_members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereAllowSelfJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereCanMembersSendMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunicationGroup whereUpdatedAt($value)
 */
	class CommunicationGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read CommunityComment|null $parent
 * @property-read \App\Models\CommunityPost|null $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityReaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommunityComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereUserId($value)
 */
	class CommunityComment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $academy_company_id
 * @property string|null $content
 * @property string $status
 * @property string $visibility
 * @property string|null $activity_status
 * @property array<array-key, mixed>|null $hashtags
 * @property bool $is_pinned
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityPostMedia> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityReaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost clinic($clinicId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereActivityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereHashtags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost withoutTrashed()
 */
	class CommunityPost extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_id
 * @property string $file_path
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $url
 * @property-read \App\Models\CommunityPost|null $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostMedia whereUpdatedAt($value)
 */
	class CommunityPostMedia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $reactable_type
 * @property int $reactable_id
 * @property int $user_id
 * @property string $emoji
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reactable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereReactableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereReactableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReaction whereUserId($value)
 */
	class CommunityReaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $reason
 * @property string|null $details
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CommunityPost|null $post
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityReport whereUserId($value)
 */
	class CommunityReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $path
 * @property string $category
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunitySticker whereUpdatedAt($value)
 */
	class CommunitySticker extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $empresa_id
 * @property string $nome_provedor
 * @property string $tipo_envio
 * @property string $preset
 * @property string|null $smtp_host
 * @property int $smtp_porta
 * @property string|null $smtp_usuario
 * @property string|null $smtp_senha
 * @property string $criptografia
 * @property string|null $email_remetente
 * @property string|null $nome_remetente
 * @property int $timeout
 * @property int $limite_envio_por_hora
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $empresa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereCriptografia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereEmailRemetente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereEmpresaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereLimiteEnvioPorHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereNomeProvedor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereNomeRemetente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail wherePreset($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereSmtpHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereSmtpPorta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereSmtpSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereSmtpUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereTipoEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfiguracaoEmail whereUpdatedAt($value)
 */
	class ConfiguracaoEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lead_id
 * @property int|null $proposal_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property string $content
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $representative_id
 * @property-read \App\Models\Lead|null $lead
 * @property-read \App\Models\CommercialProposal|null $proposal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUpdatedAt($value)
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_one_id
 * @property int|null $user_two_id
 * @property string $tipo
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User $userOne
 * @property-read \App\Models\User|null $userTwo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUserOneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUserTwoId($value)
 */
	class Conversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property int $professional_id
 * @property int|null $patient_id
 * @property string|null $code
 * @property string $discount_type
 * @property numeric $discount_value
 * @property \Illuminate\Support\Carbon $expiration_date
 * @property int $max_uses
 * @property int $used_count
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $patient
 * @property-read \App\Models\User $professional
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponUsage> $usages
 * @property-read int|null $usages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereMaxUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUsedCount($value)
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $coupon_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon $coupon
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CouponUsage whereUserId($value)
 */
	class CouponUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $quantidade
 * @property numeric $valor
 * @property string $status
 * @property string|null $gateway
 * @property string|null $payment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereQuantidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoCompra whereValor($value)
 */
	class CreditoCompra extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property int $quantidade
 * @property numeric $valor
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereQuantidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditoPacote whereValor($value)
 */
	class CreditoPacote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $version
 * @property string $environment
 * @property string $status
 * @property string|null $homolog_status
 * @property string $impact_level
 * @property string $risk_level
 * @property int|null $deployed_by
 * @property string|null $git_branch
 * @property string|null $git_commit
 * @property string|null $notes
 * @property string|null $failure_message
 * @property int|null $files_changed_count
 * @property \Illuminate\Support\Carbon|null $deployed_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property bool $is_current
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $deployer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereDeployedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereDeployedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereFailureMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereFilesChangedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereGitBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereGitCommit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereHomologStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereImpactLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeployRelease whereVersion($value)
 */
	class DeployRelease extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $platform
 * @property string|null $device_name
 * @property string|null $app_version
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUserId($value)
 */
	class DeviceToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $empresa_id
 * @property string $tipo
 * @property string $nome_template
 * @property string $assunto
 * @property string $mensagem
 * @property string|null $variaveis
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $empresa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereAssunto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereEmpresaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereMensagem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereNomeTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereVariaveis($value)
 */
	class EmailTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string $categoria
 * @property string $client_term Termo usado para clientes desta especialidade: Paciente, Aluno, Cliente, etc.
 * @property array<array-key, mixed>|null $enabled_modules Módulos habilitados: [\"treinos\", \"dietas\", \"prontuarios\"]
 * @property string|null $icone
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $profession_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clinic> $clinics
 * @property-read int|null $clinics_count
 * @property-read \App\Models\Profession|null $profession
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfessionalProfile> $professionals
 * @property-read int|null $professionals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereClientTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereEnabledModules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereIcone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Especialidade whereUpdatedAt($value)
 */
	class Especialidade extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $photo_path
 * @property string $type
 * @property string $registered_date
 * @property numeric|null $weight_kg
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereRegisteredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvolutionPhoto whereWeightKg($value)
 */
	class EvolutionPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $muscle_group
 * @property string|null $equipment
 * @property string $difficulty
 * @property string|null $instructions
 * @property array<array-key, mixed>|null $tips
 * @property array<array-key, mixed>|null $common_mistakes
 * @property string|null $video_url
 * @property string $video_type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Muscle> $muscles
 * @property-read int|null $muscles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereCommonMistakes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereEquipment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereMuscleGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereVideoType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCatalog whereVideoUrl($value)
 */
	class ExerciseCatalog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon $entry_date
 * @property string $activity_type
 * @property int $duration_min
 * @property int|null $rpe
 * @property int|null $rest_default
 * @property int|null $calories_burned
 * @property array<array-key, mixed>|null $sets_data
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereCaloriesBurned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereDurationMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereRestDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereRpe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereSetsData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereUserId($value)
 */
	class ExerciseEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $training_plan_exercise_id
 * @property int $set_number
 * @property int|null $reps_target
 * @property numeric|null $weight_target
 * @property int $rest_seconds
 * @property int|null $rpe_target
 * @property string|null $cadence
 * @property string $set_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TrainingPlanExercise $trainingPlanExercise
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereCadence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereRepsTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereRestSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereRpeTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereSetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereSetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereTrainingPlanExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseSet whereWeightTarget($value)
 */
	class ExerciseSet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $plan_id
 * @property int $feature_id
 * @property int $limit_value
 * @property string $limit_type
 * @property string $action_type
 * @property string|null $custom_popup_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AppFeature $feature
 * @property-read \App\Models\Plan|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereCustomPopupText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereLimitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereLimitValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureLimit whereUpdatedAt($value)
 */
	class FeatureLimit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $academy_company_id
 * @property string $action
 * @property numeric|null $amount
 * @property string|null $status_before
 * @property string|null $status_after
 * @property string|null $transaction_id
 * @property string $origin
 * @property string|null $ip_address
 * @property string|null $observation
 * @property array<array-key, mixed>|null $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereObservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereStatusAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereStatusBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialLog whereUserId($value)
 */
	class FinancialLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $brand
 * @property string|null $barcode
 * @property numeric $base_amount
 * @property string $unit
 * @property string $data_source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nutrient> $nutrients
 * @property-read int|null $nutrients_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereBaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereDataSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Food whereUpdatedAt($value)
 */
	class Food extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon $entry_date
 * @property string $meal_type
 * @property string $food_name
 * @property float|null $amount
 * @property string $unit
 * @property int $calories
 * @property float $protein_g
 * @property float $carbs_g
 * @property float $fat_g
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry inDateRange($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereCarbsG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereFatG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereFoodName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereMealType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereProteinG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FoodEntry whereUserId($value)
 */
	class FoodEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $document_id
 * @property int $user_id
 * @property string $type
 * @property int $version
 * @property string $hash
 * @property \Illuminate\Support\Carbon $generated_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedReport whereVersion($value)
 */
	class GeneratedReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property float $target_value
 * @property float $current_value
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $professional_id
 * @property-read float $progress
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereCurrentValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereTargetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Goal whereUpdatedAt($value)
 */
	class Goal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $severity
 * @property string $message
 * @property int $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthAlert whereUserId($value)
 */
	class HealthAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property numeric $value
 * @property string|null $unit
 * @property string $source
 * @property \Illuminate\Support\Carbon $recorded_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthMetric whereValue($value)
 */
	class HealthMetric extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int $professional_id
 * @property string $data_type
 * @property string $access_level
 * @property bool $is_confidential
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereAccessLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereIsConfidential($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthPermission whereUpdatedAt($value)
 */
	class HealthPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $academy_company_id
 * @property int|null $academy_unit_id
 * @property int|null $user_id
 * @property int|null $pdf_template_id
 * @property \App\Enums\PdfDocumentType $document_type
 * @property string|null $related_document_type
 * @property int|null $related_document_id
 * @property string|null $numero_oficial
 * @property string $nome_arquivo
 * @property string $caminho_arquivo
 * @property string $codigo_validacao
 * @property \App\Enums\PdfValidationStatus $validation_status
 * @property \Illuminate\Support\Carbon $issued_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string $generation_status
 * @property array<array-key, mixed>|null $source_variables
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\AcademyCompany|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PdfDeliveryLog> $deliveryLogs
 * @property-read int|null $delivery_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PdfSignature> $signatures
 * @property-read int|null $signatures_count
 * @property-read \App\Models\PdfTemplate|null $template
 * @property-read \App\Models\AcademyUnit|null $unit
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf forCompany(?int $companyId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereAcademyUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereCaminhoArquivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereCodigoValidacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereGenerationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereNomeArquivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereNumeroOficial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf wherePdfTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereRelatedDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereRelatedDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereSourceVariables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricoPdf whereValidationStatus($value)
 */
	class HistoricoPdf extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sender_id
 * @property int $recipient_id
 * @property string $subject
 * @property string $content
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property string|null $excluded_at_sender
 * @property string|null $excluded_at_receiver
 * @property string $status
 * @property int|null $parent_id
 * @property bool $is_system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $recipient
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereExcludedAtReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereExcludedAtSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalEmail whereUpdatedAt($value)
 */
	class InternalEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property string $priority
 * @property int $user_id
 * @property int|null $assigned_to
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int $position
 * @property array<array-key, mixed>|null $labels
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KanbanTask withoutTrashed()
 */
	class KanbanTask extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $titulo
 * @property string $slug
 * @property string $conteudo
 * @property int $categoria_id
 * @property string $tipo_usuario
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KnowledgeCategory $category
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle forUserType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereConteudo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereTipoUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeArticle whereUpdatedAt($value)
 */
	class KnowledgeArticle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property string|null $descricao
 * @property string $tipo_usuario
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KnowledgeArticle> $articles
 * @property-read int|null $articles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory forUserType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereDescricao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereTipoUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeCategory whereUpdatedAt($value)
 */
	class KnowledgeCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $representative_id
 * @property string $nome
 * @property string|null $email
 * @property string|null $telefone
 * @property string|null $empresa
 * @property string|null $origem
 * @property int|null $responsavel_id
 * @property int|null $converted_user_id
 * @property string $status
 * @property string|null $observacao
 * @property numeric|null $valor_estimado
 * @property \Illuminate\Support\Carbon|null $previsao_fechamento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $converted_company_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\AcademyCompany|null $convertedCompany
 * @property-read mixed $ai_next_action
 * @property-read mixed $ai_probability
 * @property-read mixed $ai_summary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LeadInteraction> $interactions
 * @property-read int|null $interactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnboardingStep> $onboardingSteps
 * @property-read int|null $onboarding_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommercialProposal> $proposals
 * @property-read int|null $proposals_count
 * @property-read \App\Models\User|null $responsavel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereConvertedCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereConvertedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereObservacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereOrigem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead wherePrevisaoFechamento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereResponsavelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereValorEstimado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead withoutTrashed()
 */
	class Lead extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lead_id
 * @property int $user_id
 * @property string $tipo_contato
 * @property string $descricao
 * @property \Illuminate\Support\Carbon $data_contato
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lead|null $lead
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereDataContato($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereDescricao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereTipoContato($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadInteraction whereUserId($value)
 */
	class LeadInteraction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property int $training_plan_exercise_id
 * @property int $exercise_id
 * @property \Illuminate\Support\Carbon $log_date
 * @property int $set_number
 * @property int $reps_done
 * @property int $to_failure
 * @property numeric $weight_kg
 * @property numeric|null $one_rm
 * @property int|null $rpe
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\ExerciseCatalog $catalogExercise
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\TrainingPlanExercise $trainingPlanExercise
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereLogDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereOneRm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereRepsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereRpe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereSetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereToFailure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereTrainingPlanExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadLog whereWeightKg($value)
 */
	class LoadLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $empresa_id
 * @property int|null $usuario_id
 * @property string|null $tipo_envio
 * @property string $email_destino
 * @property string|null $assunto
 * @property string|null $mensagem
 * @property string $status
 * @property string|null $erro
 * @property string|null $ip
 * @property \Illuminate\Support\Carbon $data_envio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $empresa
 * @property-read \App\Models\User|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereAssunto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereDataEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereEmailDestino($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereEmpresaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereErro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereMensagem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereTipoEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogEnvioEmail whereUsuarioId($value)
 */
	class LogEnvioEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $subtitle
 * @property string|null $description
 * @property string|null $image_desktop
 * @property string|null $image_mobile
 * @property string $background_color
 * @property string|null $icon
 * @property string|null $primary_button_text
 * @property string|null $primary_button_link
 * @property string|null $secondary_button_text
 * @property string|null $secondary_button_link
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property int $priority
 * @property bool $is_active
 * @property bool $allow_dismiss
 * @property bool $dont_show_again_option
 * @property string $display_type
 * @property int $frequency_days
 * @property array<array-key, mixed>|null $segmentation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketingBannerClick> $clicks
 * @property-read int|null $clicks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketingBannerDismissal> $dismissals
 * @property-read int|null $dismissals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketingBannerTarget> $targets
 * @property-read int|null $targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketingBannerView> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereAllowDismiss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereDisplayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereDontShowAgainOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereFrequencyDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereImageDesktop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereImageMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner wherePrimaryButtonLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner wherePrimaryButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereSecondaryButtonLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereSecondaryButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereSegmentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBanner withoutTrashed()
 */
	class MarketingBanner extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $banner_id
 * @property int|null $user_id
 * @property string $button_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\MarketingBanner|null $banner
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereButtonType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerClick whereUserId($value)
 */
	class MarketingBannerClick extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $banner_id
 * @property int|null $user_id
 * @property int $dont_show_again
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\MarketingBanner|null $banner
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereDontShowAgain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerDismissal whereUserId($value)
 */
	class MarketingBannerDismissal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $banner_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MarketingBanner|null $banner
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerTarget whereUpdatedAt($value)
 */
	class MarketingBannerTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $banner_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\MarketingBanner|null $banner
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketingBannerView whereUserId($value)
 */
	class MarketingBannerView extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MealTemplateItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplate whereUserId($value)
 */
	class MealTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $meal_template_id
 * @property string $meal_type
 * @property string $food_name
 * @property int $calories
 * @property float $protein_g
 * @property float $carbs_g
 * @property float $fat_g
 * @property int $position
 * @property-read \App\Models\MealTemplate $mealTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereCarbsG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereFatG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereFoodName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereMealTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereMealType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MealTemplateItem whereProteinG($value)
 */
	class MealTemplateItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int $patient_id
 * @property int $professional_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $reason
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string|null $period
 * @property string|null $observations
 * @property string|null $pdf_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalCertificate whereUpdatedAt($value)
 */
	class MedicalCertificate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int $patient_id
 * @property int $professional_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $type
 * @property string|null $chief_complaint
 * @property string|null $assessment
 * @property string|null $diagnosis
 * @property string|null $conduct
 * @property string|null $observations
 * @property array<array-key, mixed>|null $attachments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereAssessment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereChiefComplaint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereConduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereDiagnosis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalEvolution whereUpdatedAt($value)
 */
	class MedicalEvolution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int $user_id
 * @property string $action_type
 * @property string $module
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalHistory whereUserId($value)
 */
	class MedicalHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int $patient_id
 * @property int $professional_id
 * @property int|null $especialidade_id
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $objective
 * @property string|null $protocol
 * @property string $medicine
 * @property string|null $dosage
 * @property string|null $frequency
 * @property string|null $duration
 * @property string|null $observations
 * @property string|null $pdf_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\AcademyCompany|null $company
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @property-read \App\Models\Especialidade|null $specialty
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereEspecialidadeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereMedicine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereObjective($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalPrescription whereUpdatedAt($value)
 */
	class MedicalPrescription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int $patient_id
 * @property int $professional_id
 * @property string $title
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $description
 * @property string|null $conclusion
 * @property string|null $observations
 * @property string|null $pdf_path
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereConclusion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalReport whereUpdatedAt($value)
 */
	class MedicalReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $label
 * @property string $route
 * @property string $match_mode
 * @property bool $is_container
 * @property string|null $icon
 * @property int $order
 * @property bool $is_required
 * @property string $portal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Menu> $children
 * @property-read int|null $children_count
 * @property-read Menu|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserMenuPreference> $preferences
 * @property-read int|null $preferences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleMenuPermission> $roleMenuPermissions
 * @property-read int|null $role_menu_permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIsContainer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereMatchMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $role_id
 * @property int|null $academy_company_id
 * @property string $action
 * @property array<array-key, mixed>|null $payload
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Role|null $profile
 * @property-read \App\Models\Role|null $role
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuPermissionAuditLog whereUserId($value)
 */
	class MenuPermissionAuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $mp_payment_id
 * @property int $user_id
 * @property string $plan_code
 * @property numeric $transaction_amount
 * @property string $currency_id
 * @property string $created_at
 * @property int|null $coupon_id
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereMpPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit wherePlanCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereTransactionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoCredit whereUserId($value)
 */
	class MercadoPagoCredit extends \Eloquent {}
}

namespace App\Models{
/**
 * Representa uma assinatura recorrente do Mercado Pago.
 *
 * Tabela correspondente: mercadopago_subscriptions
 *
 * @property string $mp_preapproval_id
 * @property int $user_id
 * @property string $plan_code
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $coupon_id
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereMpPreapprovalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription wherePlanCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MercadoPagoSubscription whereUserId($value)
 */
	class MercadoPagoSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property string $content
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property int $mood_score Humor de 0 a 10
 * @property int|null $energy_level Energia de 0 a 10
 * @property float|null $sleep_hours Horas de sono
 * @property int|null $stress_level Estresse de 0 a 10
 * @property string|null $notes Notas livres do paciente
 * @property bool $is_confidential Verdadeiro se for nota do profissional — nunca expor ao paciente
 * @property \Illuminate\Support\Carbon $logged_at Data do registro
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $professional
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog visibleToPatient()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereEnergyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereIsConfidential($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereLoggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereMoodScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereSleepHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereStressLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MoodLog whereUserId($value)
 */
	class MoodLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $group_id
 * @property string $name
 * @property string $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExerciseCatalog> $exercises
 * @property-read int|null $exercises_count
 * @property-read \App\Models\MuscleGroup $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Muscle whereUpdatedAt($value)
 */
	class Muscle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $region
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Muscle> $muscles
 * @property-read int|null $muscles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MuscleGroup whereUpdatedAt($value)
 */
	class MuscleGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $unit
 * @property int $is_main
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Food> $foods
 * @property-read int|null $foods_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nutrient whereUpdatedAt($value)
 */
	class Nutrient extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $status
 * @property int $max_simultaneous_chats
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniCompany $company
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereMaxSimultaneousChats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniAgent whereUserId($value)
 */
	class OmniAgent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string|null $whatsapp_phone
 * @property int $is_active
 * @property array<array-key, mixed>|null $business_hours
 * @property string|null $out_of_office_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniBotStep> $steps
 * @property-read int|null $steps_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereBusinessHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereOutOfOfficeMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBot whereWhatsappPhone($value)
 */
	class OmniBot extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $step_id
 * @property string $trigger_value
 * @property string $label
 * @property int $destination_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereDestinationStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereTriggerValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotOption whereUpdatedAt($value)
 */
	class OmniBotOption extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $bot_id
 * @property string $label
 * @property string $type
 * @property string $content
 * @property int $is_start
 * @property int|null $next_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniBotOption> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereBotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereIsStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereNextStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBotStep whereUpdatedAt($value)
 */
	class OmniBotStep extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property int $day_of_week
 * @property string $open_time
 * @property string $close_time
 * @property bool $is_closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereCloseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereIsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereOpenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniBusinessHour whereUpdatedAt($value)
 */
	class OmniBusinessHour extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $type
 * @property string $name
 * @property bool $is_active
 * @property array<array-key, mixed>|null $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChannel whereUpdatedAt($value)
 */
	class OmniChannel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $trigger_type
 * @property string|null $pattern
 * @property string $response
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule wherePattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereTriggerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniChatbotRule whereUpdatedAt($value)
 */
	class OmniChatbotRule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $logo
 * @property bool $is_active
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniAgent> $agents
 * @property-read int|null $agents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniBusinessHour> $businessHours
 * @property-read int|null $business_hours_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniChannel> $channels
 * @property-read int|null $channels_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniChatbotRule> $chatbotRules
 * @property-read int|null $chatbot_rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniQueue> $queues
 * @property-read int|null $queues_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniCompany whereUpdatedAt($value)
 */
	class OmniCompany extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property int|null $bot_id
 * @property int $channel_id
 * @property string $customer_external_id
 * @property string|null $customer_name
 * @property int|null $agent_id
 * @property int|null $queue_id
 * @property string $status
 * @property int|null $current_bot_step_id
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniAgent|null $agent
 * @property-read \App\Models\OmniChannel $channel
 * @property-read \App\Models\OmniCompany $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OmniMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\OmniQueue|null $queue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereBotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereCurrentBotStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereCustomerExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereQueueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniConversation whereUpdatedAt($value)
 */
	class OmniConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_id
 * @property string $sender_type
 * @property int|null $sender_id
 * @property string $content
 * @property string $content_type
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniConversation $conversation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniMessage whereUpdatedAt($value)
 */
	class OmniMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OmniCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OmniQueue whereUpdatedAt($value)
 */
	class OmniQueue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $lead_id
 * @property string $title
 * @property string|null $description
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lead|null $lead
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingStep whereUpdatedAt($value)
 */
	class OnboardingStep extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $type
 * @property int|null $owner_id
 * @property string|null $tax_id
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patient> $patients
 * @property-read int|null $patients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUuid($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property array<array-key, mixed> $pain_points
 * @property int $eva_level
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $assessment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User|null $professional
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereAssessmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereEvaLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord wherePainPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PainRecord whereUserId($value)
 */
	class PainRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property string $uuid
 * @property string $name
 * @property string $cpf
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $gender
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalEvolution> $evolutions
 * @property-read int|null $evolutions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUuid($value)
 */
	class Patient extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property string $type
 * @property string $token_hash
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereTokenHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientAccessToken whereUsedAt($value)
 */
	class PatientAccessToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property int $patient_id
 * @property int $professional_id
 * @property string $title
 * @property string $category
 * @property string $file_path
 * @property string|null $file_type
 * @property int|null $file_size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientDocument whereUpdatedAt($value)
 */
	class PatientDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property string $module_key
 * @property bool $is_enabled
 * @property bool $auto_discovered
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereAutoDiscovered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereModuleKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientModule whereUpdatedAt($value)
 */
	class PatientModule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property int $patient_id
 * @property int $professional_id
 * @property string|null $diagnosis
 * @property string|null $objectives
 * @property string|null $care_plan
 * @property string|null $orientations
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereCarePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereDiagnosis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereObjectives($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereOrientations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PatientTreatmentPlan whereUpdatedAt($value)
 */
	class PatientTreatmentPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $subscription_id
 * @property string $gateway
 * @property string $gateway_id
 * @property numeric $amount
 * @property numeric $fee_amount Taxa retida pela plataforma
 * @property numeric $net_amount Valor líquido após taxas
 * @property string $currency
 * @property string $status
 * @property array<array-key, mixed>|null $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Subscription|null $subscription
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFeeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withoutTrashed()
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $gateway
 * @property string|null $client_id
 * @property string|null $client_secret
 * @property string $environment
 * @property string|null $public_key
 * @property string|null $access_token
 * @property string|null $webhook_secret
 * @property string|null $webhook_url
 * @property int $timeout
 * @property int $priority
 * @property bool $enable_credit_card
 * @property bool $enable_pix
 * @property bool $enable_boleto
 * @property int $boleto_expiration_days
 * @property int $pix_expiration_minutes
 * @property string $status
 * @property numeric $penalty_percent
 * @property numeric $interest_percent
 * @property numeric $discount_percent
 * @property int $tolerance_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereBoletoExpirationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereEnableBoleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereEnableCreditCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereEnablePix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereInterestPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting wherePenaltyPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting wherePixExpirationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereToleranceDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereWebhookSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentSetting whereWebhookUrl($value)
 */
	class PaymentSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $gateway
 * @property string|null $event_type
 * @property string|null $external_id
 * @property array<array-key, mixed> $payload
 * @property array<array-key, mixed>|null $headers
 * @property int|null $status_code
 * @property string|null $status_message
 * @property float|null $processing_time
 * @property string|null $error
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereProcessingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereStatusMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentWebhookLog whereUpdatedAt($value)
 */
	class PaymentWebhookLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historico_pdf_id
 * @property \App\Enums\PdfDeliveryChannel $channel
 * @property string|null $email_destinatario
 * @property string|null $telefone_destinatario
 * @property \Illuminate\Support\Carbon|null $data_envio
 * @property string $status_envio
 * @property int $tentativas
 * @property string|null $ultimo_erro
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\HistoricoPdf $historicoPdf
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereDataEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereEmailDestinatario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereHistoricoPdfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereStatusEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereTelefoneDestinatario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereTentativas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereUltimoErro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfDeliveryLog whereUpdatedAt($value)
 */
	class PdfDeliveryLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $pdf_template_id
 * @property int|null $historico_pdf_id
 * @property string $document_type
 * @property string|null $template_name
 * @property string $action
 * @property string $filename
 * @property string $status
 * @property string|null $error_message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\HistoricoPdf|null $historicoPdf
 * @property-read \App\Models\PdfTemplate|null $template
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereHistoricoPdfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog wherePdfTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfGenerationLog whereUserId($value)
 */
	class PdfGenerationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property string $tipo_documento
 * @property int $ano
 * @property int $sequencia_atual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\AcademyCompany $company
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereAno($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereSequenciaAtual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereTipoDocumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfNumberSequence whereUpdatedAt($value)
 */
	class PdfNumberSequence extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historico_pdf_id
 * @property int|null $user_id
 * @property string|null $signer_name
 * @property \App\Enums\PdfSignatureRole $tipo_assinatura
 * @property \App\Enums\PdfSignatureMode $modo
 * @property string $imagem_assinatura
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon $data_assinatura
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\HistoricoPdf $historicoPdf
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereDataAssinatura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereHistoricoPdfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereImagemAssinatura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereModo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereSignerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereTipoAssinatura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignature whereUserId($value)
 */
	class PdfSignature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $historico_pdf_id
 * @property int|null $user_id
 * @property string $evento
 * @property string|null $detalhe
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\HistoricoPdf $historicoPdf
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereDetalhe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereEvento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereHistoricoPdfId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfSignatureAuditLog whereUserId($value)
 */
	class PdfSignatureAuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $academy_company_id
 * @property int|null $academy_unit_id
 * @property string $name
 * @property \App\Enums\PdfDocumentType $document_type
 * @property string|null $description
 * @property string $html_body
 * @property string|null $css_extra
 * @property string|null $logo_path
 * @property string $primary_color
 * @property string|null $secondary_color
 * @property string|null $accent_color
 * @property string|null $footer_html
 * @property bool $auto_email_enabled
 * @property array<array-key, mixed>|null $auto_email_recipients
 * @property bool $auto_whatsapp_enabled
 * @property string|null $whatsapp_message_template
 * @property array<array-key, mixed>|null $auto_whatsapp_recipients
 * @property int|null $duplicated_from_id
 * @property bool $is_active
 * @property bool $is_default
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\AcademyCompany|null $company
 * @property-read PdfTemplate|null $duplicatedFrom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PdfGenerationLog> $generationLogs
 * @property-read int|null $generation_logs_count
 * @property-read \App\Models\AcademyUnit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate forTenant(?int $academyCompanyId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate forType(\App\Enums\PdfDocumentType|string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate forUnit(?int $unitId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAcademyUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAutoEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAutoEmailRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAutoWhatsappEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereAutoWhatsappRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereCssExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereDuplicatedFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereFooterHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereHtmlBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PdfTemplate whereWhatsappMessageTemplate($value)
 */
	class PdfTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plan> $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property string $file_path
 * @property string|null $category
 * @property string|null $description
 * @property string $plan_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo wherePlanType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereUpdatedAt($value)
 */
	class Photo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property bool $is_corporate
 * @property numeric $price
 * @property numeric $commission_rate Percentual de comissão (ex: 10.00 para 10%)
 * @property int $ai_credits
 * @property int $max_students
 * @property int $max_workouts
 * @property int $max_diets
 * @property int $max_assessments
 * @property int $max_patients
 * @property int $max_professionals
 * @property int $is_active
 * @property int $trial_days
 * @property int $max_exercises_per_workout
 * @property numeric|null $price_per_professional
 * @property int $min_professionals
 * @property string|null $features
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlanFeature> $planFeatures
 * @property-read int|null $plan_features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereAiCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsCorporate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxAssessments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxDiets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxExercisesPerWorkout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxPatients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxProfessionals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxWorkouts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMinProfessionals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePricePerProfessional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $plan_id
 * @property string $feature_key
 * @property bool $is_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereFeatureKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereUpdatedAt($value)
 */
	class PlanFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $plan_id
 * @property int $permission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Permission $permission
 * @property-read \App\Models\Plan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanPermission whereUpdatedAt($value)
 */
	class PlanPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $especialidade_id
 * @property int $professional_id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $professional
 * @property-read \App\Models\Especialidade $specialty
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereEspecialidadeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrescriptionTemplate whereUpdatedAt($value)
 */
	class PrescriptionTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Especialidade> $specialties
 * @property-read int|null $specialties_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profession whereUpdatedAt($value)
 */
	class Profession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $professional_id
 * @property int $patient_id
 * @property \Illuminate\Support\Carbon $appointment_at
 * @property string $status
 * @property string|null $service_type
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status_label
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereAppointmentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAppointment whereUpdatedAt($value)
 */
	class ProfessionalAppointment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $professional_id
 * @property int $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalAvailability whereUpdatedAt($value)
 */
	class ProfessionalAvailability extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $clinic_name
 * @property string $primary_color
 * @property string $accent_color
 * @property string|null $logo_path
 * @property string|null $custom_domain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereClinicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalBranding whereUserId($value)
 */
	class ProfessionalBranding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $professional_id
 * @property string $name
 * @property string $type
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfessionalFinanceEntry> $entries
 * @property-read int|null $entries_count
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceCategory whereUpdatedAt($value)
 */
	class ProfessionalFinanceCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $professional_id
 * @property int|null $category_id
 * @property string $description
 * @property numeric $amount
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property string|null $payment_method
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProfessionalFinanceCategory|null $category
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceEntry whereUpdatedAt($value)
 */
	class ProfessionalFinanceEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $professional_id
 * @property numeric $monthly_goal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal whereMonthlyGoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalFinanceGoal whereUpdatedAt($value)
 */
	class ProfessionalFinanceGoal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $patient_type
 * @property string|null $insurance_type
 * @property string|null $insurance_card_number
 * @property string|null $insurance_expiry
 * @property string|null $responsible_legal
 * @property array<array-key, mixed>|null $patient_permissions
 * @property int|null $linked_by
 * @property string|null $linking_ip
 * @property string|null $linking_device
 * @property int $profissional_id
 * @property string $data_cadastro
 * @property string|null $data_fim
 * @property string|null $status
 * @property string|null $motivo_desvinculacao
 * @property bool $is_favorite
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property string|null $main_diagnosis
 * @property string|null $important_notes
 * @property int|null $empresa_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $tracking_status
 * @property string|null $professional_notes_for_patient
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User|null $actor
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereDataCadastro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereDataFim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereEmpresaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereImportantNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereInsuranceCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereInsuranceExpiry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereInsuranceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereLinkedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereLinkingDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereLinkingIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereMainDiagnosis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereMotivoDesvinculacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient wherePatientPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient wherePatientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereProfessionalNotesForPatient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereProfissionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereResponsibleLegal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereTrackingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatient whereUserId($value)
 */
	class ProfessionalPatient extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $patient_id
 * @property int $professional_id
 * @property string $status
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $patient
 * @property-read \App\Models\User $professional
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPatientRequest whereUpdatedAt($value)
 */
	class ProfessionalPatientRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $max_patients
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan whereMaxPatients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalPlan whereUpdatedAt($value)
 */
	class ProfessionalPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_unit_id
 * @property string|null $room
 * @property int $profession_id
 * @property int|null $especialidade_id
 * @property string|null $specialty
 * @property int|null $experience_years
 * @property string|null $education
 * @property string|null $certifications
 * @property string|null $about
 * @property string|null $professional_photo_path
 * @property string|null $offered_services
 * @property array<array-key, mixed>|null $service_types
 * @property numeric|null $consultation_price
 * @property int $appointment_duration
 * @property int $appointment_interval
 * @property string|null $company_name
 * @property string|null $clinic_address
 * @property string|null $clinic_city
 * @property string|null $clinic_state
 * @property array<array-key, mixed>|null $work_days
 * @property string|null $work_start_time
 * @property string|null $work_end_time
 * @property bool $is_public
 * @property bool $use_finance_module
 * @property string|null $internal_permissions
 * @property string $registration_number
 * @property string $council
 * @property string $registration_uf
 * @property \Illuminate\Support\Carbon $registration_expiry_date
 * @property string|null $last_audit_at
 * @property string $audit_status
 * @property string|null $document_path
 * @property string|null $signature_path
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $document_version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Especialidade|null $especialidade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Especialidade> $especialidades
 * @property-read int|null $especialidades_count
 * @property-read string|null $expiry_warning
 * @property-read \App\Models\Profession $profession
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereAcademyUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereAppointmentDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereAppointmentInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereAuditStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereCertifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereClinicAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereClinicCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereClinicState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereConsultationPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereCouncil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereDocumentVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereEspecialidadeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereExperienceYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereInternalPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereLastAuditAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereOfferedServices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereProfessionalPhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereRegistrationExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereRegistrationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereRegistrationUf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereServiceTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereSignaturePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereSpecialty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereUseFinanceModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereWorkDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereWorkEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfessionalProfile whereWorkStartTime($value)
 */
	class ProfessionalProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $entity_type
 * @property string $entity_id
 * @property int $version_number
 * @property array<array-key, mixed> $data
 * @property string|null $notes
 * @property string $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecordVersion whereVersionNumber($value)
 */
	class RecordVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property int $representative_id
 * @property int|null $commercial_proposal_id
 * @property int|null $clinic_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\CommercialProposal|null $commercialProposal
 * @property-read \App\Models\User $representative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCommercialProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReferralCode whereUsedAt($value)
 */
	class ReferralCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id Usuário (admin) que fez a ação
 * @property string $action
 * @property string|null $entity_type
 * @property int|null $entity_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeAudit whereUserId($value)
 */
	class RepresentativeAudit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property numeric $commission_rate Percentual base de comissão
 * @property numeric $max_discount_rate Percentual máximo de desconto que pode conceder
 * @property \Illuminate\Support\Carbon|null $code_expires_at
 * @property int|null $max_code_usages
 * @property int $current_code_usages
 * @property array<array-key, mixed>|null $payment_rules Regras financeiras (JSON ou texto)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereCodeExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereCurrentCodeUsages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereMaxCodeUsages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereMaxDiscountRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile wherePaymentRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepresentativeProfile whereUserId($value)
 */
	class RepresentativeProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketingBanner> $marketingBanners
 * @property-read int|null $marketing_banners_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleMenuPermission> $roleMenuPermissions
 * @property-read int|null $role_menu_permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $menu_id
 * @property bool $pode_visualizar
 * @property bool $pode_criar
 * @property bool $pode_editar
 * @property bool $pode_excluir
 * @property bool $pode_exportar
 * @property bool $pode_imprimir
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Menu $menu
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeCriar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeEditar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeExcluir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeExportar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeImprimir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission wherePodeVisualizar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenuPermission whereUpdatedAt($value)
 */
	class RoleMenuPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $permission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Permission $permission
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission whereUpdatedAt($value)
 */
	class RolePermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $academy_company_id
 * @property int|null $coupon_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\ShopCoupon|null $coupon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopCartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCart whereUserId($value)
 */
	class ShopCart extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShopCart $cart
 * @property-read \App\Models\ShopProduct|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCartItem whereUpdatedAt($value)
 */
	class ShopCartItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $image_path
 * @property string $product_type
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ShopCategory> $children
 * @property-read int|null $children_count
 * @property-read ShopCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopProduct> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCategory whereUpdatedAt($value)
 */
	class ShopCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int $created_by
 * @property string $code
 * @property string|null $description
 * @property string $type
 * @property numeric|null $discount_value
 * @property numeric|null $minimum_order_value
 * @property numeric|null $maximum_discount
 * @property string $applies_to
 * @property array<array-key, mixed>|null $category_ids
 * @property array<array-key, mixed>|null $product_ids
 * @property bool $free_shipping
 * @property int|null $max_uses_total
 * @property int|null $max_uses_per_user
 * @property int $used_count
 * @property bool $is_single_use
 * @property string|null $campaign
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopCouponUsage> $usages
 * @property-read int|null $usages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereAppliesTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereCategoryIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereFreeShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereIsSingleUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereMaxUsesPerUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereMaxUsesTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereMaximumDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereMinimumOrderValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereProductIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCoupon whereUsedCount($value)
 */
	class ShopCoupon extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $coupon_id
 * @property int $order_id
 * @property int $user_id
 * @property numeric $discount_applied
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShopCoupon $coupon
 * @property-read \App\Models\ShopOrder|null $order
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereDiscountApplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCouponUsage whereUserId($value)
 */
	class ShopCouponUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int $user_id
 * @property int|null $coupon_id
 * @property string $order_number
 * @property string $status
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $shipping_amount
 * @property numeric $tax_amount
 * @property numeric $total
 * @property int $points_earned
 * @property numeric $cashback_amount
 * @property string|null $payment_method
 * @property string|null $payment_gateway
 * @property string|null $gateway_payment_id
 * @property string|null $gateway_status
 * @property string|null $shipping_method
 * @property array<array-key, mixed>|null $shipping_address
 * @property string|null $tracking_code
 * @property \Illuminate\Support\Carbon|null $pickup_at
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $cancellation_reason
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\ShopCoupon|null $coupon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCashbackAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereGatewayPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereGatewayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePickupAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereTrackingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder withoutTrashed()
 */
	class ShopOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $vendor_id
 * @property string $product_name
 * @property string|null $product_sku
 * @property string $product_type
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $discount_amount
 * @property numeric $total
 * @property numeric|null $commission_rate
 * @property numeric|null $commission_amount
 * @property string|null $commission_status
 * @property string|null $download_token
 * @property \Illuminate\Support\Carbon|null $download_expires_at
 * @property int $download_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShopOrder|null $order
 * @property-read \App\Models\ShopProduct|null $product
 * @property-read \App\Models\ShopVendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereCommissionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereDownloadExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereDownloadToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereProductSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereVendorId($value)
 */
	class ShopOrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $wallet_id
 * @property int $user_id
 * @property string $type
 * @property int $points
 * @property numeric|null $cashback_amount
 * @property string $description
 * @property string|null $source
 * @property int|null $source_id
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\ShopPointsWallet $wallet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereCashbackAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsTransaction whereWalletId($value)
 */
	class ShopPointsTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $academy_company_id
 * @property int $balance_points
 * @property numeric $balance_cashback
 * @property int $lifetime_points_earned
 * @property numeric $lifetime_cashback_earned
 * @property string $tier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopPointsTransaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereBalanceCashback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereBalancePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereLifetimeCashbackEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereLifetimePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopPointsWallet whereUserId($value)
 */
	class ShopPointsWallet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int $vendor_id
 * @property int|null $category_id
 * @property int|null $supplier_id
 * @property string $type
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $short_description
 * @property string|null $sku
 * @property numeric $price
 * @property numeric|null $sale_price
 * @property numeric|null $cost_price
 * @property bool $manage_stock
 * @property int|null $stock_quantity
 * @property int|null $stock_alert_threshold
 * @property numeric|null $weight
 * @property array<array-key, mixed>|null $dimensions
 * @property string|null $downloadable_file
 * @property int|null $download_limit
 * @property int|null $download_expiry_days
 * @property bool $requires_scheduling
 * @property array<array-key, mixed>|null $ai_tags
 * @property array<array-key, mixed>|null $goal_types
 * @property bool $is_featured
 * @property bool $is_active
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\ShopCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopOrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\ShopSupplier|null $supplier
 * @property-read \App\Models\ShopVendor $vendor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopWishlist> $wishlistEntries
 * @property-read int|null $wishlist_entries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct inStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereAiTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDownloadExpiryDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDownloadLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereDownloadableFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereGoalTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereManageStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereRequiresScheduling($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereStockAlertThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProduct withoutTrashed()
 */
	class ShopProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $path
 * @property string|null $alt
 * @property int $sort_order
 * @property bool $is_primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShopProduct|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopProductImage whereUpdatedAt($value)
 */
	class ShopProductImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $academy_company_id
 * @property array<array-key, mixed> $product_ids
 * @property string|null $reason
 * @property array<array-key, mixed>|null $context
 * @property numeric|null $score
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereProductIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopRecommendation whereUserId($value)
 */
	class ShopRecommendation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property string $name
 * @property string|null $document
 * @property string|null $contact_name
 * @property string|null $email
 * @property string|null $phone
 * @property array<array-key, mixed>|null $address
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopProduct> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopSupplier whereUpdatedAt($value)
 */
	class ShopSupplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academy_company_id
 * @property int|null $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $document
 * @property numeric $commission_rate
 * @property string $status
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property array<array-key, mixed>|null $bank_data
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany $academyCompany
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopProduct> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereBankData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopVendor whereUserId($value)
 */
	class ShopVendor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $notified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShopProduct|null $product
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopWishlist whereUserId($value)
 */
	class ShopWishlist extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property string $name
 * @property string|null $goal
 * @property string|null $target_audience
 * @property string $responsible_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property float $adherence_rate
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User|null $professional
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Supplement> $supplements
 * @property-read int|null $supplements_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereAdherenceRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereGoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereResponsibleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereTargetAudience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartStack whereUserId($value)
 */
	class SmartStack extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $post_id
 * @property string $platform
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CommunityPost|null $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialPostQueue whereUpdatedAt($value)
 */
	class SocialPostQueue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $gateway_id
 * @property string|null $gateway_type
 * @property int|null $user_id
 * @property int|null $academy_company_id
 * @property string $billing_type
 * @property int|null $max_professionals
 * @property int $plan_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $status
 * @property int $days_overdue
 * @property string|null $payment_method
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $card_expiry
 * @property \Illuminate\Support\Carbon|null $next_billing_date
 * @property int $retry_count
 * @property \Illuminate\Support\Carbon|null $last_attempt_at
 * @property int|null $pending_plan_id
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $refunded_at
 * @property numeric|null $refunded_amount
 * @property string|null $reason_for_suspension
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\AcademyCompany|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubscriptionLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Plan|null $pendingPlan
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCanonicalStatus(string ...$statuses)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCardBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCardExpiry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCardLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereDaysOverdue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereGatewayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereLastAttemptAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereMaxProfessionals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereNextBillingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePendingPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereReasonForSuspension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereRefundedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereRetryCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withoutTrashed()
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $subscription_id
 * @property string $event
 * @property string|null $old_status
 * @property string|null $new_status
 * @property numeric|null $amount
 * @property array<array-key, mixed>|null $payload
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereNewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereOldStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionLog whereUpdatedAt($value)
 */
	class SubscriptionLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $smart_stack_id
 * @property string $name
 * @property string|null $dosage
 * @property string $unit
 * @property string|null $frequency
 * @property int|null $duration_days
 * @property string|null $supplement_goal
 * @property string|null $observations
 * @property string|null $time_of_day
 * @property \Illuminate\Support\Carbon|null $last_taken_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\SmartStack|null $smartStack
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereLastTakenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereSmartStackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereSupplementGoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereTimeOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplement whereUserId($value)
 */
	class Supplement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $category
 * @property string|null $default_dosage
 * @property string|null $default_unit
 * @property string|null $description
 * @property string|null $benefits
 * @property string|null $side_effects
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereDefaultDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereDefaultUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereSideEffects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementCatalog whereUpdatedAt($value)
 */
	class SupplementCatalog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $supplement_id
 * @property \Illuminate\Support\Carbon $taken_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Supplement $supplement
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereSupplementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereTakenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupplementLog whereUserId($value)
 */
	class SupplementLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $priority
 * @property string $status
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUserId($value)
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string $system_name
 * @property string $system_url
 * @property string|null $qr_code_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereQrCodePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereSystemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereSystemUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemAccessLink whereUserId($value)
 */
	class SystemAccessLink extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $type
 * @property string|null $url
 * @property string|null $method
 * @property string $message
 * @property string|null $stack_trace
 * @property array<array-key, mixed>|null $payload
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereStackTrace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemError whereUserId($value)
 */
	class SystemError extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereValue($value)
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $support_ticket_id
 * @property int $user_id
 * @property string $message
 * @property bool $is_admin_reply
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\SupportTicket $ticket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereIsAdminReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereSupportTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketMessage whereUserId($value)
 */
	class TicketMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $module_id
 * @property string $title
 * @property string $slug
 * @property string|null $video_url
 * @property string|null $content
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingLessonCompletion> $completions
 * @property-read int|null $completions_count
 * @property-read \App\Models\TrainingModule $module
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLesson whereVideoUrl($value)
 */
	class TrainingLesson extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $lesson_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\TrainingLesson $lesson
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingLessonCompletion whereUserId($value)
 */
	class TrainingLessonCompletion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingLesson> $lessons
 * @property-read int|null $lessons_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingModule whereUpdatedAt($value)
 */
	class TrainingModule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $clinic_id
 * @property int $user_id
 * @property int|null $professional_id
 * @property int|null $creator_id
 * @property string $name
 * @property string|null $plan_label
 * @property string|null $description
 * @property string|null $goal
 * @property int|null $frequency
 * @property array<array-key, mixed>|null $days_of_week
 * @property string|null $difficulty
 * @property string|null $student_profile
 * @property string|null $split_type
 * @property int|null $estimated_duration
 * @property numeric $total_volume
 * @property array<array-key, mixed>|null $muscles_worked
 * @property bool $is_active
 * @property bool $created_by_ai
 * @property string $status
 * @property bool $is_template
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingPlanExercise> $exercises
 * @property-read int|null $exercises_count
 * @property-read \App\Models\User|null $professional
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkoutTargetArea> $targetAreas
 * @property-read int|null $target_areas_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereCreatedByAi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereDaysOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereEstimatedDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereGoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereIsTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereMusclesWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan wherePlanLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereSplitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereStudentProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereTotalVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlan whereUserId($value)
 */
	class TrainingPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $training_plan_id
 * @property int $exercise_id
 * @property string|null $custom_name
 * @property int $position
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExerciseCatalog $catalogExercise
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoadLog> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExerciseSet> $sets
 * @property-read int|null $sets_count
 * @property-read \App\Models\TrainingPlan $trainingPlan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise dParentRelationName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise dParentTenantColumnName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereCustomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereTrainingPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingPlanExercise whereUpdatedAt($value)
 */
	class TrainingPlanExercise extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $feature_code
 * @property string $title
 * @property string $message
 * @property array<array-key, mixed>|null $benefits
 * @property string $button_text
 * @property string|null $image_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereFeatureCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UpgradePopup whereUpdatedAt($value)
 */
	class UpgradePopup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $representative_id
 * @property bool $is_representative
 * @property string|null $uuid
 * @property string|null $google_id
 * @property string|null $provider
 * @property int|null $profile_id
 * @property int|null $plan_id
 * @property int|null $academy_company_id
 * @property int|null $clinic_id
 * @property string|null $user_type
 * @property string|null $admission_date
 * @property string|null $link_type
 * @property string|null $clinic_role
 * @property string|null $sector
 * @property string $status
 * @property string|null $remember_profile
 * @property string|null $activated_at
 * @property string $registration_approval_status
 * @property \Illuminate\Support\Carbon|null $registration_reviewed_at
 * @property string|null $registration_rejection_note
 * @property string|null $professional_code
 * @property string|null $qr_code_path
 * @property int|null $professional_plan_id
 * @property string $email
 * @property int $creditos
 * @property int $ai_credits
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $whatsapp
 * @property string|null $cpf
 * @property string|null $cnpj
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property bool $email_verified
 * @property string|null $email_verification_token
 * @property \Illuminate\Support\Carbon|null $email_verification_expires_at
 * @property \Illuminate\Support\Carbon|null $data_envio_confirmacao
 * @property int $tentativas_envio
 * @property string $password_hash
 * @property bool $force_password_change
 * @property \Illuminate\Support\Carbon|null $temp_password_expires_at
 * @property string|null $remember_token
 * @property string $name
 * @property string|null $username
 * @property bool $is_premium
 * @property bool $is_demo
 * @property \Illuminate\Support\Carbon|null $demo_expires_at
 * @property bool $is_admin
 * @property string|null $department
 * @property \Illuminate\Support\Carbon|null $premium_expires_at
 * @property string $onboarding_status
 * @property bool $perfil_paciente_completo
 * @property int $profile_completion_percentage
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property int $health_score
 * @property string $churn_risk
 * @property array<array-key, mixed>|null $usage_stats
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PatientAccessToken> $accessTokens
 * @property-read int|null $access_tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AIChat> $aiChats
 * @property-read int|null $ai_chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiCreditTransaction> $aiTransactions
 * @property-read int|null $ai_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiCreditUsageLog> $aiUsage
 * @property-read int|null $ai_usage_count
 * @property-read \App\Models\AiCreditWallet|null $aiWallet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BodyAssessment> $assessments
 * @property-read int|null $assessments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfessionalAvailability> $availabilities
 * @property-read int|null $availabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $blockedUsers
 * @property-read int|null $blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $blockers
 * @property-read int|null $blockers_count
 * @property-read \App\Models\ProfessionalBranding|null $branding
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Commission> $commissions
 * @property-read int|null $commissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunicationGroup> $communicationGroups
 * @property-read int|null $communication_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityComment> $communityComments
 * @property-read int|null $community_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityPost> $communityPosts
 * @property-read int|null $community_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityReaction> $communityReactions
 * @property-read int|null $community_reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserConsent> $consents
 * @property-read int|null $consents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponUsage> $couponUsages
 * @property-read int|null $coupon_usages_count
 * @property-read \App\Models\Subscription|null $currentSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvolutionPhoto> $evolutionPhotos
 * @property-read int|null $evolution_photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExerciseEntry> $exerciseEntries
 * @property-read int|null $exercise_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FoodEntry> $foodEntries
 * @property-read int|null $food_entries_count
 * @property-read string $community_profile_label
 * @property-read string $initials
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HealthMetric> $healthMetrics
 * @property-read int|null $health_metrics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoadLog> $loadLogs
 * @property-read int|null $load_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MealTemplate> $mealTemplates
 * @property-read int|null $meal_templates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalCertificate> $medicalCertificates
 * @property-read int|null $medical_certificates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalEvolution> $medicalEvolutions
 * @property-read int|null $medical_evolutions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalHistory> $medicalHistories
 * @property-read int|null $medical_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalPrescription> $medicalPrescriptions
 * @property-read int|null $medical_prescriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalReport> $medicalReports
 * @property-read int|null $medical_reports_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PatientDocument> $patientDocuments
 * @property-read int|null $patient_documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $patients
 * @property-read int|null $patients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Plan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserMenuPreference> $preferences
 * @property-read int|null $preferences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalCertificate> $professionalMedicalCertificates
 * @property-read int|null $professional_medical_certificates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalEvolution> $professionalMedicalEvolutions
 * @property-read int|null $professional_medical_evolutions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalPrescription> $professionalMedicalPrescriptions
 * @property-read int|null $professional_medical_prescriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MedicalReport> $professionalMedicalReports
 * @property-read int|null $professional_medical_reports_count
 * @property-read \App\Models\ProfessionalPlan|null $professionalPlan
 * @property-read \App\Models\ProfessionalProfile|null $professionalProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $professionals
 * @property-read int|null $professionals_count
 * @property-read \App\Models\UserProfile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfessionalPatientRequest> $receivedRequests
 * @property-read int|null $received_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $referrals
 * @property-read int|null $referrals_count
 * @property-read User|null $representative
 * @property-read \App\Models\RepresentativeProfile|null $representativeProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $requestedCoupons
 * @property-read int|null $requested_coupons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfessionalPatientRequest> $sentRequests
 * @property-read int|null $sent_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemAccessLink> $systemAccessLinks
 * @property-read int|null $system_access_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingPlan> $trainingPlans
 * @property-read int|null $training_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PatientTreatmentPlan> $treatmentPlans
 * @property-read int|null $treatment_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserPlan> $userPlans
 * @property-read int|null $user_plans_count
 * @property-read \App\Models\Role|null $userProfile
 * @property-read \App\Models\Role|null $userRole
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WaterEntry> $waterEntries
 * @property-read int|null $water_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeightEntry> $weightEntries
 * @property-read int|null $weight_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkoutSession> $workoutSessions
 * @property-read int|null $workout_sessions_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdmissionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAiCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereChurnRisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereClinicRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreditos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDataEnvioConfirmacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDemoExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerificationExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerificationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereForcePasswordChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHealthScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsDemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsRepresentative($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOnboardingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePerfilPacienteCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePremiumExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfessionalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfessionalPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileCompletionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereQrCodePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegistrationApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegistrationRejectionNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegistrationReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTempPasswordExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTentativasEnvio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsageStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWhatsapp($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $badge_code
 * @property string $title
 * @property string|null $description
 * @property string|null $icon_url
 * @property \Illuminate\Support\Carbon $unlocked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereBadgeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereUnlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAchievement whereUserId($value)
 */
	class UserAchievement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $version
 * @property string $consent_type
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereConsentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereVersion($value)
 */
	class UserConsent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $menu_id
 * @property bool $visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Menu $menu
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenuPreference whereVisible($value)
 */
	class UserMenuPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $plan_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPlan whereUserId($value)
 */
	class UserPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string $sex
 * @property int|null $height_cm
 * @property float|null $target_weight_kg
 * @property string|null $training_days_per_week
 * @property string $activity_level
 * @property string $climate
 * @property string $goal
 * @property int|null $daily_calorie_target
 * @property float|null $protein_target_g
 * @property float|null $carbs_target_g
 * @property float|null $fat_target_g
 * @property int|null $water_target_ml
 * @property bool $is_water_target_auto
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property bool $has_disease
 * @property string|null $disease_details
 * @property bool $has_injury
 * @property string|null $injury_details
 * @property bool $uses_medication
 * @property string|null $medication_details
 * @property bool $has_allergy
 * @property string|null $allergy_details
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property \Illuminate\Support\Carbon|null $profile_completed_at
 * @property string|null $physical_level
 * @property string|null $experience_level
 * @property string|null $training_location
 * @property string|null $cardio_frequency
 * @property int|null $sleep_hours
 * @property int|null $nutrition_quality
 * @property int|null $available_daily_time_mins
 * @property string|null $fitness_notes
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereActivityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereAllergyDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereAvailableDailyTimeMins($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCarbsTargetG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCardioFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereClimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereDailyCalorieTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereDiseaseDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereEmergencyContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereEmergencyContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereExperienceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereFatTargetG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereFitnessNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereGoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereHasAllergy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereHasDisease($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereHasInjury($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereHeightCm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereInjuryDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereIsWaterTargetAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereMedicationDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereNutritionQuality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile wherePhysicalLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereProfileCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereProteinTargetG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereSleepHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereTargetWeightKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereTrainingDaysPerWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereTrainingLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUsesMedication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereWaterTargetMl($value)
 */
	class UserProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon $entry_date
 * @property \Illuminate\Support\Carbon|null $drank_at
 * @property int $amount_ml
 * @property string $source
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereAmountMl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereDrankAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereEntryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WaterEntry whereUserId($value)
 */
	class WaterEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $professional_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property \Illuminate\Support\Carbon $weighed_at
 * @property float $weight_kg
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereProfessionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereWeighedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeightEntry whereWeightKg($value)
 */
	class WeightEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $representative_id
 * @property numeric $amount
 * @property string|null $pix_key
 * @property string|null $bank_info
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Commission> $commissions
 * @property-read int|null $commissions_count
 * @property-read \App\Models\User $representative
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereBankInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest wherePixKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereRepresentativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereUpdatedAt($value)
 */
	class WithdrawalRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $clinic_id
 * @property int|null $academy_company_id
 * @property string|null $image_path
 * @property string|null $raw_ocr_text
 * @property array<array-key, mixed>|null $structured_json
 * @property string $status
 * @property string|null $error_message
 * @property float|null $ai_confidence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereAcademyCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereAiConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereClinicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereRawOcrText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereStructuredJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutImportLog whereUserId($value)
 */
	class WorkoutImportLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $session_date
 * @property int|null $rpe_score
 * @property string|null $mood
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereMood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereRpeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutSession whereUserId($value)
 */
	class WorkoutSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $training_plan_id
 * @property int|null $muscle_id
 * @property string $target_area
 * @property string|null $reference_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademyCompany|null $academyCompany
 * @property-read \App\Models\Muscle|null $muscle
 * @property-read \App\Models\TrainingPlan|null $trainingPlan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereMuscleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereReferencePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereTargetArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereTrainingPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkoutTargetArea whereUserId($value)
 */
	class WorkoutTargetArea extends \Eloquent {}
}

