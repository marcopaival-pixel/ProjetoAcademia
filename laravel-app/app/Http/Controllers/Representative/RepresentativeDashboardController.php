<?php

namespace App\Http\Controllers\Representative;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RepresentativeDashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = auth()->user();

        // Minhas Vendas e Recebimentos (Comissões)
        $commissions = Commission::where('representative_id', $user->id)->get();
        
        $totalEarned = $commissions->where('status', '!=', Commission::STATUS_CANCELADO)->sum('commission_amount');
        $pendingAmount = $commissions->whereIn('status', [Commission::STATUS_PENDENTE, Commission::STATUS_AGUARDANDO_PAGAMENTO, Commission::STATUS_CARENCIA])->sum('commission_amount');
        $availableAmount = $commissions->where('status', Commission::STATUS_DISPONIVEL)->sum('commission_amount');
        $paidAmount = $commissions->where('status', Commission::STATUS_PAGO)->sum('commission_amount');

        // Métricas do Dashboard Comercial
        $clinicsCount = \App\Models\Clinic::where('representative_id', $user->id)->count();
        $activeClinicsCount = \App\Models\Clinic::where('representative_id', $user->id)->where('sale_status', 'ativa')->count();
        $defaultingClinicsCount = \App\Models\Clinic::where('representative_id', $user->id)->where('sale_status', 'inadimplente')->count();
        
        $salesThisMonth = \App\Models\Clinic::where('representative_id', $user->id)
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->count();

        // Próximas liberações (Valor previsto para próximo pagamento)
        $nextPaymentValue = Commission::where('representative_id', $user->id)
            ->where('status', Commission::STATUS_DISPONIVEL)
            ->sum('commission_amount');

        $leadsCount = \App\Models\Lead::where('responsavel_id', $user->id)->count();
        $proposalsCount = \App\Models\CommercialProposal::where('representative_id', $user->id)->count();
        $contractsCount = \App\Models\Contract::where('representative_id', $user->id)->count();
        $activeContractsCount = \App\Models\Contract::where('representative_id', $user->id)->where('status', 'ativo')->count();
        $totalSoldValue = \App\Models\CommercialProposal::where('representative_id', $user->id)->where('status', 'aceita')->sum('valor');

        $salesConversion = $leadsCount > 0 ? round(($contractsCount / $leadsCount) * 100, 2) : 0;
        $monthlyGoal = 10000; // Meta mensal mockada ou configurável no perfil

        // Comissões (últimas 10)
        $latestCommissions = Commission::with(['user', 'payment'])
            ->where('representative_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Próximas liberações
        $upcomingReleases = Commission::where('representative_id', $user->id)
            ->where('status', Commission::STATUS_PENDENTE)
            ->orderBy('created_at', 'asc') // ideal seria uma data de previsão
            ->take(5)
            ->get();

        // Link de indicação
        $refCode = $user->representativeProfile->code ?? $user->username ?? $user->professional_code ?? $user->id;
        $referralLink = route('referral.link', ['code' => $refCode]);

        return view('representative.dashboard', compact(
            'totalEarned',
            'pendingAmount',
            'availableAmount',
            'paidAmount',
            'clinicsCount',
            'activeClinicsCount',
            'defaultingClinicsCount',
            'salesThisMonth',
            'nextPaymentValue',
            'leadsCount',
            'proposalsCount',
            'contractsCount',
            'activeContractsCount',
            'totalSoldValue',
            'salesConversion',
            'monthlyGoal',
            'latestCommissions',
            'upcomingReleases',
            'referralLink'
        ));
    }

    public function commissions(Request $request): View
    {
        $user = auth()->user();
        $commissions = Commission::with(['user', 'payment', 'subscription'])
            ->where('representative_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('representative.commissions', compact('commissions'));
    }

    public function referrals(Request $request): View
    {
        $user = auth()->user();
        $referrals = User::where('representative_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('representative.referrals', compact('referrals'));
    }

    public function myCodes(Request $request): View
    {
        $user = auth()->user();
        $codes = \App\Models\ReferralCode::where('representative_id', $user->id)
            ->with(['commercialProposal', 'clinic'])
            ->latest()
            ->paginate(20);

        return view('representative.my_codes', compact('codes'));
    }

    public function withdrawForm(): View
    {
        /** @var User $user */
        $user = auth()->user();

        // Saldo disponível (Comissões com status DISPONIVEL)
        $availableBalance = Commission::where('representative_id', $user->id)
            ->where('status', Commission::STATUS_DISPONIVEL)
            ->sum('commission_amount');

        // Solicitações pendentes
        $pendingWithdrawals = WithdrawalRequest::where('representative_id', $user->id)
            ->where('status', WithdrawalRequest::STATUS_PENDENTE)
            ->sum('amount');

        $netAvailable = $availableBalance - $pendingWithdrawals;

        return view('representative.withdraw', compact('netAvailable', 'availableBalance', 'pendingWithdrawals'));
    }

    public function withdrawStore(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $request->validate([
            'amount' => 'required|numeric|min:20', // Saque mínimo de R$ 20,00
            'pix_key' => 'required|string|max:255',
        ], [
            'amount.min' => 'O valor mínimo para saque é R$ 20,00',
            'pix_key.required' => 'A chave PIX é obrigatória para o resgate.'
        ]);

        $amount = $request->input('amount');

        // Verificar saldo real
        $availableBalance = Commission::where('representative_id', $user->id)
            ->where('status', Commission::STATUS_DISPONIVEL)
            ->sum('commission_amount');

        $pendingWithdrawals = WithdrawalRequest::where('representative_id', $user->id)
            ->where('status', WithdrawalRequest::STATUS_PENDENTE)
            ->sum('amount');

        $netAvailable = $availableBalance - $pendingWithdrawals;

        if ($amount > $netAvailable) {
            return back()->with('error', 'Saldo insuficiente para esta solicitação.');
        }

        WithdrawalRequest::create([
            'representative_id' => $user->id,
            'amount' => $amount,
            'pix_key' => $request->input('pix_key'),
            'status' => WithdrawalRequest::STATUS_PENDENTE,
        ]);

        return redirect()->route('representative.withdraw.history')->with('success', 'Solicitação de saque enviada com sucesso!');
    }

    public function withdrawHistory(): View
    {
        $user = auth()->user();
        $withdrawals = WithdrawalRequest::where('representative_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('representative.withdraw_history', compact('withdrawals'));
    }
}
