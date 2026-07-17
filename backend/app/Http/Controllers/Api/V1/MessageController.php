<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::query()
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['messages' => fn ($query) => $query->latest()->limit(1), 'userOne', 'userTwo'])
            ->get()
            ->sortByDesc(fn (Conversation $conversation) => optional($conversation->messages->first()?->created_at ?? $conversation->created_at)->timestamp)
            ->values();

        return response()->json([
            'data' => [
                'conversations' => $conversations->map(fn (Conversation $conversation) => $this->conversationPayload($conversation, $userId)),
            ],
        ]);
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation->load(['messages.sender', 'userOne', 'userTwo']);

        return response()->json([
            'data' => [
                'conversation' => $this->conversationPayload($conversation, $request->user()->id),
                'messages' => $conversation->messages->sortBy('created_at')->map(fn (Message $message) => $this->messagePayload($message, $request->user()->id))->values(),
            ],
        ]);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $currentUser = $request->user();
        $otherUser = $conversation->getOtherUser($currentUser->id);

        if (! $otherUser || ! $currentUser->canMessage($otherUser)) {
            return response()->json(['message' => 'Voce nao tem permissao para enviar mensagens para este utilizador.'], 403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $currentUser->id,
            'content' => $validated['content'],
        ]);

        $message->load('sender');

        return response()->json([
            'data' => [
                'message' => $this->messagePayload($message, $currentUser->id),
            ],
        ], 201);
    }

    public function startSupport(Request $request): JsonResponse
    {
        $user = $request->user();
        $admin = User::where('is_admin', true)->first();

        if (! $admin) {
            return response()->json(['message' => 'Nenhum administrador disponivel no momento.'], 422);
        }

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $user->id,
            'tipo' => Conversation::TIPO_SUPORTE,
            'status' => Conversation::STATUS_ABERTO,
        ], [
            'user_two_id' => $admin->id,
        ]);

        $conversation->load(['messages' => fn ($query) => $query->latest()->limit(1), 'userOne', 'userTwo']);

        return response()->json([
            'data' => [
                'conversation' => $this->conversationPayload($conversation, $user->id),
            ],
        ]);
    }

    private function authorizeConversation(Request $request, Conversation $conversation): void
    {
        if ($conversation->user_one_id !== $request->user()->id && $conversation->user_two_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function conversationPayload(Conversation $conversation, int $userId): array
    {
        $otherUser = $conversation->getOtherUser($userId);
        $last = $conversation->messages->first();

        return [
            'id' => $conversation->id,
            'type' => $conversation->tipo,
            'status' => $conversation->status,
            'other_user_name' => $otherUser?->name,
            'last_message' => $last?->content,
            'last_message_at' => optional($last?->created_at ?? $conversation->created_at)->toISOString(),
        ];
    }

    private function messagePayload(Message $message, int $userId): array
    {
        return [
            'id' => $message->id,
            'sender_name' => $message->sender?->name,
            'content' => $message->content,
            'is_mine' => $message->sender_id === $userId,
            'is_read' => $message->is_read,
            'created_at' => optional($message->created_at)->toISOString(),
        ];
    }
}
