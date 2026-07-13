<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\FormatsApiResponses;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\Payment\PaymentGatewayManager;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionCheckoutController extends Controller
{
    use FormatsApiResponses;

    public function __construct(
        private PaymentGatewayManager $paymentManager,
        private SubscriptionService $subscriptionService
    ) {}

    public function plans(Request $request): JsonResponse
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->where('type', 'student')
            ->orderBy('price')
            ->get()
            ->map(function (Plan $plan) {
                return [
                    'id' => $plan->getAttribute('id'),
                    'name' => $plan->getAttribute('name'),
                    'price' => (float) $plan->getAttribute('price'),
                    'billing_cycle' => $plan->getAttribute('billing_cycle'),
                    'description' => $plan->getAttribute('description'),
                ];
            });

        return $this->success(['plans' => $plans]);
    }

    public function current(Request $request): JsonResponse
    {
        $subscription = Subscription::query()
            ->with(['plan:id,name,price,billing_cycle,description', 'pendingPlan:id,name,price,billing_cycle'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        return $this->success([
            'subscription' => $subscription ? $this->formatSubscription($subscription) : null,
            'is_premium' => $request->user()->hasPremiumAccess(),
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $subscription = Subscription::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (! $subscription) {
            return $this->error('Nenhuma assinatura ativa encontrada para cancelamento.', 404, 'subscription_not_found');
        }

        $this->subscriptionService->cancel($subscription);
        $subscription->refresh()->load(['plan:id,name,price,billing_cycle,description', 'pendingPlan:id,name,price,billing_cycle']);

        return $this->success([
            'message' => 'Cancelamento agendado com sucesso.',
            'subscription' => $this->formatSubscription($subscription),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'payment_method' => ['nullable', 'in:credit_card,pix,boleto,card'],
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($validated['plan_id']);
        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        if ($plan->type !== 'student') {
            return $this->error('Plano inválido para aluno.', 422, 'invalid_plan');
        }

        try {
            return DB::transaction(function () use ($user, $plan, $pagamentoAtivo, $validated) {
                if (! $pagamentoAtivo || $plan->price <= 0) {
                    $subscription = $this->activateFreePlan($user, $plan);

                    return $this->success([
                        'status' => 'activated',
                        'subscription_id' => $subscription->id,
                        'plan' => $plan->name,
                    ]);
                }

                $gateway = $this->paymentManager->driver();
                $checkout = $gateway->createSubscription($user, $plan, []);

                if (! ($checkout['ok'] ?? false)) {
                    return $this->error($checkout['error'] ?? 'Erro no gateway de pagamento.', 502, 'gateway_error');
                }

                $subscription = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => Subscription::STATUS_FIN_PENDENTE,
                        'payment_method' => $validated['payment_method'] ?? 'gateway',
                        'start_date' => now(),
                        'gateway_type' => $gateway->getIdentifier(),
                    ]
                );

                return $this->success([
                    'status' => 'pending_payment',
                    'subscription_id' => $subscription->id,
                    'checkout_url' => $checkout['init_point'] ?? null,
                    'gateway' => $gateway->getIdentifier(),
                    'app_return_links' => $this->appReturnLinks(),
                ], status: 202);
            });
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500, 'checkout_failed');
        }
    }

    private function activateFreePlan($user, Plan $plan): Subscription
    {
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'payment_method' => 'free',
                'start_date' => now(),
            ]
        );

        /** @var Subscription $subscription */
        $this->subscriptionService->upgrade($subscription, $plan);

        return $subscription->fresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function formatSubscription(Subscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'status' => $subscription->canonicalStatus(),
            'financial_status' => $subscription->getFinancialStatus(),
            'payment_method' => $subscription->payment_method,
            'gateway_type' => $subscription->gateway_type,
            'start_date' => $subscription->start_date?->toDateString(),
            'end_date' => $subscription->end_date?->toDateString(),
            'next_billing_date' => $subscription->next_billing_date?->toDateString(),
            'cancelled_at' => $subscription->cancelled_at?->toIso8601String(),
            'days_overdue' => $subscription->days_overdue,
            'retry_count' => $subscription->retry_count,
            'plan' => $subscription->plan ? [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'price' => (float) $subscription->plan->price,
                'billing_cycle' => $subscription->plan->billing_cycle,
                'description' => $subscription->plan->description,
            ] : null,
            'pending_plan' => $subscription->pendingPlan ? [
                'id' => $subscription->pendingPlan->id,
                'name' => $subscription->pendingPlan->name,
                'price' => (float) $subscription->pendingPlan->price,
                'billing_cycle' => $subscription->pendingPlan->billing_cycle,
            ] : null,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function appReturnLinks(): array
    {
        return [
            'success' => 'nexshape://subscription/success',
            'pending' => 'nexshape://subscription/pending',
            'cancelled' => 'nexshape://subscription/cancelled',
            'web_success' => url('/app/subscription/return/success'),
            'web_pending' => url('/app/subscription/return/pending'),
        ];
    }
}
