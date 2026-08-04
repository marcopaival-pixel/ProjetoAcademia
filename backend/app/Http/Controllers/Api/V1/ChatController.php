<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AIChat;
use App\Models\User;
use App\Services\AI\OrchestratorService;
use App\Services\AiChatConsentService;
use App\Services\AiCreditService;
use App\Services\StudentContextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private StudentContextService $studentContext,
    ) {}

    public function consent(Request $request, AiChatConsentService $consentService): JsonResponse
    {
        $consent = $consentService->latestConsent($request->user());

        return response()->json([
            'data' => [
                'required' => true,
                'type' => AiChatConsentService::CONSENT_TYPE,
                'version' => AiChatConsentService::CONSENT_VERSION,
                'accepted' => $consent !== null,
                'accepted_at' => $consent?->created_at?->toIso8601String(),
                'copy' => [
                    'title' => 'Uso de dados de saude no chat com IA',
                    'summary' => 'O NexBot pode usar seu perfil, metas e registros recentes para personalizar respostas. Os dados sao enviados ao provedor de IA apenas para esta conversa.',
                ],
            ],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $messages = AIChat::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->limit((int) min(max($request->integer('limit', 50), 1), 100))
            ->get()
            ->reverse()
            ->map(fn (AIChat $chat) => [
                'id' => $chat->id,
                'role' => $chat->role,
                'message' => $chat->message,
                'created_at' => optional($chat->created_at)->toISOString(),
            ])
            ->values();

        return response()->json(['data' => ['messages' => $messages]]);
    }

    public function send(
        Request $request,
        OrchestratorService $orchestrator,
        AiCreditService $credits,
        AiChatConsentService $consentService,
    ): JsonResponse {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'accept_ai_chat_health_data' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        $consentService->ensureConsentForApi($user, $request);

        if (! $credits->hasCredits($user, 'chat_response')) {
            return response()->json([
                'message' => 'Creditos de IA insuficientes.',
                'code' => 'credits_exceeded',
            ], 402);
        }

        AIChat::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        $result = $orchestrator->run($user, $validated['message'], [
            'source' => 'android',
            'clinic_id' => $user->clinic_id,
            'academy_company_id' => $user->academy_company_id,
            'feature_key' => 'chat',
            'conversation_history' => $this->studentContext->conversationHistory($user->id),
            'user_metrics' => $this->studentContext->metrics($user),
        ]);

        if (($result['status'] ?? null) !== 'success') {
            return response()->json([
                'message' => 'Nao foi possivel gerar a resposta agora.',
                'source' => 'orchestrator',
            ], 422);
        }

        $message = (string) ($result['message'] ?? '');
        $credits->consume($user, 'chat_response', [
            'source' => 'android',
            'message_chars' => mb_strlen($validated['message']),
        ], hash('sha256', $user->id.'|'.$validated['message'].'|'.now()->format('Y-m-d-H-i')));

        AIChat::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'message' => $message,
        ]);

        return response()->json([
            'data' => [
                'message' => $message,
                'source' => 'orchestrator',
            ],
        ]);
    }
}
