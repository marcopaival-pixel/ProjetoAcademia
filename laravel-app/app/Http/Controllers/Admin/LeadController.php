<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lead;
use App\Models\User;
use App\Models\OnboardingStep;
use App\Models\Contract;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with('responsavel')->latest()->paginate(15);
        return view('admin.leads.index', compact('leads'));
    }

    public function funnel()
    {
        $leadsByStatus = Lead::all()->groupBy('status');
        $statuses = ['Novo', 'Em contato', 'Em negociação', 'Convertido', 'Perdido'];
        
        return view('admin.leads.funnel', compact('leadsByStatus', 'statuses'));
    }

    public function create()
    {
        $responsaveis = User::where('is_admin', true)->get();
        return view('admin.leads.create', compact('responsaveis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'origem' => 'nullable|string|max:255',
            'responsavel_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Novo,Em contato,Em negociação,Convertido,Perdido',
            'observacao' => 'nullable|string',
            'valor_estimado' => 'nullable|numeric|min:0',
            'previsao_fechamento' => 'nullable|date',
        ]);

        Lead::create($validated);

        return redirect()->route('admin.leads.index')->with('success', 'Lead criado com sucesso!');
    }

    public function show(Lead $lead)
    {
        $lead->load(['responsavel', 'interactions.user', 'proposals.plan', 'onboardingSteps', 'contracts']);
        return view('admin.leads.show', compact('lead'));
    }

    public function toggleOnboardingStep(Lead $lead, OnboardingStep $step)
    {
        $step->update([
            'is_completed' => !$step->is_completed,
            'completed_at' => !$step->is_completed ? now() : null
        ]);

        return back()->with('success', 'Status do onboarding atualizado!');
    }

    public function edit(Lead $lead)
    {
        $responsaveis = User::where('is_admin', true)->get();
        return view('admin.leads.edit', compact('lead', 'responsaveis'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:255',
            'origem' => 'nullable|string|max:255',
            'responsavel_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Novo,Em contato,Em negociação,Convertido,Perdido',
            'observacao' => 'nullable|string',
            'valor_estimado' => 'nullable|numeric|min:0',
            'previsao_fechamento' => 'nullable|date',
        ]);

        $lead->update($validated);

        return redirect()->route('admin.leads.index')->with('success', 'Lead atualizado com sucesso!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead removido!');
    }

    public function generateDemo(Lead $lead)
    {
        if (!$lead->email) {
            return back()->with('error', 'O lead precisa de um e-mail para gerar acesso demo.');
        }

        // Verifica se já existe um usuário com este e-mail
        $user = User::where('email', $lead->email)->first();

        if (!$user) {
            $user = new User([
                'name' => $lead->nome,
                'email' => $lead->email,
                'status' => 'active',
                'is_demo' => true,
                'demo_expires_at' => now()->addDays(7),
            ]);
            $user->password_hash = bcrypt(Str::random(12));
            $user->save();
        } else {
            $user->update([
                'is_demo' => true,
                'demo_expires_at' => now()->addDays(7),
            ]);
        }

        $lead->update(['converted_user_id' => $user->id]);

        return back()->with('success', "Acesso demo gerado para {$lead->email}! Válido por 7 dias.");
    }

    public function storeInteraction(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'tipo_contato' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_contato' => 'required|date',
        ]);

        $lead->interactions()->create([
            'user_id' => auth()->id(),
            'tipo_contato' => $validated['tipo_contato'],
            'descricao' => $validated['descricao'],
            'data_contato' => $validated['data_contato'],
        ]);

        return back()->with('success', 'Interação registrada!');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:Novo,Em contato,Em negociação,Convertido,Perdido',
        ]);

        $lead->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }
}
