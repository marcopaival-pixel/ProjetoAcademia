<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\CommercialProposal;
use Illuminate\Support\Facades\DB;

class CommercialDashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'total_leads' => Lead::count(),
            'new_leads_month' => Lead::whereMonth('created_at', now()->month)->count(),
            'conversion_rate' => Lead::count() > 0 ? (Lead::where('status', 'Convertido')->count() / Lead::count()) * 100 : 0,
            'pipeline_value' => Lead::whereNotIn('status', ['Convertido', 'Perdido'])->sum('valor_estimado'),
            'closed_value' => CommercialProposal::where('status', 'Aprovada')->sum(DB::raw('valor - desconto')),
        ];

        $funnelData = Lead::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Garante que todos os status estejam presentes no array
        $statuses = ['Novo', 'Em contato', 'Em negociação', 'Convertido', 'Perdido'];
        foreach ($statuses as $status) {
            if (!isset($funnelData[$status])) {
                $funnelData[$status] = 0;
            }
        }

        $latestProposals = CommercialProposal::with(['lead', 'plan'])->latest()->take(5)->get();
        $latestLeads = Lead::latest()->take(5)->get();

        return view('admin.commercial.dashboard', compact('kpis', 'funnelData', 'latestProposals', 'latestLeads'));
    }
}
