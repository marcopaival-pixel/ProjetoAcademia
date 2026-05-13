<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\AiCreditPackage;
use App\Models\AiCreditWallet;
use App\Models\AiCreditTransaction;
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
     * View packages and balance.
     */
    public function index()
    {
        $user = auth()->user();
        $wallet = $this->aiCreditService->getWallet($user);
        $packages = AiCreditPackage::where('is_active', true)->orderBy('credits')->get();
        
        $history = $user->aiTransactions()
            ->latest()
            ->paginate(10);

        return view('ai-credits.index', compact('user', 'wallet', 'packages', 'history'));
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

        // Se pagamento_ativo for falso, libera automático (modo teste)
        $pagamentoAtivo = \App\Models\SystemSetting::where('key', 'pagamento_ativo')->first()?->value === 'true';
        if (!$pagamentoAtivo) {
            $this->aiCreditService->addCredits($user, $package->credits, 'purchase', "Compra de créditos (Modo Teste): {$package->name}");
            
            return response()->json([
                'success' => true,
                'message' => 'Créditos ativados com sucesso (Modo Teste)',
                'reload' => true
            ]);
        }

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
     * Histórico detalhado e Dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $wallet = $this->aiCreditService->getWallet($user);
        
        $usageToday = $user->getAiCreditsUsedToday();
        $usageMonth = $user->getAiCreditsUsedThisMonth();
        $totalUsage = $user->getAiCreditsUsedTotal();
        
        $history = $user->aiTransactions()
            ->latest()
            ->paginate(15);

        $featureCosts = \App\Models\AiFeatureCost::where('is_active', true)->get();

        return view('ai-credits.dashboard', compact(
            'user', 
            'wallet',
            'usageToday', 
            'usageMonth', 
            'totalUsage', 
            'history',
            'featureCosts'
        ));
    }

    /**
     * Get available packages as JSON for the purchase modal.
     */
    public function packages()
    {
        $packages = AiCreditPackage::where('is_active', true)
            ->orderBy('credits')
            ->get();

        return response()->json($packages);
    }
}
