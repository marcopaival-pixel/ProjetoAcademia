<?php

namespace App\Http\Controllers;

use App\Models\AIChat;
use App\Models\User;
use App\Support\AiStudentWriteGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private \App\Services\AI\OrchestratorService $orchestrator,
        private \App\Services\AgentActionDispatcher $actionDispatcher,
        private \App\Services\AiCreditService $aiCredits,
        private \App\Services\AiChatConsentService $chatConsent,
        private \App\Services\StudentContextService $studentContext,
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
            'accept_ai_chat_health_data' => ['sometimes', 'boolean'],
        ]);

        $this->chatConsent->ensureConsent($user, $request);

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

        if (! $this->aiCredits->hasCredits($user, 'chat_response')) {
            return response()->json([
                'ok' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para conversar com o NexBot.',
                'plano_url' => route('plano'),
            ], 402);
        }

        // Salvar mensagem do usuário no histórico
        $conversationHistory = $this->studentContext->conversationHistory($user->id);
        $userMetrics = $this->studentContext->metrics($user);

        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        // Chamar Orquestrador Central
        $result = $this->orchestrator->run($user, $validated['message'], [
            'source' => 'chat_page',
            'clinic_id' => $user->clinic_id,
            'academy_company_id' => $user->academy_company_id,
            'feature_key' => 'chat',
            'conversation_history' => $conversationHistory,
            'user_metrics' => $userMetrics,
        ]);

        if ($result['status'] === 'error') {
            return response()->json([
                'ok' => false,
                'error' => $this->friendlyAiError($result['error'] ?? null),
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

        if (! $this->aiCredits->consume($user, 'chat_response', [
            'source' => 'chat_page',
            'message_chars' => mb_strlen($validated['message']),
        ])) {
            return response()->json([
                'ok' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para registrar esta resposta.',
                'plano_url' => route('plano'),
            ], 402);
        }

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
            'action.acao' => 'required|string|in:'.implode(',', AiStudentWriteGuard::writeActionCodes()),
            'action.dados' => 'nullable|array',
        ]);

        if (! AiStudentWriteGuard::writesEnabled()) {
            return response()->json([
                'ok' => false,
                'code' => 'ai_writes_disabled',
                'error' => 'Escritas automaticas da IA estao desativadas neste ambiente.',
            ], 403);
        }

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
     *
     * @deprecated Use StudentContextService::metrics() directly.
     */
    private function getUserMetrics(int $userId): array
    {
        return $this->studentContext->metrics(User::findOrFail($userId));
    }

    private function countUserMessagesToday(int $userId): int
    {
        return AIChat::query()
            ->where('user_id', $userId)
            ->where('role', 'user')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    private function conversationHistory(int $userId, int $limit = 16): array
    {
        return $this->studentContext->conversationHistory($userId, $limit);
    }

    private function friendlyAiError(?string $error): string
    {
        if (! $error) {
            return 'O NexBot ficou indisponivel por um instante. Tente novamente em alguns segundos.';
        }

        if (str_contains($error, 'API Key') || str_contains($error, 'OpenAI')) {
            report(new \RuntimeException('NexBot provider error: ' . $error));

            return 'O servico de IA esta temporariamente indisponivel. A equipe tecnica ja tem dados para verificar a configuracao.';
        }

        return 'Nao consegui concluir essa resposta agora. Tente reformular a pergunta ou tente novamente em alguns segundos.';
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
