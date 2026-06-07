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
                    $user = new User([
                        'name' => $request->name,
                        'email' => $request->email,
                        'status' => 'active',
                        'onboarding_status' => 'pending',
                    ]);
                    $user->password_hash = Hash::make($request->password);
                    $user->save();
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

                $referralCodeStr = session('referral_code') ?? $request->input('referral_code');
                $referralCode = null;
                $discountAmount = 0.0;
                $representativeId = null;
                $resolvedReferral = null;

                if ($referralCodeStr) {
                    $resolvedReferral = app(\App\Services\ReferralCodeResolver::class)->resolve(
                        $referralCodeStr,
                        (int) $request->plan_id
                    );
                    if ($resolvedReferral !== null) {
                        $discountAmount = $resolvedReferral['discount_amount'];
                        $representativeId = $resolvedReferral['representative_id'];
                        $referralCode = $resolvedReferral['referral_code'];
                        if ($referralCode === null && $resolvedReferral['profile']) {
                            $user->representative_id = $representativeId;
                            $user->save();
                        }
                    }
                }

                $gateway = $this->paymentManager->driver();
                $checkoutOptions = [];
                if ($discountAmount > 0) {
                    $checkoutOptions['referral_discount'] = $discountAmount;
                    if ($referralCodeStr) {
                        $checkoutOptions['referral_code'] = $referralCodeStr;
                    }
                }
                $checkout = $gateway->createSubscription($user, $plan, $checkoutOptions);

                if (!$checkout['ok']) {
                    throw new \Exception($checkout['error'] ?? 'Erro no gateway de pagamento.');
                }

                // Criar assinatura pendente
                $subscription = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => Subscription::STATUS_FIN_PENDENTE,
                        'payment_method' => $request->payment_method ?? 'gateway',
                        'start_date' => now(),
                        'gateway_type' => $gateway->getIdentifier(),
                    ]
                );

                if ($representativeId) {
                    $clinicId = $user->clinic_id ?? null;
                    if ($clinicId) {
                        \App\Models\Clinic::where('id', $clinicId)->update([
                            'representative_code_used' => $referralCode?->code ?? $referralCodeStr,
                            'applied_discount_rate' => $discountAmount,
                            'representative_id' => $representativeId,
                        ]);
                        if ($referralCode) {
                            $referralCode->markAsUsed($clinicId);
                        }
                    }

                    $commissionRate = $this->resolveRepresentativeCommissionRate(
                        $representativeId,
                        $resolvedReferral['profile'] ?? null
                    );

                    app(\App\Services\CommissionService::class)->recordAwaitingPayment(
                        $representativeId,
                        $user->id,
                        $subscription->id,
                        $clinicId,
                        (float) $plan->price,
                        $commissionRate,
                        ($plan->price - $discountAmount) * ($commissionRate / 100),
                        'Checkout com código: '.($referralCodeStr ?? '').' / Plano: '.$plan->name
                    );

                    \App\Models\RepresentativeAudit::create([
                        'user_id' => $user->id,
                        'action' => 'Utilizou Código de Indicação',
                        'entity_type' => 'ReferralCode',
                        'entity_id' => $referralCode?->id,
                        'new_values' => ['code' => $referralCodeStr, 'plan' => $plan->name],
                    ]);
                }

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

            $referralCodeStr = session('referral_code') ?? request()->input('referral_code');
            if ($referralCodeStr) {
                $resolved = app(\App\Services\ReferralCodeResolver::class)->resolve($referralCodeStr, (int) $plan->id);
                if ($resolved !== null) {
                    $representativeId = $resolved['representative_id'];
                    $referralCode = $resolved['referral_code'];
                    $discountAmount = $resolved['discount_amount'];
                    $clinicId = $user->clinic_id ?? null;

                    if ($clinicId && $referralCode) {
                        \App\Models\Clinic::where('id', $clinicId)->update([
                            'representative_code_used' => $referralCode->code,
                            'applied_discount_rate' => $discountAmount,
                            'representative_id' => $representativeId,
                        ]);
                        $referralCode->markAsUsed($clinicId);
                    }

                    $commissionRate = $this->resolveRepresentativeCommissionRate(
                        $representativeId,
                        $resolved['profile'] ?? null
                    );

                    app(\App\Services\CommissionService::class)->recordAwaitingPayment(
                        $representativeId,
                        $user->id,
                        $subscription->id,
                        $clinicId,
                        (float) $plan->price,
                        $commissionRate,
                        ($plan->price - $discountAmount) * ($commissionRate / 100),
                        'Ativação gratuita com código: '.$referralCodeStr.' / Plano: '.$plan->name
                    );

                    \App\Models\RepresentativeAudit::create([
                        'user_id' => $user->id,
                        'action' => 'Utilizou Código de Indicação (Gratuito)',
                        'entity_type' => 'ReferralCode',
                        'entity_id' => $referralCode?->id,
                        'new_values' => ['code' => $referralCodeStr, 'plan' => $plan->name],
                    ]);
                }
            }

            return $subscription;
        });
    }

    /**
     * Taxa de comissão do representante sem depender do escopo global de User.
     */
    private function resolveRepresentativeCommissionRate(
        int $representativeId,
        ?\App\Models\RepresentativeProfile $profileFromResolver = null
    ): float {
        if ($profileFromResolver) {
            return (float) $profileFromResolver->commission_rate;
        }

        $profile = \App\Models\RepresentativeProfile::withoutGlobalScopes()
            ->where('user_id', $representativeId)
            ->first();

        return $profile ? (float) $profile->commission_rate : 0.0;
    }

    /**
     * Página de sucesso.
     */
    public function success()
    {
        return view('checkout-success');
    }
}
