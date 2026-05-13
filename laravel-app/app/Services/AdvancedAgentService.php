<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use App\Models\AIChat;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * @deprecated Este serviço foi substituído pelo App\Services\AI\OrchestratorService.
 * O novo orquestrador utiliza agentes especializados (Training, Nutrition, Support, etc.)
 * para uma gestão de contexto mais granular e eficiente.
 */
class AdvancedAgentService
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
     * Processa a mensagem do usuário usando o Agente Inteligente Avançado
     */
    public function process(User $user, string $message, array $conversationHistory = []): array
    {
        if (empty($this->apiKey)) {
            return ['ok' => false, 'error' => 'Chave OpenAI não configurada'];
        }

        try {
            // 1. Coleta o contexto completo e real
            $context = $this->collectRealContext($user);

            // 2. Constrói o System Prompt baseado no template
            $systemPrompt = $this->buildAdvancedPrompt($context);

            // 3. Prepara as mensagens
            $messages = $conversationHistory;
            $messages[] = ['role' => 'user', 'content' => $message];

            // 4. Chamada à API
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ...$messages
                    ],
                    'temperature' => 0.4, 
                ]);

            if (!$response->successful()) {
                return ['ok' => false, 'error' => $response->json()['error']['message'] ?? 'Erro na API'];
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '';

            // 5. Extração de Ação JSON
            $action = $this->extractJsonAction($content);

            return [
                'ok' => true,
                'response' => $content,
                'action' => $action
            ];

        } catch (Exception $e) {
            Log::error("Erro no AdvancedAgent: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Coleta dados reais do banco de dados para o contexto
     */
    private function collectRealContext(User $user): array
    {
        $profile = $user->profile;
        $company = $user->academyCompany;
        
        // Nutrição
        $nutritionService = app(\App\Services\Nutrition::class);
        $dailyTarget = $nutritionService->dailyTargetKcal($user);
        $nutritionLogs = $nutritionService->getLogs($user, now()->toDateString());
        
        // Hidratação
        $waterConsumed = \App\Models\WaterEntry::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount_ml');

        // Treino
        $lastWorkout = \App\Models\WorkoutSession::where('user_id', $user->id)
            ->with('trainingPlan')
            ->latest()
            ->first();

        // Profissional Vinculado
        $professional = $user->professionals()->first();

        // Financeiro
        $subscription = $user->currentSubscription;

        // Evolução e Bioimpedância (Última Avaliação)
        $lastAssessment = \App\Models\PhysicalAssessment::where('user_id', $user->id)
            ->latest()
            ->first();

        // Performance (Melhor 1RM)
        $topPR = \App\Models\ExerciseLog::where('user_id', $user->id)
            ->whereNotNull('one_rm')
            ->orderBy('one_rm', 'desc')
            ->first();

        return [
            'tipoUsuario' => $user->getRoleNames()[0] ?? 'aluno',
            'empresaId' => $company->id ?? 'N/A',
            'planoEmpresa' => $user->activePlan->plan->name ?? 'Free',
            'featuresPlano' => $this->getFeaturesContext($user),
            'configuracoesEmpresa' => $this->getCompanySettingsContext($company),
            'dadosAluno' => "Nome: {$user->name}, Peso: " . ($user->weight ?? 'N/A') . "kg, Altura: " . ($user->height ?? 'N/A') . "cm, Objetivo: " . ($profile->goal ?? 'Geral'),
            'professional_context' => $professional ? "Profissional Responsável: {$professional->name} (ID: {$professional->id})" : "Nenhum profissional vinculado",
            'dadosTreino' => $lastWorkout ? "Último: {$lastWorkout->trainingPlan->name} em " . $lastWorkout->created_at->format('d/m') : 'Nenhum treino registrado',
            'dadosDieta' => "Meta: {$dailyTarget}kcal, Consumido hoje: " . ($nutritionLogs['consumed']['kcal'] ?? 0) . "kcal. Macros: P: " . ($nutritionLogs['consumed']['protein'] ?? 0) . "g, C: " . ($nutritionLogs['consumed']['carb'] ?? 0) . "g, G: " . ($nutritionLogs['consumed']['fat'] ?? 0) . "g",
            'dadosHidratacao' => "Meta: " . ($profile->water_goal ?? 3000) . "ml, Consumido hoje: {$waterConsumed}ml",
            'dadosEvolucao' => $lastAssessment ? "BF: {$lastAssessment->bf_percent}%, Massa Magra: {$lastAssessment->muscle_mass}kg, Gordura: {$lastAssessment->fat_mass}kg (Avaliação em {$lastAssessment->assessment_date->format('d/m')})" : "Sem avaliações físicas registradas",
            'dadosPerformance' => $topPR ? "Melhor levantamento: {$topPR->exercise_name} com 1RM estimado de " . round($topPR->one_rm, 1) . "kg" : "Sem recordes de carga registrados",
            'dadosAgenda' => "Consultar agenda para próximos horários",
            'dadosFinanceiro' => $subscription ? "Status: {$subscription->status}, Vence em: " . ($subscription->end_date ?? 'N/A') : "Nenhuma assinatura ativa"
        ];
    }

    private function getFeaturesContext(User $user): string
    {
        $features = [
            'ai_training' => $user->hasFeature('ai_training'),
            'ai_nutrition' => $user->hasFeature('ai_nutrition'),
            'automated_actions' => $user->hasFeature('automated_actions'),
        ];
        
        $ctx = "";
        foreach ($features as $k => $v) {
            $val = $v ? 'SIM' : 'NÃO';
            $ctx .= "- " . str_replace('_', ' ', ucfirst($k)) . ": $val\n";
        }
        return $ctx;
    }

    private function getCompanySettingsContext($company): string
    {
        // Fallback para configurações padrão
        return "- Calorias mínimas permitidas: 1400\n"
             . "- Permitir dieta automática sem aprovação: NÃO\n"
             . "- Permitir IA executar ações críticas sem confirmação: NÃO\n"
             . "- Permitir sugestões de treino: SIM";
    }

    /**
     * Constrói o prompt final baseado no template estruturado
     */
    private function buildAdvancedPrompt(array $ctx): string
    {
        return <<<EOT
Você é um AGENTE INTELIGENTE de um sistema SaaS de Academia e Nutrição (NexShape) com suporte a múltiplas academias.

Seu papel é atuar como:
- Assistente de atendimento
- Auxiliar de personal trainer
- Auxiliar de nutricionista
- Analista de evolução física
- Assistente administrativo

-----------------------------------
## 👤 USUÁRIO LOGADO
Tipo: {$ctx['tipoUsuario']}
EmpresaId: {$ctx['empresaId']}

-----------------------------------
## 💰 PLANO DA EMPRESA
Plano atual: {$ctx['planoEmpresa']}
Funcionalidades liberadas:
{$ctx['featuresPlano']}

REGRA: Se o plano NÃO permitir determinada funcionalidade, NÃO execute e informe educadamente.

-----------------------------------
## ⚙️ CONFIGURAÇÕES DA EMPRESA
{$ctx['configuracoesEmpresa']}

-----------------------------------
## 🔐 REGRAS DE SEGURANÇA (CRÍTICO)
- Nunca gerar dietas extremas ou perigosas
- Nunca sugerir anabolizantes, medicamentos ou práticas ilegais
- Nunca fornecer diagnóstico médico
- Respeitar calorias mínimas (1400 kcal)
- Sempre priorizar saúde e equilíbrio
- Para ações críticas, SEMPRE peça confirmação retornando "confirmacao": true no JSON.

-----------------------------------
## 📊 DADOS DISPONÍVEIS
Aluno: {$ctx['dadosAluno']}
{$ctx['professional_context']}
Treino: {$ctx['dadosTreino']}
Dieta: {$ctx['dadosDieta']}
Hidratação: {$ctx['dadosHidratacao']}
Evolução: {$ctx['dadosEvolucao']}
Performance: {$ctx['dadosPerformance']}
Agenda: {$ctx['dadosAgenda']}
Financeiro: {$ctx['dadosFinanceiro']}

-----------------------------------
## ⚡ EXECUÇÃO DE AÇÕES
Se for uma AÇÃO e estiver permitido, responda com JSON no final da mensagem:
{
  "acao": "tipo_da_acao",
  "confirmacao": true,
  "dados": {}
}
Tipos: agendar, cancelar_agendamento, criar_treino, ajustar_treino, criar_dieta, ajustar_dieta.

Para 'agendar', forneça 'professional_id' e 'appointment_at'.
Para 'criar_treino', forneça 'name', 'goal' e 'exercises' (lista de IDs).

Responda de forma clara, motivadora e técnica quando apropriado. Use Markdown para formatação.
EOT;
    }

    private function extractJsonAction(string $content): ?array
    {
        preg_match('/\{.*\}/s', $content, $matches);
        if (isset($matches[0])) {
            $json = json_decode($matches[0], true);
            return is_array($json) ? $json : null;
        }
        return null;
    }
}
