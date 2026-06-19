<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function index()
    {
        $proposals = \App\Models\CommercialProposal::where('representative_id', auth()->id())->with('lead', 'plan')->latest()->paginate(20);
        return view('representative.proposals.index', compact('proposals'));
    }

    public function create()
    {
        $leads = \App\Models\Lead::where('responsavel_id', auth()->id())->get();
        $plans = \App\Models\Plan::all();
        return view('representative.proposals.form', compact('leads', 'plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'plan_id' => 'required|exists:plans,id',
            'valor' => 'required|numeric',
            'desconto' => 'nullable|numeric',
            'validade' => 'required|date',
            'status' => 'required|string',
            'observacoes' => 'nullable|string',
            'clinic_name' => 'required|string|max:255',
            'clinic_cnpj' => 'required|string|max:18',
            'clinic_city' => 'required|string|max:100',
            'clinic_state' => 'required|string|size:2',
            'clinic_phone' => 'required|string|max:20',
            'clinic_contact' => 'required|string|max:100',
        ]);

        $this->ensureLeadBelongsToRepresentative((int) $data['lead_id']);

        $data['representative_id'] = auth()->id();
        $data['token'] = \Illuminate\Support\Str::uuid()->toString();
        // Garantir status inicial
        $data['status'] = 'Ativa';

        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $proposal = \App\Models\CommercialProposal::create($data);

            $representative = auth()->user();
            $codeStr = \App\Models\ReferralCode::generateUniqueCode($representative);

            \App\Models\ReferralCode::create([
                'code' => $codeStr,
                'representative_id' => $representative->id,
                'commercial_proposal_id' => $proposal->id,
                'status' => \App\Models\ReferralCode::STATUS_DISPONIVEL,
                'expires_at' => $proposal->validade
            ]);
            
            \App\Models\RepresentativeAudit::create([
                'user_id' => $representative->id,
                'action' => 'Criou Código de Indicação',
                'details' => 'Código: ' . $codeStr . ' / Proposta ID: ' . $proposal->id
            ]);
        });

        return redirect()->route('representative.proposals.index')->with('success', 'Proposta criada com sucesso!');
    }

    public function show(\App\Models\CommercialProposal $proposal)
    {
        $this->authorize('view', $proposal);
        return view('representative.proposals.show', compact('proposal'));
    }

    public function edit(\App\Models\CommercialProposal $proposal)
    {
        $this->authorize('update', $proposal);
        $leads = \App\Models\Lead::where('responsavel_id', auth()->id())->get();
        $plans = \App\Models\Plan::all();
        return view('representative.proposals.form', compact('proposal', 'leads', 'plans'));
    }

    public function update(Request $request, \App\Models\CommercialProposal $proposal)
    {
        $this->authorize('update', $proposal);

        $data = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'plan_id' => 'required|exists:plans,id',
            'valor' => 'required|numeric',
            'desconto' => 'nullable|numeric',
            'validade' => 'required|date',
            'status' => 'required|string',
            'observacoes' => 'nullable|string',
            'clinic_name' => 'required|string|max:255',
            'clinic_cnpj' => 'required|string|max:18',
            'clinic_city' => 'required|string|max:100',
            'clinic_state' => 'required|string|size:2',
            'clinic_phone' => 'required|string|max:20',
            'clinic_contact' => 'required|string|max:100',
        ]);

        $this->ensureLeadBelongsToRepresentative((int) $data['lead_id']);

        $proposal->update($data);

        return redirect()->route('representative.proposals.index')->with('success', 'Proposta atualizada com sucesso!');
    }

    public function generatePdf(\App\Models\CommercialProposal $proposal)
    {
        $this->authorize('view', $proposal);

        // Auditoria: logar a geração do PDF da proposta
        \App\Models\RepresentativeAudit::create([
            'user_id' => auth()->id(),
            'action' => 'Gerou PDF da Proposta',
            'details' => 'Proposta ID: ' . $proposal->id . ' / Clínica: ' . $proposal->clinic_name
        ]);

        $data = [
            'proposal' => $proposal,
            'representative' => $proposal->representative,
            'plan' => $proposal->plan,
            'basePrice' => $proposal->valor,
            'discountRate' => $proposal->desconto > 0 ? round(($proposal->desconto / $proposal->valor) * 100, 2) : 0,
            'discountAmount' => $proposal->desconto,
            'finalPrice' => $proposal->valor_final,
            'validityDate' => \Carbon\Carbon::parse($proposal->validade)->format('d/m/Y'),
            'qrCode' => $proposal->generateQrCode()
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('representative.pdf.proposal', $data);
        $fileName = 'Proposta_NexShape_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    private function ensureLeadBelongsToRepresentative(int $leadId): void
    {
        $owned = \App\Models\Lead::where('id', $leadId)
            ->where('responsavel_id', auth()->id())
            ->exists();

        if (! $owned) {
            abort(403, 'Lead não pertence a este representante.');
        }
    }
}
