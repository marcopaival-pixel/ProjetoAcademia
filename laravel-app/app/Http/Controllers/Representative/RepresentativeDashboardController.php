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

        // Resumo Financeiro
        $commissions = Commission::where('representative_id', $user->id)->get();
        
        $totalEarned = $commissions->where('status', '!=', Commission::STATUS_CANCELADO)->sum('commission_amount');
        $pendingAmount = $commissions->where('status', Commission::STATUS_PENDENTE)->sum('commission_amount');
        $availableAmount = $commissions->where('status', Commission::STATUS_DISPONIVEL)->sum('commission_amount');
        $paidAmount = $commissions->where('status', Commission::STATUS_PAGO)->sum('commission_amount');

        // Indicações (últimos 10)
        $referrals = User::where('representative_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Comissões (últimas 10)
        $latestCommissions = Commission::with(['user', 'payment'])
            ->where('representative_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Link de indicação
        $refCode = $user->username ?: $user->professional_code ?: $user->id;
        $referralLink = route('home', ['ref' => $refCode]);

        return view('representative.dashboard', compact(
            'totalEarned',
            'pendingAmount',
            'availableAmount',
            'paidAmount',
            'referrals',
            'latestCommissions',
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
