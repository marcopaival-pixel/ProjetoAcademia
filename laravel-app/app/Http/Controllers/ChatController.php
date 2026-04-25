<?php

namespace App\Http\Controllers;

use App\Models\AIChat;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AIChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private AIChatService $aiService) {}
    
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

        if (!$user->hasFeature('ai_training') && !$user->hasFeature('ai_nutrition')) {
            return response()->json([
                'ok' => false,
                'code' => 'plan_blocked',
                'error' => 'O assistente NexBot está disponível apenas no plano Pro. Faça upgrade agora para liberar seu assistente pessoal!',
                'plano_url' => route('plano'),
            ], 403);
        }

        // Salvar mensagem do usuário no histórico
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        // 1. Verificar Biblioteca Inteligente antes de consumir crédito e chamar IA
        $libraryService = app(\App\Services\IntelligenceLibraryService::class);
        $cachedResponse = $libraryService->consultar($validated['message']);

        if ($cachedResponse) {
            // Salvar resposta no histórico (vinda da biblioteca)
            AIChat::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'message' => $cachedResponse->conteudo,
            ]);

            return response()->json([
                'ok' => true,
                'message' => $cachedResponse->conteudo,
                'chat_quota' => $this->chatQuotaPayload($user),
                'from_library' => true
            ]);
        }

        // 2. Se não estiver na biblioteca, consumir crédito e chamar IA
        if (!$user->consumeAiCredit('chat_response', ['message_length' => strlen($validated['message'])])) {
             return response()->json([
                'ok' => false,
                'code' => 'chat_quota_exceeded',
                'error' => 'Créditos insuficientes. Adquira mais créditos para continuar conversando com o NexBot.',
                'plano_url' => route('plano'),
            ], 403);
        }

        // Obter métricas do usuário para contexto
        $userMetrics = $this->getUserMetrics($user->id);

        // Obter últimas 5 mensagens do histórico para contexto
        $conversationHistory = AIChat::where('user_id', $user->id)
            ->where('id', '!=', 0) // dummy to avoid issues with limit/orderBy
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(function ($chat) {
                return [
                    'role' => $chat->role,
                    'content' => $chat->message,
                ];
            })
            ->toArray();

        // Chamar IA
        $aiResponse = $this->aiService->chat(
            $validated['message'],
            $userMetrics,
            $conversationHistory
        );

        if (!$aiResponse['ok']) {
            return response()->json([
                'ok' => false,
                'error' => $aiResponse['error'],
            ], 500);
        }

        // Salvar resposta da IA no histórico do usuário
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $aiResponse['message'],
        ]);

        // 3. Salvar resposta na Biblioteca Inteligente para uso futuro global
        $libraryService->salvarRespostaIA([
            'message' => $aiResponse['message'],
            'titulo' => $validated['message'],
        ], 'CHAT', 'GERAL', $validated['message']);

        return response()->json([
            'ok' => true,
            'message' => $aiResponse['message'],
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
     * @return array{is_premium: bool, daily_user_limit: int|null, daily_user_used: int}
     */
    private function chatQuotaPayload(User $user): array
    {
        $hasAi = $user->hasFeature('ai_training') || $user->hasFeature('ai_nutrition');
        $remaining = $user->getRemainingAiCredits();
        $limit = $user->getPlanLimit('ai_credits');

        return [
            'is_premium' => $user->isPremiumActive(),
            'has_ai_access' => $hasAi,
            'daily_user_limit' => $limit,
            'remaining_credits' => $remaining,
        ];
    }
}
