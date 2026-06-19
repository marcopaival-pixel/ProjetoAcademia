<?php

namespace App\Http\Controllers;

use App\Models\AIChat;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private \App\Services\AI\OrchestratorService $orchestrator,
        private \App\Services\AgentActionDispatcher $actionDispatcher,
        private \App\Services\KnowledgeBaseResolverService $knowledgeBase,
        private \App\Services\IntelligenceLibraryService $libraryService
    ) {}
    
    /**
     * Exibir a página do NexBot
     */
    public function index(): View
    {
        return view('chat-page');
    }

    /**
     * Enviar mensagem para o chatbot
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['ok' => false, 'error' => 'Não autenticado'], 401);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if (! $user->hasPremiumAccess() && ! $user->isAdministrator()) {
            $limit = (int) config('projeto.chat_free_daily_user_messages', 8);
            $used = $this->countUserMessagesToday($user->id);
            if ($used >= $limit) {
                return response()->json([
                    'ok' => false,
                    'code' => 'chat_quota_exceeded',
                    'error' => 'Limite diário de mensagens atingido.',
                    'quota' => [
                        'limit' => $limit,
                        'used' => $used,
                    ],
                    'plano_url' => route('plano'),
                ], 403);
            }
        }

        // Salvar mensagem do usuário no histórico
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        $message = $validated['message'];
        $forceIa = $request->boolean('force_ia');

        // 1) Biblioteca interna → 2) Base de conhecimento → 3) Orquestrador (LLM)
        if (! $forceIa) {
            $libraryHit = $this->libraryService->consultar($message, 'CHAT', 'GERAL');
            if ($libraryHit) {
                $assistantMessage = $libraryHit->conteudo;
                AIChat::create([
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'message' => $assistantMessage,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => $assistantMessage,
                    'action' => null,
                    'source' => 'library',
                    'chat_quota' => $this->chatQuotaPayload($user),
                ]);
            }

            $kbHit = $this->knowledgeBase->resolve($message);
            if ($kbHit !== null) {
                $assistantMessage = $kbHit['message'];
                AIChat::create([
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'message' => $assistantMessage,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => $assistantMessage,
                    'action' => null,
                    'source' => 'knowledge_base',
                    'chat_quota' => $this->chatQuotaPayload($user),
                ]);
            }
        }

        $chatHistory = $this->buildChatHistory($user->id);

        $result = $this->orchestrator->run($user, $message, [
            'source' => 'chat_page',
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
            'feature_code' => 'ai_chat',
            'chat_history' => $chatHistory,
            'force_ia' => $forceIa,
        ]);

        if ($result['status'] === 'error') {
            return response()->json([
                'ok' => false,
                'error' => $result['error'],
            ], 500);
        }

        if ($result['status'] === 'limit_reached') {
            return response()->json([
                'ok' => false,
                'code' => 'chat_quota_exceeded',
                'error' => 'Créditos ou plano insuficientes.',
                'plano_url' => route('plano'),
            ], 403);
        }

        $assistantMessage = $result['message'];
        $action = $result['action'] ?? null;

        // Salvar resposta da IA no histórico do usuário
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $assistantMessage,
        ]);

        return response()->json([
            'ok' => true,
            'message' => $assistantMessage,
            'action' => $action, 
            'chat_quota' => $this->chatQuotaPayload($user),
        ]);
    }

    /**
     * Obter histórico de chat
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['ok' => false, 'error' => 'Não autenticado'], 401);
        }

        $limit = (int) $request->query('limit', 50);

        $messages = AIChat::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'role' => $chat->role,
                    'message' => $chat->message,
                    'created_at' => $chat->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'ok' => true,
            'messages' => $messages,
            'chat_quota' => $this->chatQuotaPayload($user),
        ]);
    }

    /**
     * Limpar histórico de chat
     */
    public function clearHistory(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['ok' => false, 'error' => 'Não autenticado'], 401);
        }

        AIChat::where('user_id', $user->id)->delete();

        return response()->json(['ok' => true, 'message' => 'Histórico limpo']);
    }

    /**
     * Executar uma ação estruturada gerada pela IA após confirmação do usuário
     */
    public function executeAction(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['ok' => false, 'error' => 'Não autenticado'], 401);
        }

        $validated = $request->validate([
            'action' => 'required|array',
            'action.acao' => 'required|string',
            'action.dados' => 'nullable|array',
        ]);

        $result = $this->actionDispatcher->dispatch($user, $validated['action']);

        if ($result['ok']) {
            // Salva um log da ação no chat para feedback visual
            AIChat::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'message' => "✅ **Ação Executada:** " . ($result['message'] ?? 'Sucesso.'),
            ]);
        }

        return response()->json($result);
    }

    /**
     * Obter métricas profundas do usuário para contexto da IA (Nutrição, Treino, Hidratação)
     */
    private function getUserMetrics(int $userId): array
    {
        $user = User::findOrFail($userId);
        $profile = UserProfile::where('user_id', $userId)->first();
        
        // 1. Nutrição (Serviço de Nutrição)
        $nutritionService = app(\App\Services\Nutrition::class);
        $dailyTarget = $nutritionService->dailyTargetKcal($user);
        $nutritionLogs = $nutritionService->getLogs($user, now()->toDateString());
        
        // 2. Hidratação
        $waterTarget = $profile?->water_goal ?? ($user->weight * 35); // Fallback: 35ml/kg
        $waterConsumed = \App\Models\WaterEntry::where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount_ml');

        // 3. Último Treino
        $lastWorkout = \App\Models\WorkoutSession::where('user_id', $userId)
            ->with('trainingPlan')
            ->latest()
            ->first();

        $metrics = [
            'name' => $user->name,
            'objective' => $profile?->goal ?? 'manter peso',
            'current_weight' => $user->weight ?? $profile?->weight,
            'goal_weight' => $profile?->goal_weight ?? null,
            'biological_sex' => $profile?->biological_sex ?? 'não informado',
            
            // Nutrição Real
            'daily_calories_target' => $dailyTarget,
            'consumed_calories_today' => $nutritionLogs['consumed']['kcal'] ?? 0,
            'protein_target' => $nutritionService->dailyTargetMacros($user)['protein'] ?? 0,
            
            // Hidratação
            'water_target_ml' => $waterTarget,
            'water_consumed_ml' => $waterConsumed,

            // Performance
            'last_workout_name' => $lastWorkout?->trainingPlan?->name ?? 'Nenhum treino registrado recentemente',
            'last_workout_date' => $lastWorkout?->created_at?->diffForHumans() ?? 'N/A',
        ];

        return $metrics;
    }

    private function countUserMessagesToday(int $userId): int
    {
        return AIChat::query()
            ->where('user_id', $userId)
            ->where('role', 'user')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    /**
     * Histórico compacto para memória conversacional (sliding window).
     *
     * @return array<int, array{role: string, content: string}>
     */
    private function buildChatHistory(int $userId): array
    {
        $limit = config('ai.chat_history_messages', 6);

        return AIChat::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn ($chat) => [
                'role' => $chat->role === 'assistant' ? 'assistant' : 'user',
                'content' => (string) $chat->message,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{is_premium: bool, has_ai_access: bool, daily_user_limit: int|null, daily_user_used: int, remaining_credits: int}
     */
    private function chatQuotaPayload(User $user): array
    {
        $isPremium = $user->isPremiumActive() || $user->isAdministrator();
        $dailyLimit = $isPremium ? null : (int) config('projeto.chat_free_daily_user_messages', 8);
        $dailyUsed = $this->countUserMessagesToday($user->id);

        return [
            'is_premium' => $isPremium,
            'has_ai_access' => $user->hasFeature('ai_training') || $user->hasFeature('ai_nutrition'),
            'daily_user_limit' => $dailyLimit,
            'daily_user_used' => $dailyUsed,
            'remaining_credits' => (int) (\App\Models\AiCreditWallet::query()
                ->where('user_id', $user->id)
                ->value('balance') ?? 0),
        ];
    }
}
