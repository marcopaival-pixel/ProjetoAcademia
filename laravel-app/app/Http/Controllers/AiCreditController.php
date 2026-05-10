<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\AiCreditPackage;
use App\Services\AiCreditService;
use Illuminate\Http\Request;

class AiCreditController extends Controller
{
    protected $aiCreditService;
    protected $gateway;

    public function __construct(AiCreditService $aiCreditService, PaymentGatewayInterface $gateway)
    {
        $this->aiCreditService = $aiCreditService;
        $this->gateway = $gateway;
    }

    /**
     * List available credit packages.
     */
    public function packages()
    {
        $packages = AiCreditPackage::where('is_active', true)->orderBy('credits')->get();
        return response()->json($packages);
    }

    /**
     * Process a credit purchase.
     */
    public function buy(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:ai_credits_packages,id',
        ]);

        $package = AiCreditPackage::findOrFail($request->package_id);
        $user = auth()->user();

        $checkout = $this->gateway->createCheckout($user, (float) $package->price, [
            'title' => "Créditos IA — {$package->name}",
            'description' => "Compra de {$package->credits} créditos de IA.",
            'external_reference' => "ai_credits:{$package->id}",
        ]);

        if (!$checkout['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar checkout: ' . $checkout['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'init_point' => $checkout['init_point'],
        ]);
    }

    /**
     * Dashboard of AI usage (Improvement 13).
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        $usageToday = $user->aiUsage()
            ->whereDate('created_at', now()->today())
            ->sum('credits_consumed');
            
        $usageMonth = $user->aiUsage()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('credits_consumed');
            
        $totalUsage = $user->aiUsage()->sum('credits_consumed');
        
        $history = $user->aiUsage()
            ->latest()
            ->paginate(15);

        return view('ai-credits.dashboard', compact('user', 'usageToday', 'usageMonth', 'totalUsage', 'history'));
    }

    /**
     * Distribute credits (Improvement 6).
     */
    public function distribute(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
        ]);

        $clinic = auth()->user();
        $target = \App\Models\User::find($request->target_user_id);

        $success = $this->aiCreditService->distribute($clinic, $target, $request->amount);

        if (!$success) {
            return back()->with('error', 'Saldo insuficiente para distribuição.');
        }

        return back()->with('success', "{$request->amount} créditos distribuídos com sucesso para {$target->name}.");
    }
}
