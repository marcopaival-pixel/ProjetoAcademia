<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\AdminSetting;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $paymentManager;

    public function __construct(PaymentGatewayManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }
    /**
     * Exibe o fluxo de checkout.
     */
    public function index($planId)
    {
        $plan = Plan::where('id', $planId)
            ->where('is_active', true)
            ->with('planFeatures')
            ->firstOrFail();

        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        // Se o faturamento global estiver desativado e o usuário já estiver logado, ativar imediatamente
        if (!$pagamentoAtivo && Auth::check()) {
            $this->autoActivate(Auth::user(), $plan);
            return redirect()->route('dashboard')->with('success', 'Assinatura ativada com sucesso!');
        }

        return view('checkout', [
            'plan' => $plan,
            'user' => Auth::user(),
            'pagamentoAtivo' => $pagamentoAtivo,
        ]);
    }

    /**
     * Processa a finalização do checkout.
     */
    public function process(Request $request)
    {
        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        $rules = [
            'plan_id' => 'required|exists:plans,id',
            // Se não estiver logado, validar campos de usuário
            'name' => Auth::check() ? 'nullable' : 'required|string|max:255',
            'email' => Auth::check() ? 'nullable' : 'required|email|unique:users,email',
            'password' => Auth::check() ? 'nullable' : 'required|min:8',
        ];

        if ($pagamentoAtivo) {
            $rules['payment_method'] = 'required|in:credit_card,pix,boleto';
        }

        $request->validate($rules);

        try {
            return DB::transaction(function () use ($request, $pagamentoAtivo) {
                $user = Auth::user();

                // 1. Criar usuário se não existir
                if (!$user) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password_hash' => Hash::make($request->password),
                        'status' => 'active',
                        'onboarding_status' => 'pending',
                    ]);
                    Auth::login($user);
                }

                $plan = Plan::findOrFail($request->plan_id);

                // 2. Se for plano gratuito ou pagamentos desligados, ativação direta
                if (!$pagamentoAtivo || $plan->price <= 0) {
                    $subscription = $this->autoActivate($user, $plan);
                    return response()->json([
                        'success' => true,
                        'redirect' => route('dashboard'),
                        'message' => 'Plano ativado com sucesso!'
                    ]);
                }

                // 3. Se for plano pago, criar preferência no Gateway
                $gateway = $this->paymentManager->driver();
                $checkout = $gateway->createSubscription($user, $plan);

                if (!$checkout['ok']) {
                    throw new \Exception($checkout['error'] ?? 'Erro no gateway de pagamento.');
                }

                // Criar assinatura pendente
                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => Subscription::STATUS_FIN_PENDENTE,
                        'payment_method' => $request->payment_method ?? 'gateway',
                        'start_date' => now(),
                        'gateway_type' => $gateway->getIdentifier(),
                    ]
                );

                return response()->json([
                    'success' => true,
                    'redirect' => $checkout['init_point'],
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar ativação: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ativação automática de plano (quando cobrança está desligada).
     */
    private function autoActivate($user, $plan)
    {
        return DB::transaction(function () use ($user, $plan) {
            $subscription = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'status' => Subscription::STATUS_FIN_ATIVO,
                    'payment_method' => 'free_activation',
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'next_billing_date' => now()->addMonth(),
                    'gateway_type' => 'system',
                ]
            );

            $user->payments()->create([
                'subscription_id' => $subscription->id,
                'amount' => 0,
                'status' => 'paid',
                'gateway' => 'internal',
                'gateway_id' => 'AUTO-' . strtoupper(Str::random(10)),
                'currency' => 'BRL',
                'payload' => ['plan_name' => $plan->name, 'action' => 'direct_activation'],
            ]);

            return $subscription;
        });
    }

    /**
     * Página de sucesso.
     */
    public function success()
    {
        return view('checkout-success');
    }
}
