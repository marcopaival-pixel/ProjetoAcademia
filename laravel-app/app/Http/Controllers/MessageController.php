<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        
        // Buscar conversas onde o utilizador participa
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'userOne', 'userTwo'])
            ->get()
            ->sortByDesc(fn($c) => $c->messages->first()?->created_at);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeAccess($conversation);

        $messages = $conversation->messages()->with('sender')->oldest()->get();
        
        // Marcar mensagens como lidas
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeAccess($conversation);

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:messages,id',
        ]);

        // Apenas deleta se o utilizador for o remetente ou o destinatário (simplificado: via conversação)
        Message::whereIn('id', $request->ids)
            ->whereHas('conversation', function($q) {
                $q->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
            })
            ->delete();

        return redirect()->back()->with('success', 'Mensagens excluídas com sucesso.');
    }

    private function authorizeAccess(Conversation $conversation): void
    {
        if ($conversation->user_one_id !== Auth::id() && $conversation->user_two_id !== Auth::id()) {
            abort(403);
        }
    }
}
