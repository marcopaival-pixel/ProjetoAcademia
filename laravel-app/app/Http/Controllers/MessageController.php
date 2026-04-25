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
            ->get();

        // Filtrar conversas com usuários que me bloquearam (opcional, ou apenas ocultar unread)
        // Por agora, vamos apenas manter a lista mas impedir novas mensagens.

        // Ordenar: primeiro as que têm mensagens (pela data da última), depois as vazias (pela data de criação)
        $conversations = $conversations->sort(function ($a, $b) {
            $dateA = $a->messages->first()?->created_at ?: $a->created_at;
            $dateB = $b->messages->first()?->created_at ?: $b->created_at;
            return $dateB <=> $dateA;
        });

        return view('messages.index', compact('conversations'));
    }

    public function create(Request $request): View
    {
        $search = $request->input('search');
        $currentUser = Auth::user();

        $users = User::where('id', '!=', $currentUser->id)
            ->when($search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get()
            ->filter(function($user) use ($currentUser) {
                return $currentUser->canMessage($user);
            })
            ->take(10);

        return view('messages.create', compact('users', 'search'));
    }

    public function startConversation(Request $request): RedirectResponse
    {
        $recipientId = $request->input('user_id');
        $currentUser = Auth::user();

        if ($recipientId == $currentUser->id) {
            return redirect()->back()->with('error', 'Você não pode conversar consigo mesmo.');
        }

        $recipient = User::findOrFail($recipientId);
        if (!$currentUser->canMessage($recipient)) {
            return redirect()->back()->with('error', 'Você não tem permissão para iniciar uma conversa com este utilizador. Entre em um grupo comum para liberar a comunicação.');
        }

        // Verificar se já existe uma conversa entre estes dois usuários
        $conversation = Conversation::where(function($q) use ($currentUser, $recipientId) {
            $q->where('user_one_id', $currentUser->id)->where('user_two_id', $recipientId);
        })->orWhere(function($q) use ($currentUser, $recipientId) {
            $q->where('user_one_id', $recipientId)->where('user_two_id', $currentUser->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $currentUser->id,
                'user_two_id' => $recipientId,
            ]);
        }

        return redirect()->route('messages.show', $conversation);
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

        $otherUser = $conversation->getOtherUser(Auth::id());
        $canReply = Auth::user()->canMessage($otherUser);

        return view('messages.show', compact('conversation', 'messages', 'canReply'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeAccess($conversation);

        $currentUser = Auth::user();
        $otherUser = $conversation->getOtherUser($currentUser->id);
        
        if (!$currentUser->canMessage($otherUser)) {
            return redirect()->back()->with('error', 'Você não tem permissão para enviar mensagens para este utilizador.');
        }

        if ($currentUser->isBlocking($otherUser) || $currentUser->isBlockedBy($otherUser)) {
            return redirect()->back()->with('error', 'Não é possível enviar mensagens para este utilizador.');
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $conversation->messages()->create([
            'sender_id' => $currentUser->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        $count = Message::whereHas('conversation', function($q) {
                $q->where('user_one_id', Auth::id())->orWhere('user_two_id', Auth::id());
            })
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->whereHas('sender', function($q) {
                $q->whereDoesntHave('blockers', function($sq) { $sq->where('blocker_id', Auth::id()); });
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    public function blockUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Ação inválida.');
        }

        if (Auth::user()->isBlocking($user)) {
            Auth::user()->blockedUsers()->detach($user->id);
            $msg = 'Utilizador desbloqueado.';
        } else {
            Auth::user()->blockedUsers()->attach($user->id);
            $msg = 'Utilizador bloqueado com sucesso.';
        }

        return redirect()->back()->with('success', $msg);
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
