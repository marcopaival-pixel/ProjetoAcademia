<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\DB;

class SupportTicketController extends Controller
{
    public function index()
    {
        $status = request('status');
        $priority = request('priority');

        $query = SupportTicket::with('user')->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }

        $tickets = $query->paginate(20);

        $stats = [
            'open' => SupportTicket::where('status', 'Open')->count(),
            'in_progress' => SupportTicket::where('status', 'In Progress')->count(),
            'resolved' => SupportTicket::where('status', 'Resolved')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'messages.user']);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate(['message' => 'required|string']);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => true,
        ]);

        $ticket->update(['status' => 'In Progress']);

        return back()->with('success', 'Resposta do suporte enviada com sucesso!');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:Open,In Progress,Resolved,Closed']);
        
        $ticket->update(['status' => $request->status]);

        return back()->with('success', "Status do ticket atualizado para {$request->status}!");
    }
}
