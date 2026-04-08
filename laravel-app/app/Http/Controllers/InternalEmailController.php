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
            ->with(['remetente', 'anexos'])
            ->orderBy('data_envio', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('assunto', 'like', "%{$search}%")
                  ->orWhere('mensagem', 'like', "%{$search}%")
                  ->orWhereHas('remetente', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $messages = $query->paginate(20);
        $unreadCount = InternalEmail::inbox(Auth::id())->where('lida', false)->count();

        return view('internal-email.inbox', compact('messages', 'unreadCount'));
    }

    public function sent(Request $request): View
    {
        $query = InternalEmail::sent(Auth::id())
            ->with(['destinatario', 'anexos'])
            ->orderBy('data_envio', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('assunto', 'like', "%{$search}%")
                  ->orWhere('mensagem', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);
        return view('internal-email.sent', compact('messages'));
    }

    public function outbox(): View
    {
        $messages = InternalEmail::outbox(Auth::id())
            ->with(['destinatario', 'anexos'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('internal-email.outbox', compact('messages'));
    }

    public function trash(): View
    {
        $messages = InternalEmail::trash(Auth::id())
            ->with(['remetente', 'destinatario'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('internal-email.trash', compact('messages'));
    }

    public function create(Request $request): View
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $replyTo = null;
        if ($request->has('reply_to')) {
            $replyTo = InternalEmail::find($request->reply_to);
        }
        return view('internal-email.create', compact('users', 'replyTo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'destinatario_id' => 'required|array',
            'destinatario_id.*' => 'exists:users,id',
            'assunto' => 'required|string|max:200',
            'mensagem' => 'required|string',
            'anexos.*' => 'nullable|file|max:10240', // 10MB limit
            'parent_id' => 'nullable|exists:internal_emails,id',
        ]);

        foreach ($request->destinatario_id as $destId) {
            $message = InternalEmail::create([
                'remetente_id' => Auth::id(),
                'destinatario_id' => $destId,
                'assunto' => $request->assunto,
                'mensagem' => $request->mensagem,
                'data_envio' => now(),
                'status' => 'sent',
                'parent_id' => $request->parent_id,
            ]);

            if ($request->hasFile('anexos')) {
                foreach ($request->file('anexos') as $file) {
                    $path = $file->store('email_attachments', 'public');
                    InternalEmailAnexo::create([
                        'mensagem_id' => $message->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            // LGPD: Log interaction
            Log::info("Interna Message sent by User " . Auth::id() . " to User " . $destId, [
                'msg_id' => $message->id,
                'subject' => $request->assunto
            ]);
        }

        return redirect()->route('internal-email.sent')->with('success', 'Mensagem enviada com sucesso!');
    }

    public function show(InternalEmail $message): View
    {
        if ($message->destinatario_id !== Auth::id() && $message->remetente_id !== Auth::id()) {
            abort(403);
        }

        if ($message->destinatario_id === Auth::id() && !$message->lida) {
            $message->update([
                'lida' => true,
                'data_leitura' => now(),
            ]);
        }

        $message->load(['remetente', 'destinatario', 'anexos', 'replies.remetente']);

        return view('internal-email.show', compact('message'));
    }

    public function markAsRead(InternalEmail $message): RedirectResponse
    {
        if ($message->destinatario_id === Auth::id()) {
            $message->update(['lida' => true, 'data_leitura' => now()]);
        }
        return redirect()->back()->with('success', 'Mensagem marcada como lida.');
    }

    public function markAsUnread(InternalEmail $message): RedirectResponse
    {
        if ($message->destinatario_id === Auth::id()) {
            $message->update(['lida' => false, 'data_leitura' => null]);
        }
        return redirect()->back()->with('success', 'Mensagem marcada como não lida.');
    }

    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        try {
            $count = InternalEmail::inbox(Auth::id())->where('lida', false)->count();
        } catch (\Exception $e) {
            $count = 0;
        }
        
        return response()->json([
            'count' => $count
        ]);
    }

    public function destroy(InternalEmail $message): RedirectResponse
    {
        if ($message->remetente_id === Auth::id()) {
            $message->update(['excluded_at_sender' => now()]);
        }
        
        if ($message->destinatario_id === Auth::id()) {
            $message->update(['excluded_at_receiver' => now()]);
        }

        return redirect()->route('internal-email.inbox')->with('success', 'Mensagem movida para a lixeira.');
    }

    public function restore(InternalEmail $message): RedirectResponse
    {
        if ($message->remetente_id === Auth::id()) {
            $message->update(['excluded_at_sender' => null]);
        }
        
        if ($message->destinatario_id === Auth::id()) {
            $message->update(['excluded_at_receiver' => null]);
        }

        return redirect()->back()->with('success', 'Mensagem restaurada.');
    }

    public function permanentDelete(InternalEmail $message): RedirectResponse
    {
        // Actually delete if both excluded or only one side exists
        // But for simplicity, we just delete if the user requesting is authorized
        if ($message->remetente_id === Auth::id() || $message->destinatario_id === Auth::id()) {
            // Delete attachments from storage
            foreach ($message->anexos as $anexo) {
                Storage::disk('public')->delete($anexo->file_path);
                $anexo->delete();
            }
            $message->delete();
        }

        return redirect()->route('internal-email.trash')->with('success', 'Mensagem excluída permanentemente.');
    }
}
