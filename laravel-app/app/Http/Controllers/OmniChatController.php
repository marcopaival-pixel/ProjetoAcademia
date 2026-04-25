<?php

namespace App\Http\Controllers;

use App\Models\OmniConversation;
use App\Models\OmniMessage;
use App\Services\OmniChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OmniChatController extends Controller
{
    private $chatService;

    public function __construct(OmniChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Recebe mensagem do Widget ou API externa
     */
    public function receiveMessage(Request $request)
    {
        $secret = (string) config('projeto.omni_webhook_secret', '');
        if ($secret !== '') {
            $sent = (string) $request->header('X-Omni-Secret', '');
            if ($sent === '' || ! hash_equals($secret, $sent)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
        }

        $validated = $request->validate([
            'company_slug' => 'required|string',
            'channel_type' => 'required|string',
            'customer_id' => 'required|string',
            'customer_name' => 'nullable|string',
            'content' => 'required|string',
            'content_type' => 'nullable|string',
        ]);

        $message = $this->chatService->handleIncomingMessage($validated);

        return response()->json([
            'status' => 'success',
            'message_id' => $message->id
        ]);
    }

    /**
     * Resposta do atendente
     */
    public function agentReply(Request $request, $conversationId)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'content_type' => 'nullable|string',
        ]);

        $conversation = OmniConversation::findOrFail($conversationId);
        
        // Verifica se o atendente tem permissão (seria via middleware ou Policy)
        
        $message = OmniMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'sender_id' => Auth::id(),
            'content' => $validated['content'],
            'content_type' => $validated['content_type'] ?? 'text',
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => 'open' // Garante que saiu do modo bot
        ]);

        return response()->json($message);
    }

    /**
     * Histórico de conversa
     */
    public function getHistory($conversationId)
    {
        $messages = OmniMessage::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Lista de conversas ativas (Dashboard)
     */
    public function activeConversations(Request $request)
    {
        $query = OmniConversation::with(['channel', 'agent', 'queue'])
            ->where('status', '!=', 'closed')
            ->orderBy('last_message_at', 'desc');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Exibe a interface de gestão de bots (IA)
     */
    public function bots()
    {
        return view('admin.omnichannel-bots');
    }
}
