<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AIChat;
use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
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

    public function send(Request $request, OrchestratorService $orchestrator, AiCreditService $credits): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $user = $request->user();

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
        ]);

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
