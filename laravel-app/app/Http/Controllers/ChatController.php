<?php

namespace App\Http\Controllers;

use App\Models\AIChat;
use App\Models\UserProfile;
use App\Services\AIChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private AIChatService $aiService) {}

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

        // Salvar mensagem do usuário
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        // Obter métricas do usuário para contexto
        $userMetrics = $this->getUserMetrics($user->id);

        // Obter últimas 5 mensagens do histórico para contexto
        $conversationHistory = AIChat::where('user_id', $user->id)
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

        // Salvar resposta da IA
        AIChat::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $aiResponse['message'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => $aiResponse['message'],
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
     * Obter métricas do usuário para contexto
     */
    private function getUserMetrics(int $userId): array
    {
        $profile = UserProfile::where('user_id', $userId)->first();

        $metrics = [
            'objective' => $profile?->objective ?? 'manter peso',
            'current_weight' => $profile?->weight ?? null,
            'goal_weight' => $profile?->goal_weight ?? null,
        ];

        // Remover valores null
        return array_filter($metrics, fn($value) => $value !== null);
    }
}
