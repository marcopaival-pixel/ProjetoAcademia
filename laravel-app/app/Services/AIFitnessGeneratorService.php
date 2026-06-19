<?php

namespace App\Services;

use App\Models\User;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseCatalog;
use App\Services\AI\AIProviderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIFitnessGeneratorService
{
    public function __construct(
        private AIProviderService $aiProvider,
    ) {}

    /**
     * Gera um treino automático baseado no perfil do usuário.
     */
    public function generateTrainingPlan(User $user): ?TrainingPlan
    {
        $profile = $user->profile;
        if (! $profile) {
            return null;
        }

        $prompt = $this->buildTrainingPrompt($user);

        $response = $this->aiProvider->call(
            user: $user,
            messages: [
                ['role' => 'system', 'content' => 'Você é um Personal Trainer de elite. Responda apenas com JSON estruturado seguindo o modelo solicitado.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            agentName: 'fitness_generator_training',
            modelType: 'main',
            context: ['response_format' => ['type' => 'json_object'], 'temperature' => 0.5]
        );

        if (! ($response['ok'] ?? false)) {
            Log::error('IA Response Error: '.($response['error'] ?? 'unknown'));

            return null;
        }

        $planData = json_decode($response['message'] ?? '{}', true);
        if (! is_array($planData) || empty($planData['exercises'])) {
            return null;
        }

        return DB::transaction(function () use ($user, $planData) {
            $plan = TrainingPlan::create([
                'user_id' => $user->id,
                'name' => $planData['name'] ?? 'Treino Gerado por IA',
                'goal' => $planData['goal'] ?? $user->profile->goal,
                'description' => $planData['notes'] ?? 'Plano gerado automaticamente pelo NexBot.',
                'is_active' => true,
                'created_by_ai' => true,
                'status' => 'active',
            ]);

            foreach ($planData['exercises'] as $index => $ex) {
                $catalogEx = ExerciseCatalog::where('name', 'like', '%'.$ex['name'].'%')->first();

                $tpe = TrainingPlanExercise::create([
                    'training_plan_id' => $plan->id,
                    'exercise_id' => $catalogEx?->id,
                    'custom_name' => $catalogEx ? null : $ex['name'],
                    'position' => $index + 1,
                    'notes' => $ex['notes'] ?? null,
                ]);

                $numSets = (int) ($ex['sets'] ?? 3);
                for ($i = 1; $i <= $numSets; $i++) {
                    ExerciseSet::create([
                        'training_plan_exercise_id' => $tpe->id,
                        'set_number' => $i,
                        'reps_target' => $ex['reps'] ?? '10-12',
                        'rest_seconds' => $ex['rest_seconds'] ?? 60,
                        'rpe_target' => $ex['rpe'] ?? 8,
                        'set_type' => 'normal',
                    ]);
                }
            }

            return $plan;
        });
    }

    private function buildTrainingPrompt(User $user): string
    {
        $p = $user->profile;
        $age = \App\Services\Nutrition::ageYears($p->birth_date?->toDateString()) ?? 'N/A';

        return "Gere um plano de treino semanal completo para este usuário do sistema NexShape:
        - Nome: {$user->name}
        - Sexo: {$p->sex}
        - Idade: {$age}
        - Objetivo: {$p->goal}
        - Nível Físico: {$p->physical_level}
        - Local de Treino: {$p->training_location}
        - Tempo Disponível: {$p->available_daily_time_mins} min
        - Frequência Semanal: {$p->training_days_per_week} dias
        - Restrições/Notas: {$p->fitness_notes}

        Retorne no formato JSON rigoroso:
        {
            \"name\": \"Título do Plano\",
            \"goal\": \"Objetivo do Plano\",
            \"notes\": \"Dicas gerais de execução e segurança\",
            \"exercises\": [
                {
                    \"name\": \"Nome do Exercício\",
                    \"sets\": 3,
                    \"reps\": \"10-12\",
                    \"rest_seconds\": 60,
                    \"rpe\": 8,
                    \"notes\": \"Dica técnica para este exercício\"
                }
            ]
        }";
    }

    /**
     * Gera sugestões alimentares via IA.
     */
    public function generateMealPlan(User $user): array
    {
        $p = $user->profile;
        if (! $p) {
            return ['ok' => false, 'error' => 'Perfil não encontrado'];
        }

        $prompt = "Gere um plano alimentar diário sugerido para este usuário:
        - Peso: ".($user->weightEntries()->latest()->first()->weight_kg ?? 'N/A')."kg
        - Objetivo: {$p->goal}
        - Meta Calórica: {$p->daily_calorie_target} kcal
        - Nível de Atividade: {$p->activity_level}
        - Restrições: {$p->fitness_notes}

        Retorne um JSON com a seguinte estrutura:
        {
            \"daily_summary\": \"Resumo da estratégia nutricional\",
            \"meals\": [
                {
                    \"time\": \"08:00\",
                    \"name\": \"Café da Manhã\",
                    \"suggestions\": [\"Ovo mexido\", \"Pão integral\"],
                    \"macros_est\": \"P: 20g, C: 30g, F: 10g\"
                }
            ]
        }";

        $response = $this->aiProvider->call(
            user: $user,
            messages: [
                ['role' => 'system', 'content' => 'Você é um Nutricionista Esportivo de elite. Responda apenas com JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            agentName: 'fitness_generator_meal',
            modelType: 'main',
            context: ['response_format' => ['type' => 'json_object'], 'temperature' => 0.5]
        );

        if (! ($response['ok'] ?? false)) {
            return ['ok' => false, 'error' => $response['error'] ?? 'IA Offline'];
        }

        $plan = json_decode($response['message'] ?? '{}', true);

        return ['ok' => true, 'plan' => $plan];
    }

    /**
     * Gera um relatório de evolução semanal via IA.
     */
    public function generateEvolutionReport(User $user): array
    {
        $p = $user->profile;
        if (! $p) {
            return ['ok' => false, 'error' => 'Perfil não encontrado'];
        }

        $assessments = \App\Models\BodyAssessment::where('user_id', $user->id)
            ->orderBy('assessment_date', 'desc')
            ->take(2)
            ->get();

        $currentAssessment = $assessments->first();
        $previousAssessment = $assessments->last();

        $pesoAtual = $currentAssessment ? $currentAssessment->weight_kg.'kg' : 'N/A';
        $bfAtual = $currentAssessment ? $currentAssessment->bf_percent.'%' : 'N/A';

        $historico = 'Nenhum histórico anterior';
        if ($currentAssessment && $previousAssessment && $currentAssessment->id !== $previousAssessment->id) {
            $historico = "Peso anterior: {$previousAssessment->weight_kg}kg, BF anterior: {$previousAssessment->bf_percent}%";
        }

        $activePlan = TrainingPlan::where('user_id', $user->id)->where('is_active', true)->first();
        $treinoStr = $activePlan ? "{$activePlan->name} - Objetivo: {$activePlan->goal}" : 'Nenhum treino ativo no sistema';

        $notasProf = $currentAssessment->notes ?? '';
        $notasUser = $p->fitness_notes ?? '';
        $observacoes = trim("$notasProf $notasUser") ?: 'Nenhuma observação extra.';

        $idade = $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age.' anos' : 'N/A';
        $altura = $p->height_cm ? $p->height_cm.' cm' : 'N/A';

        $metaGeralTranslate = [
            'lose_weight' => 'Perda de Peso / Emagrecimento',
            'gain_mass' => 'Hipertrofia / Ganho de Massa',
            'maintain' => 'Manutenção / Condicionamento',
            'health' => 'Saúde e Qualidade de Vida',
            'performance' => 'Performance Desportiva',
        ];
        $metaGeral = $metaGeralTranslate[$p->goal] ?? ($p->goal ?? 'Não definido');

        $pesoAlvo = $p->target_weight_kg ? $p->target_weight_kg.'kg' : 'N/A';
        $nivelExperiencia = ucfirst($p->experience_level ?? 'N/A');
        $diasTreino = $p->training_days_per_week ? $p->training_days_per_week.' dias/semana' : 'N/A';

        $prompt = "Você é o NexBot, a Inteligência Artificial de alta performance do ecossistema NexShape. Gere um Relatório Semanal de Evolução em JSON válido (sem markdown).

DADOS: Atleta {$user->name} ({$idade}), Objetivo: {$metaGeral}, Peso: {$pesoAtual}, BF: {$bfAtual}, Histórico: {$historico}, Treino: {$treinoStr}, Notas: {$observacoes}

Retorne JSON com chaves: diagnostico, scores (performance_geral, disciplina, consistencia, recuperacao, intensidade, condicionamento, regularidade, evolucao), tendencias, insight_premium, estrategia_semana, ajustes_treino, recuperacao_energia, alimentacao_metabolismo, comparativo, veredito, proximos_passos.";

        $response = $this->aiProvider->call(
            user: $user,
            messages: [
                ['role' => 'system', 'content' => 'Retorne ESTRITAMENTE JSON válido, sem markdown.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            agentName: 'fitness_generator_evolution',
            modelType: 'main',
            context: ['response_format' => ['type' => 'json_object'], 'temperature' => 0.6, 'max_tokens' => 2500]
        );

        if (! ($response['ok'] ?? false)) {
            return ['ok' => false, 'error' => $response['error'] ?? 'IA Offline ou Erro na API'];
        }

        return ['ok' => true, 'report' => $response['message']];
    }
}
