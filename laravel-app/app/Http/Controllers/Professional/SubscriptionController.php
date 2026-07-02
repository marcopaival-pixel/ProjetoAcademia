<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Exibe a gestão de faturamento e planos do profissional (SaaS Billing).
     */
    public function index(): View
    {
        $user = auth()->user();
        $sub = Subscription::where('user_id', $user->id)->with('plan')->latest()->first();

        if (!$sub) {
            $plan = Plan::find($user->professional_plan_id ?? $user->plan_id) ?? Plan::where('type', 'professional')->first();
            $sub = new Subscription([
                'user_id' => $user->id,
                'plan_id' => $plan?->id,
                'status' => 'active',
                'start_date' => $user->created_at,
                'payment_method' => 'Visa',
            ]);
            if ($plan) {
                $sub->setRelation('plan', $plan);
            }
        }

        $subscription = [
            'plan_name' => $sub->plan?->name ?? 'Profissional Starter',
            'status' => $sub->status ?? 'active',
            'next_billing' => $sub->end_date ? $sub->end_date->format('Y-m-d') : now()->addMonth()->format('Y-m-d'),
            'amount' => 'R$ ' . number_format($sub->plan?->price ?? 29.90, 2, ',', '.') . '/mês',
            'card_last4' => '4242',
            'card_brand' => $sub->payment_method ?? 'Visa',
        ];

        $payments = Payment::where('user_id', $user->id)->latest()->take(5)->get();
        $invoices = $payments->map(function ($payment) {
            return [
                'id' => '#' . ($payment->gateway_id ?? $payment->id),
                'date' => $payment->created_at->format('Y-m-d'),
                'amount' => 'R$ ' . number_format($payment->amount, 2, ',', '.'),
                'status' => $payment->status === 'paid' ? 'Paga' : 'Pendente',
            ];
        })->toArray();

        if (empty($invoices)) {
            $invoices = [
                ['id' => '#INV-DEFAULT', 'date' => now()->format('Y-m-d'), 'amount' => $subscription['amount'], 'status' => 'Paga'],
            ];
        }

        $dbPlans = Plan::where('is_active', true)
            ->where('type', 'professional')
            ->orderBy('price', 'asc')
            ->get();

        $plans = $dbPlans->map(function ($p) use ($sub) {
            $feats = $p->planFeatures()->where('is_enabled', true)->pluck('feature_key')->toArray();
            if (empty($feats)) {
                $feats = ['Recursos do Plano'];
            }
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => number_format($p->price, 2, ',', '.'),
                'features' => $feats,
                'current' => $sub->plan_id == $p->id,
            ];
        })->toArray();

        return view('professional.billing.index', compact('subscription', 'invoices', 'plans'));
    }

    /**
     * Redireciona o profissional para o gateway de pagamento para efetuar upgrade.
     */
    public function upgrade(Request $request)
    {
        $planId = $request->query('plan_id');
        $plan = Plan::findOrFail($planId);
        $user = auth()->user();

        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        if (!$pagamentoAtivo || $plan->price <= 0) {
            $sub = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ]
            );
            $user->update([
                'professional_plan_id' => $plan->id,
                'is_premium' => true
            ]);
            return redirect()->route('professional.billing.index')->with('success', 'Plano atualizado com sucesso (Simulação Dev)!');
        }

        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->driver();
        
        $checkout = $gateway->createSubscription($user, $plan, [
            'external_reference' => "pa:{$user->id}:{$plan->name}"
        ]);

        if (!($checkout['ok'] ?? false)) {
            return back()->with('error', $checkout['error'] ?? 'Erro ao iniciar transação no gateway.');
        }

        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_FIN_PENDENTE,
                'payment_method' => 'gateway',
                'start_date' => now(),
                'gateway_type' => $gateway->getIdentifier(),
                'gateway_id' => $checkout['id'] ?? null,
            ]
        );

        $initPoint = $checkout['init_point'] ?? null;
        if ($initPoint) {
            return redirect()->away($initPoint);
        }

        return redirect()->route('professional.billing.index')->with('success', 'Assinatura iniciada. Aguarde confirmação do pagamento.');
    }
}
