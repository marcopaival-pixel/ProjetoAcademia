<?php

namespace App\Http\Controllers;

use App\Models\OmniBot;
use App\Models\OmniBotStep;
use App\Models\OmniBotOption;
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
        $bots = OmniBot::with(['steps.options'])->get();
        return view('admin.omnichannel-bots', compact('bots'));
    }

    /**
     * Cria um novo bot
     */
    public function storeBot(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'out_of_office_message' => 'nullable|string',
            'business_hours' => 'nullable|array',
        ]);

        $bot = OmniBot::create([
            'company_id' => 1,
            'name' => $validated['name'],
            'whatsapp_phone' => $validated['whatsapp_phone'],
            'is_active' => $validated['is_active'] ?? true,
            'out_of_office_message' => $validated['out_of_office_message'] ?? null,
            'business_hours' => $validated['business_hours'] ?? null,
        ]);

        return back()->with('success', 'Bot criado com sucesso!');
    }

    public function updateBot(Request $request, OmniBot $bot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'out_of_office_message' => 'nullable|string',
            'business_hours' => 'nullable|array',
        ]);

        $bot->update($validated);

        return back()->with('success', 'Bot atualizado com sucesso!');
    }

    public function destroyBot(OmniBot $bot)
    {
        $bot->delete();
        return back()->with('success', 'Bot removido com sucesso!');
    }

    // Step Management
    public function storeStep(Request $request, OmniBot $bot)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:message,menu,question,transfer',
            'content' => 'required|string',
            'is_start' => 'boolean',
            'next_step_id' => 'nullable|exists:omni_bot_steps,id',
        ]);

        $bot->steps()->create($validated);
        return back()->with('success', 'Passo criado com sucesso!');
    }

    public function updateStep(Request $request, OmniBotStep $step)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:message,menu,question,transfer',
            'content' => 'required|string',
            'is_start' => 'boolean',
            'next_step_id' => 'nullable|exists:omni_bot_steps,id',
        ]);

        $step->update($validated);
        return back()->with('success', 'Passo atualizado!');
    }

    public function destroyStep(OmniBotStep $step)
    {
        $step->delete();
        return back()->with('success', 'Passo removido!');
    }

    // Option Management
    public function storeOption(Request $request, OmniBotStep $step)
    {
        $validated = $request->validate([
            'trigger_value' => 'required|string|max:50',
            'label' => 'required|string|max:100',
            'destination_step_id' => 'required|exists:omni_bot_steps,id',
        ]);

        $step->options()->create($validated);
        return back()->with('success', 'Opção adicionada!');
    }

    public function destroyOption(OmniBotOption $option)
    {
        $option->delete();
        return back()->with('success', 'Opção removida!');
    }
}
