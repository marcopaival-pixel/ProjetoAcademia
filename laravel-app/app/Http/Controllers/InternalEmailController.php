<?php

namespace App\Http\Controllers;

use App\Models\InternalEmail;
use App\Models\InternalEmailAnexo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class InternalEmailController extends Controller
{
    public function index(Request $request): View
    {
        return $this->inbox($request);
    }

    public function inbox(Request $request): View
    {
        $query = InternalEmail::inbox(Auth::id())
            ->with(['sender', 'attachments'])
            ->whereHas('sender', function($q) {
                // Não mostrar e-mails de quem eu bloqueei
                $q->whereDoesntHave('blockers', function($sq) { $sq->where('blocker_id', Auth::id()); });
            })
            ->orderBy('sent_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $messages = $query->paginate(20);
        $unreadCount = InternalEmail::inbox(Auth::id())->where('is_read', false)->count();

        return view('internal-email.inbox', compact('messages', 'unreadCount'));
    }

    public function sent(Request $request): View
    {
        $query = InternalEmail::sent(Auth::id())
            ->with(['recipient', 'attachments'])
            ->orderBy('sent_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);
        return view('internal-email.sent', compact('messages'));
    }

    public function outbox(): View
    {
        $messages = InternalEmail::outbox(Auth::id())
            ->with(['recipient', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('internal-email.outbox', compact('messages'));
    }

    public function trash(): View
    {
        $messages = InternalEmail::trash(Auth::id())
            ->with(['sender', 'recipient'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('internal-email.trash', compact('messages'));
    }

    public function create(Request $request): View
    {
        $currentUser = Auth::user();
        $users = User::where('id', '!=', $currentUser->id)
            ->whereDoesntHave('blockers', function ($query) use ($currentUser) {
                $query->where('blocker_id', $currentUser->id);
            })
            ->whereDoesntHave('blockedUsers', function ($query) use ($currentUser) {
                $query->where('blocked_id', $currentUser->id);
            })
            ->get()
            ->filter(function($user) use ($currentUser) {
                return $currentUser->canMessage($user);
            });

        $replyTo = null;
        if ($request->has('reply_to')) {
            $replyTo = InternalEmail::find($request->reply_to);
        }

        $preSelectedTo = $request->query('to');

        return view('internal-email.create', compact('users', 'replyTo', 'preSelectedTo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_id' => 'required|array',
            'recipient_id.*' => 'exists:users,id',
            'subject' => 'required|string|max:200',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB limit
            'parent_id' => 'nullable|exists:internal_emails,id',
        ]);

        $currentUser = Auth::user();

        foreach ($request->recipient_id as $destId) {
            $recebedor = User::find($destId);
            
            if (!$currentUser->canMessage($recebedor)) {
                continue; // Pular usuários sem permissão técnica (silenciosamente ou com aviso?)
            }

            if ($currentUser->isBlocking($recebedor) || $currentUser->isBlockedBy($recebedor)) {
                continue; // Pular usuários bloqueados
            }

            $message = InternalEmail::create([
                'sender_id' => $currentUser->id,
                'recipient_id' => $destId,
                'subject' => $request->subject,
                'content' => $request->content,
                'sent_at' => now(),
                'status' => 'sent',
                'parent_id' => $request->parent_id,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('email_attachments', 'public');
                    InternalEmailAnexo::create([
                        'email_id' => $message->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            // LGPD: Log interaction
            Log::info("Interna Message sent by User " . $currentUser->id . " to User " . $destId, [
                'msg_id' => $message->id,
                'subject' => $request->subject
            ]);
        }

        return redirect()->route('internal-email.sent')->with('success', 'Mensagem enviada com sucesso!');
    }

    public function show(InternalEmail $message): View
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        if ($message->recipient_id === Auth::id() && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $message->load(['sender', 'recipient', 'attachments', 'replies.sender']);

        return view('internal-email.show', compact('message'));
    }

    public function markAsRead(InternalEmail $message): RedirectResponse
    {
        if ($message->recipient_id === Auth::id()) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }
        return redirect()->back()->with('success', 'Mensagem marcada como lida.');
    }

    public function markAsUnread(InternalEmail $message): RedirectResponse
    {
        if ($message->recipient_id === Auth::id()) {
            $message->update(['is_read' => false, 'read_at' => null]);
        }
        return redirect()->back()->with('success', 'Mensagem marcada como não lida.');
    }

    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        try {
            $count = InternalEmail::inbox(Auth::id())
                ->where('is_read', false)
                ->whereHas('sender', function($q) {
                    $q->whereDoesntHave('blockers', function($sq) { $sq->where('blocker_id', auth()->id()); });
                })
                ->count();
        } catch (\Exception $e) {
            $count = 0;
        }
        
        return response()->json([
            'count' => $count
        ]);
    }

    public function destroy(InternalEmail $message): RedirectResponse
    {
        if ($message->sender_id === Auth::id()) {
            $message->update(['excluded_at_sender' => now()]);
        }
        
        if ($message->recipient_id === Auth::id()) {
            $message->update(['excluded_at_receiver' => now()]);
        }

        return redirect()->route('internal-email.inbox')->with('success', 'Mensagem movida para a lixeira.');
    }

    public function restore(InternalEmail $message): RedirectResponse
    {
        if ($message->sender_id === Auth::id()) {
            $message->update(['excluded_at_sender' => null]);
        }
        
        if ($message->recipient_id === Auth::id()) {
            $message->update(['excluded_at_receiver' => null]);
        }

        return redirect()->back()->with('success', 'Mensagem restaurada.');
    }

    public function permanentDelete(InternalEmail $message): RedirectResponse
    {
        // Actually delete if both excluded or only one side exists
        // But for simplicity, we just delete if the user requesting is authorized
        if ($message->sender_id === Auth::id() || $message->recipient_id === Auth::id()) {
            // Delete attachments from storage
            foreach ($message->attachments as $anexo) {
                Storage::disk('public')->delete($anexo->file_path);
                $anexo->delete();
            }
            $message->delete();
        }

        return redirect()->route('internal-email.trash')->with('success', 'Mensagem excluída permanentemente.');
    }
}
