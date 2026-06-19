<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\AIChat;
use App\Models\User;
use App\Services\AgentActionDispatcher;
use App\Services\IntelligenceLibraryService;
use App\Services\KnowledgeBaseResolverService;
use App\Services\AI\OrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use FormatsApiResponses;

    public function __construct(
        private OrchestratorService $orchestrator,
        private AgentActionDispatcher $actionDispatcher,
        private KnowledgeBaseResolverService $knowledgeBase,
        private IntelligenceLibraryService $libraryService
    ) {}

    public function send(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'force_ia' => ['nullable', 'boolean'],
        ]);

        if (! $user->hasPremiumAccess() && ! $user->isAdministrator()) {
            $limit = (int) config('projeto.chat_free_daily_user_messages', 8);
            $used = $this->countUserMessagesToday($user->id);
            if ($used >= $limit) {
                return $this->error('Limite diário de mensagens atingido.', 403, 'chat_quota_exceeded');
            }
        }

        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        $message = $validated['message'];
        $forceIa = $request->boolean('force_ia');

        if (! $forceIa) {
            $libraryHit = $this->libraryService->consultar($message, 'CHAT', 'GERAL');
            if ($libraryHit) {
                return $this->respondAssistant($user, $libraryHit->conteudo, null, 'library');
            }

            $kbHit = $this->knowledgeBase->resolve($message);
            if ($kbHit !== null) {
                return $this->respondAssistant($user, $kbHit['message'], null, 'knowledge_base');
            }
        }

        $result = $this->orchestrator->run($user, $message, [
            'source' => 'api_v1_chat',
            'clinicId' => $user->academy_company_id,
            'clinic_id' => $user->clinic_id,
            'feature_code' => 'ai_chat',
            'chat_history' => $this->buildChatHistory($user->id),
            'force_ia' => $forceIa,
        ]);

        if ($result['status'] === 'error') {
            return $this->error($result['error'] ?? 'Erro no processamento.', 500, 'orchestrator_error');
        }

        if ($result['status'] === 'limit_reached') {
            return $this->error('Créditos ou plano insuficientes.', 403, 'chat_quota_exceeded');
        }

        return $this->respondAssistant(
            $user,
            $result['message'],
            $result['action'] ?? null,
            'orchestrator'
        );
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = (int) min(max($request->integer('limit', 50), 1), 100);

        $messages = AIChat::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn (AIChat $chat) => [
                'id' => $chat->id,
                'role' => $chat->role,
                'message' => $chat->message,
                'created_at' => $chat->created_at?->toIso8601String(),
            ])
            ->values();

        return $this->success([
            'messages' => $messages,
        ], $this->chatQuotaMeta($user));
    }

    public function clear(Request $request): JsonResponse
    {
        AIChat::where('user_id', $request->user()->id)->delete();

        return $this->success(['cleared' => true]);
    }

    public function executeAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'array'],
            'action.acao' => ['required', 'string'],
            'action.dados' => ['nullable', 'array'],
        ]);

        $result = $this->actionDispatcher->dispatch($request->user(), $validated['action']);

        if ($result['ok'] ?? false) {
            AIChat::create([
                'user_id' => $request->user()->id,
                'role' => 'assistant',
                'message' => '✅ **Ação Executada:** '.($result['message'] ?? 'Sucesso.'),
            ]);
        }

        return response()->json(['data' => $result]);
    }

    private function respondAssistant(User $user, string $message, ?array $action, string $source): JsonResponse
    {
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $message,
        ]);

        return $this->success([
            'message' => $message,
            'action' => $action,
            'source' => $source,
        ], $this->chatQuotaMeta($user));
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
     * @return array<string, mixed>
     */
    private function chatQuotaMeta(User $user): array
    {
        $isPremium = $user->isPremiumActive() || $user->isAdministrator();
        $dailyLimit = $isPremium ? null : (int) config('projeto.chat_free_daily_user_messages', 8);

        return [
            'chat_quota' => [
                'is_premium' => $isPremium,
                'daily_user_limit' => $dailyLimit,
                'daily_user_used' => $this->countUserMessagesToday($user->id),
            ],
        ];
    }
}
