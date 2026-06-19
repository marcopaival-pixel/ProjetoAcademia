<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(): View
    {
        // Isolamento de dados: apenas os leads deste representante
        $leads = Lead::where('responsavel_id', auth()->id())->latest()->paginate(20);
        return view('representative.leads.index', compact('leads'));
    }

    public function create(): View
    {
        return view('representative.leads.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'required|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'status' => 'required|string',
            'valor_estimado' => 'nullable|numeric',
        ]);

        $validated['responsavel_id'] = auth()->id();
        Lead::create($validated);

        return redirect()->route('representative.leads.index')->with('success', 'Lead cadastrado com sucesso!');
    }

    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);
        return view('representative.leads.form', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'required|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'status' => 'required|string',
            'valor_estimado' => 'nullable|numeric',
        ]);

        $lead->update($validated);

        return redirect()->route('representative.leads.index')->with('success', 'Lead atualizado!');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();
        return redirect()->route('representative.leads.index')->with('success', 'Lead excluído!');
    }
}
