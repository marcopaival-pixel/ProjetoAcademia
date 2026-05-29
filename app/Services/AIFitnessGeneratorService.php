<?php

namespace App\Services;

use App\Models\User;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\ExerciseSet;
use App\Models\ExerciseCatalog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIFitnessGeneratorService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.api_key', '');
        $this->apiUrl = (string) config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->model = (string) config('services.openai.model', 'gpt-4o'); 
    }

    /**
     * Gera um treino automático baseado no perfil do usuário.
     */
    public function generateTrainingPlan(User $user): ?TrainingPlan
    {
        $profile = $user->profile;
        if (!$profile) return null;

        $prompt = $this->buildTrainingPrompt($user);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um Personal Trainer de elite. Responda apenas com JSON estruturado seguindo o modelo solicitado.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if (!$response->successful()) {
                Log::error("IA Response Error: " . $response->body());
                return null;
            }

            $planData = $response->json();

            // Criar o plano no banco
            return DB::transaction(function () use ($user, $planData) {
                $plan = TrainingPlan::create([
                    'user_id' => $user->id,
                    'name' => $planData['name'] ?? 'Treino Gerado por IA',
                    'goal' => $planData['goal'] ?? $user->profile->goal,
                    'description' => $planData['notes'] ?? 'Plano gerado automaticamente pelo NexBot.',
                    'is_active' => true,
                    'created_by_ai' => true,
                    'status' => 'active'
                ]);

                foreach ($planData['exercises'] as $index => $ex) {
                    // Tenta encontrar o exercício no catálogo ou usa o nome sugerido
                    $catalogEx = ExerciseCatalog::where('name', 'like', '%' . $ex['name'] . '%')->first();
                    
                    $tpe = TrainingPlanExercise::create([
                        'training_plan_id' => $plan->id,
                        'exercise_id' => $catalogEx?->id,
                        'custom_name' => $catalogEx ? null : $ex['name'],
                        'position' => $index + 1,
                        'notes' => $ex['notes'] ?? null,
                    ]);

                    // Criar as séries
                    $numSets = (int) ($ex['sets'] ?? 3);
                    for ($i = 1; $i <= $numSets; $i++) {
                        ExerciseSet::create([
                            'training_plan_exercise_id' => $tpe->id,
                            'set_number' => $i,
                            'reps_target' => $ex['reps'] ?? '10-12',
                            'rest_seconds' => $ex['rest_seconds'] ?? 60,
                            'rpe_target' => $ex['rpe'] ?? 8,
                            'set_type' => 'normal'
                        ]);
                    }
                }

                return $plan;
            });

        } catch (\Exception $e) {
            Log::error("Erro ao gerar treino IA: " . $e->getMessage());
            return null;
        }
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
        if (!$p) return ['ok' => false, 'error' => 'Perfil não encontrado'];

        $prompt = "Gere um plano alimentar diário sugerido para este usuário:
        - Peso: " . ($user->weightEntries()->latest()->first()->weight_kg ?? 'N/A') . "kg
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

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um Nutricionista Esportivo de elite. Responda apenas com JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if (!$response->successful()) return ['ok' => false, 'error' => 'IA Offline'];

            return ['ok' => true, 'plan' => $response->json()];

        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Gera um relatório de evolução semanal via IA.
     */
    public function generateEvolutionReport(User $user): array
    {
        $p = $user->profile;
        if (!$p) return ['ok' => false, 'error' => 'Perfil não encontrado'];

        // Coletar avaliações recentes
        $assessments = \App\Models\BodyAssessment::where('user_id', $user->id)
            ->orderBy('assessment_date', 'desc')
            ->take(2)
            ->get();
            
        $currentAssessment = $assessments->first();
        $previousAssessment = $assessments->last();

        $pesoAtual = $currentAssessment ? $currentAssessment->weight_kg . 'kg' : 'N/A';
        $bfAtual = $currentAssessment ? $currentAssessment->bf_percent . '%' : 'N/A';
        
        $historico = "Nenhum histórico anterior";
        if ($currentAssessment && $previousAssessment && $currentAssessment->id !== $previousAssessment->id) {
            $historico = "Peso anterior: {$previousAssessment->weight_kg}kg, BF anterior: {$previousAssessment->bf_percent}%";
        }

        // Treino atual
        $activePlan = TrainingPlan::where('user_id', $user->id)->where('is_active', true)->first();
        $treinoStr = $activePlan ? "{$activePlan->name} - Objetivo: {$activePlan->goal}" : "Nenhum treino ativo no sistema";

        // Observações do professor / notas do usuário
        $notasProf = $currentAssessment->notes ?? '';
        $notasUser = $p->fitness_notes ?? '';
        $observacoes = trim("$notasProf $notasUser");
        if (empty($observacoes)) $observacoes = "Nenhuma observação extra.";

        // Dados base de perfil para traçar o 'patamar'
        $idade = $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age . ' anos' : 'N/A';
        $altura = $p->height_cm ? $p->height_cm . ' cm' : 'N/A';
        
        $metaGeralTranslate = [
            'lose_weight' => 'Perda de Peso / Emagrecimento',
            'gain_mass' => 'Hipertrofia / Ganho de Massa',
            'maintain' => 'Manutenção / Condicionamento',
            'health' => 'Saúde e Qualidade de Vida',
            'performance' => 'Performance Desportiva'
        ];
        $metaGeral = $metaGeralTranslate[$p->goal] ?? ($p->goal ?? 'Não definido');
        
        $pesoAlvo = $p->target_weight_kg ? $p->target_weight_kg . 'kg' : 'N/A';
        $nivelExperiencia = ucfirst($p->experience_level ?? 'N/A');
        $diasTreino = $p->training_days_per_week ? $p->training_days_per_week . ' dias/semana' : 'N/A';

        $prompt = "Você é o NexBot, a Inteligência Artificial de alta performance do ecossistema NexShape. O seu objetivo é fornecer um Relatório Semanal de Evolução ao aluno, como se fosse um coach premium (estilo Whoop, Apple Fitness).

Aja com um tom de 'Treinador de Elite/Analista de Performance', científico, sofisticado e focado em resultados.
NUNCA use 'Sem dados precisos', use frases premium como 'A ausência de biometria sincronizada limita análises avançadas de recuperação muscular e performance metabólica.'

RETORNE ESTRITAMENTE UM OBJETO JSON VÁLIDO. NÃO retorne markdown ao redor do JSON.
A estrutura JSON DEVE conter exatamente as seguintes chaves:

{
  \"diagnostico\": \"O texto do diagnóstico inteligente. Pareça muito inteligente e analítico.\",
  \"scores\": {
    \"performance_geral\": 82,
    \"disciplina\": 90,
    \"consistencia\": 85,
    \"recuperacao\": 70,
    \"intensidade\": 75,
    \"condicionamento\": 80,
    \"regularidade\": 95,
    \"evolucao\": 75
  },
  \"tendencias\": [
    {\"area\": \"Condicionamento\", \"status\": \"Evolução moderada\", \"icon\": \"↗\"},
    {\"area\": \"Peso\", \"status\": \"Estável\", \"icon\": \"→\"},
    {\"area\": \"Recuperação\", \"status\": \"Atenção\", \"icon\": \"↘\"}
  ],
  \"insight_premium\": \"Um parágrafo de insight profundo e científico sobre o comportamento.\",
  \"estrategia_semana\": \"O foco desta semana.\",
  \"ajustes_treino\": \"O que alterar no treino.\",
  \"recuperacao_energia\": \"O que fazer para recuperar melhor.\",
  \"alimentacao_metabolismo\": \"Dicas de alimentação.\",
  \"comparativo\": \"Comparativo com a semana anterior.\",
  \"veredito\": \"Frase de efeito motivacional final.\",
  \"proximos_passos\": [\"Passo 1\", \"Passo 2\", \"Passo 3\"]
}

DADOS DE PERFIL CADASTRADOS (PATAMAR ALVO):
- Atleta: {$user->name} ({$idade}), Altura: {$altura}
- Objetivo Principal: {$metaGeral}
- Peso Alvo: {$pesoAlvo}
- Nível de Experiência: {$nivelExperiencia}
- Frequência: {$diasTreino}

DADOS BIOMÉTRICOS RECENTES (STATUS ATUAL):
- Peso Atual: {$pesoAtual}
- Percentual de Gordura (BF): {$bfAtual}

HISTÓRICO RECENTE (Variação):
- {$historico}

PROGRAMA DE TREINO ATIVO:
- {$treinoStr}

NOTAS CLÍNICAS / OBSERVAÇÕES DO PROFESSOR:
- {$observacoes}

Gere os scores de forma coerente com a evolução atual (0 a 100).";

        try {
            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é o NexBot, a IA de Elite do NexShape. Retorne ESTRITAMENTE a estrutura JSON pedida e MAIS NADA. NUNCA coloque formatações markdown em torno do JSON (não use ```json).'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ];
            
            // Suporte para garantias JSON caso a API suporte
            if (str_contains($this->model, 'gpt-3.5') || str_contains($this->model, 'gpt-4')) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($this->apiUrl, $payload);

            if (!$response->successful()) {
                return ['ok' => false, 'error' => 'IA Offline ou Erro na API'];
            }

            $content = $response->json('choices.0.message.content');
            return ['ok' => true, 'report' => $content];

        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
