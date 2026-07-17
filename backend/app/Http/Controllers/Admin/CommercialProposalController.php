<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CommercialProposal;
use App\Models\Lead;
use App\Models\Plan;
use Illuminate\Support\Str;

class CommercialProposalController extends Controller
{
    public function index()
    {
        $proposals = CommercialProposal::with(['lead', 'plan'])->latest()->paginate(15);
        return view('admin.proposals.index', compact('proposals'));
    }

    public function create(Request $request)
    {
        $leads = Lead::where('status', '!=', 'Perdido')->get();
        $plans = Plan::all();
        $selectedLeadId = $request->query('lead_id');
        
        return view('admin.proposals.create', compact('leads', 'plans', 'selectedLeadId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'plan_id' => 'required|exists:plans,id',
            'valor' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'validade' => 'required|date|after:today',
            'observacoes' => 'nullable|string',
        ]);

        $validated['status'] = 'Pendente';
        $validated['token'] = Str::random(40);

        CommercialProposal::create($validated);

        return redirect()->route('admin.proposals.index')->with('success', 'Proposta gerada com sucesso!');
    }

    public function show(CommercialProposal $proposal)
    {
        $proposal->load(['lead', 'plan']);
        return view('admin.proposals.show', compact('proposal'));
    }

    public function edit(CommercialProposal $proposal)
    {
        $plans = Plan::all();
        return view('admin.proposals.edit', compact('proposal', 'plans'));
    }

    public function update(Request $request, CommercialProposal $proposal)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'valor' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'validade' => 'required|date|after:today',
            'status' => 'required|in:Pendente,Enviada,Aprovada,Rejeitada',
            'observacoes' => 'nullable|string',
        ]);

        $proposal->update($validated);

        return redirect()->route('admin.proposals.index')->with('success', 'Proposta atualizada!');
    }

    public function destroy(CommercialProposal $proposal)
    {
        $proposal->delete();
        return redirect()->route('admin.proposals.index')->with('success', 'Proposta removida!');
    }

    public function send(CommercialProposal $proposal)
    {
        $proposal->update(['status' => 'Enviada']);
        
        // Aqui enviaria o e-mail (Simulado para este MVP)
        // Mail::to($proposal->lead->email)->send(new CommercialProposalMail($proposal));

        return back()->with('success', 'Proposta marcada como enviada e link gerado!');
    }
}
