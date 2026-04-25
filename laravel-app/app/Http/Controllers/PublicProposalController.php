<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CommercialProposal;

class PublicProposalController extends Controller
{
    public function show($token)
    {
        $proposal = CommercialProposal::where('token', $token)->with(['lead', 'plan'])->firstOrFail();
        
        if ($proposal->validade->isPast() && $proposal->status == 'Pendente') {
            return view('public.proposals.expired', compact('proposal'));
        }

        return view('public.proposals.show', compact('proposal'));
    }

    public function accept($token)
    {
        $proposal = CommercialProposal::where('token', $token)->firstOrFail();
        
        if ($proposal->status == 'Aprovada') {
            return back()->with('info', 'Esta proposta já foi aprovada.');
        }

        $proposal->update(['status' => 'Aprovada']);
        $proposal->lead->update(['status' => 'Convertido']);

        // Cria o Contrato Digital
        \App\Models\Contract::create([
            'lead_id' => $proposal->lead_id,
            'proposal_id' => $proposal->id,
            'status' => 'Assinado',
            'signed_at' => now(),
            'token' => \Illuminate\Support\Str::random(40),
            'content' => "Contrato de prestação de serviços baseado na proposta {$proposal->token}. Plano: {$proposal->plan?->name}."
        ]);

        // Inicializa Onboarding
        $steps = [
            ['title' => 'Configuração da Academia', 'order' => 1],
            ['title' => 'Importação de Alunos', 'order' => 2],
            ['title' => 'Treinamento Operacional', 'order' => 3],
            ['title' => 'Configuração de Pagamentos', 'order' => 4],
            ['title' => 'Lançamento Oficial', 'order' => 5],
        ];

        foreach ($steps as $step) {
            $proposal->lead->onboardingSteps()->create($step);
        }

        return back()->with('success', 'Proposta aceita com sucesso! Nossa equipe entrará em contato para o onboarding.');
    }

    public function reject($token, Request $request)
    {
        $proposal = CommercialProposal::where('token', $token)->firstOrFail();
        $proposal->update([
            'status' => 'Rejeitada',
            'observacoes' => $proposal->observacoes . "\nMotivo Rejeição: " . $request->motivo
        ]);

        return back()->with('error', 'Proposta rejeitada. Lamentamos que não tenha atendido suas expectativas.');
    }
}
