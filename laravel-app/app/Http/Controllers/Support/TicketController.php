<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest()->paginate(10);
        return view('support.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'Open',
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => false,
        ]);

        return redirect()->route('support.tickets.show', $ticket)->with('success', 'Ticket aberto com sucesso! Aguarde o retorno de nossa equipe.');
    }

    public function show(SupportTicket $ticket)
    {
        // Garante que o usuário só veja seus próprios tickets (abort_if admin? No, admin can see)
        if ($ticket->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $ticket->load('messages.user');
        return view('support.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        if ($ticket->status === 'Closed') {
            return back()->with('error', 'Este ticket está fechado e não aceita mais respostas.');
        }

        $request->validate(['message' => 'required|string']);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => auth()->user()->is_admin,
        ]);

        // Se o cliente respondeu, volta para 'Open' ou mantém 'In Progress'
        if (!auth()->user()->is_admin) {
            $ticket->update(['status' => 'Open']);
        }

        return back()->with('success', 'Resposta enviada!');
    }
}
