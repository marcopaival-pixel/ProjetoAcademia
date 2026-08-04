<?php

namespace Database\Seeders;

use App\Models\AiAgentRegistry;
use App\Models\AiFeatureRoute;
use Illuminate\Database\Seeder;

class AiGovernanceSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            ['agent_key' => 'intent_classifier', 'name' => 'Classificador de intenção', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'intent:v1'],
            ['agent_key' => 'support', 'name' => 'Atendente contextual', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'support:v1'],
            ['agent_key' => 'training', 'name' => 'Especialista em treino', 'model' => config('services.openai.model_main'), 'prompt_version' => 'training:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'nutrition', 'name' => 'Especialista em nutrição esportiva', 'model' => config('services.openai.model_main'), 'prompt_version' => 'nutrition:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'clinical', 'name' => 'Especialista clínico limitado', 'model' => config('services.openai.model_main'), 'prompt_version' => 'clinical:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'analytics', 'name' => 'Analista de engajamento', 'model' => config('services.openai.model_main'), 'prompt_version' => 'analytics:v1'],
            ['agent_key' => 'finance', 'name' => 'Agente financeiro', 'model' => null],
            ['agent_key' => 'sales', 'name' => 'Agente comercial', 'model' => null],
            ['agent_key' => 'retention', 'name' => 'Analista de retenção', 'model' => null],
            ['agent_key' => 'vision', 'name' => 'Agente de visão geral', 'model' => config('services.openai.model_main'), 'prompt_version' => 'vision:v1'],
            ['agent_key' => 'body_photo_validator', 'name' => 'Validador de fotos corporais', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'body-photo-validator:v1'],
            ['agent_key' => 'workout_validator', 'name' => 'Validador de documento de treino', 'model' => config('services.openai.model_workout_import'), 'prompt_version' => 'workout-validator:v1'],
            ['agent_key' => 'workout_extractor', 'name' => 'Extrator de treino', 'model' => config('services.openai.model_workout_import'), 'prompt_version' => 'workout-extractor:v1'],
            ['agent_key' => 'workout_consolidator', 'name' => 'Consolidador de treino', 'model' => config('services.openai.model_workout_import'), 'prompt_version' => 'workout-consolidator:v1'],
            ['agent_key' => 'workout_auditor', 'name' => 'Auditor de treino importado', 'model' => config('services.openai.model_workout_import'), 'prompt_version' => 'workout-auditor:v1'],
            ['agent_key' => 'nutrition_meal_text_parser', 'name' => 'Extrator nutricional por texto', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'nutrition-meal-parser:v1'],
            ['agent_key' => 'workout_photo_parser', 'name' => 'Parser legado de treino por OCR', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'workout-photo-parser:v1'],
            ['agent_key' => 'fitness_training_generator', 'name' => 'Gerador de treino legado governado', 'model' => config('services.openai.model_main'), 'prompt_version' => 'fitness-training:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'fitness_meal_generator', 'name' => 'Gerador alimentar legado governado', 'model' => config('services.openai.model_main'), 'prompt_version' => 'fitness-meal:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'fitness_evolution_report', 'name' => 'Relatório de evolução legado governado', 'model' => config('services.openai.model_main'), 'prompt_version' => 'fitness-evolution:v1'],
            ['agent_key' => 'smart_stack_suggestion', 'name' => 'Sugestão de suplementação', 'model' => config('services.openai.model_main'), 'prompt_version' => 'smart-stack:v1', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['agent_key' => 'advanced_contextual_agent', 'name' => 'Agente contextual legado governado', 'model' => config('services.openai.model_main'), 'prompt_version' => 'advanced-agent:v1'],
            ['agent_key' => 'nexbot_chat', 'name' => 'Chat legado governado', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'nexbot-chat:v1'],
            ['agent_key' => 'evolution_report_comparison', 'name' => 'Comparador visual de evolução', 'model' => config('ai_evolution.openai.comparison_model'), 'prompt_version' => 'evolution-report-orchestrator:v2', 'schema_version' => 'evolution-comparison:v1'],
            ['agent_key' => 'evolution_report_audit', 'name' => 'Auditor anti-alucinação de evolução', 'model' => config('ai_evolution.openai.audit_model'), 'prompt_version' => 'evolution-report-orchestrator:v2', 'schema_version' => 'report-audit:v1'],
            ['agent_key' => 'prescription_safety_auditor', 'name' => 'Auditor de segurança de prescrições', 'model' => config('services.openai.model_fast'), 'prompt_version' => 'prescription-safety:v1', 'schema_version' => 'prescription-safety:v1'],
            ['agent_key' => 'bug_surgeon', 'name' => 'Bug Surgeon — diagnóstico automático', 'model' => config('services.bug_surgeon.ai_model', config('services.openai.model_fast')), 'prompt_version' => 'bug-surgeon:v1', 'schema_version' => 'bug-surgeon-diagnosis:v1'],
        ];

        foreach ($agents as $agent) {
            AiAgentRegistry::updateOrCreate(['agent_key' => $agent['agent_key']], array_merge([
                'description' => null,
                'allowed_functions' => [],
                'forbidden_functions' => [],
                'provider' => 'openai',
                'reasoning_effort' => null,
                'schema_version' => null,
                'requires_audit' => false,
                'auditor_agent_key' => null,
                'fallback_agent_key' => null,
                'active' => true,
            ], $agent));
        }

        $routes = [
            ['feature_key' => 'ai_orchestrator', 'entry_agent_key' => 'intent_classifier', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService'],
            ['feature_key' => 'chat', 'entry_agent_key' => 'support', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService'],
            ['feature_key' => 'workout_image_import', 'entry_agent_key' => 'workout_validator', 'orchestrator' => 'App\\Services\\AI\\WorkoutImportOrchestrator', 'requires_validation' => true, 'requires_audit' => true, 'auditor_agent_key' => 'workout_auditor'],
            ['feature_key' => 'nutrition_text_analysis', 'entry_agent_key' => 'nutrition_meal_text_parser'],
            ['feature_key' => 'nutrition_suggestion', 'entry_agent_key' => 'nutrition', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'workout_prescription', 'entry_agent_key' => 'training', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'nutrition_prescription', 'entry_agent_key' => 'nutrition', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'clinical_prescription', 'entry_agent_key' => 'clinical', 'orchestrator' => 'App\\Services\\AI\\OrchestratorService', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'evolution_report', 'entry_agent_key' => 'body_photo_validator', 'orchestrator' => 'App\\Services\\AI\\EvolutionReportOrchestratorService', 'requires_validation' => true, 'requires_audit' => true, 'auditor_agent_key' => 'evolution_report_audit'],
            ['feature_key' => 'body_photo_validation', 'entry_agent_key' => 'body_photo_validator', 'requires_validation' => true],
            ['feature_key' => 'smart_stack_suggestion', 'entry_agent_key' => 'smart_stack_suggestion', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'fitness_training_generation', 'entry_agent_key' => 'fitness_training_generator', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'fitness_meal_generation', 'entry_agent_key' => 'fitness_meal_generator', 'requires_audit' => true, 'auditor_agent_key' => 'prescription_safety_auditor'],
            ['feature_key' => 'bug_surgeon_diagnosis', 'entry_agent_key' => 'bug_surgeon', 'failure_behavior' => 'fail_closed'],
        ];

        foreach ($routes as $route) {
            AiFeatureRoute::updateOrCreate(['feature_key' => $route['feature_key']], array_merge([
                'orchestrator' => null,
                'requires_validation' => false,
                'requires_audit' => false,
                'auditor_agent_key' => null,
                'failure_behavior' => 'fail_closed',
                'active' => true,
            ], $route));
        }
    }
}
