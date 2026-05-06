<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
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

                // 2. Criar ou Atualizar Assinatura
                $subscription = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => !$pagamentoAtivo ? Subscription::STATUS_FIN_ATIVO : ($request->payment_method === 'pix' ? Subscription::STATUS_FIN_PENDENTE : Subscription::STATUS_FIN_ATIVO),
                        'payment_method' => $pagamentoAtivo ? $request->payment_method : 'free_activation',
                        'start_date' => now(),
                        'end_date' => now()->addMonth(),
                        'next_billing_date' => now()->addMonth(),
                        'gateway_type' => $pagamentoAtivo ? 'manual' : 'system',
                    ]
                );

                // 3. Registrar Log de Pagamento
                $user->payments()->create([
                    'subscription_id' => $subscription->id,
                    'amount' => $pagamentoAtivo ? $plan->price : 0,
                    'status' => $subscription->status === Subscription::STATUS_FIN_ATIVO ? 'paid' : 'pending',
                    'gateway' => $pagamentoAtivo ? 'manual' : 'internal',
                    'gateway_id' => 'TXN-' . strtoupper(Str::random(10)),
                    'currency' => 'BRL',
                    'payload' => [
                        'payment_method' => $pagamentoAtivo ? $request->payment_method : 'auto_activation',
                        'plan_name' => $plan->name,
                        'global_payment_enabled' => $pagamentoAtivo,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard'),
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
