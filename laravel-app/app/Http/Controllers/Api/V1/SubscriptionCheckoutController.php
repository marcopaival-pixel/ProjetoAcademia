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
            ->map(fn (Plan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'billing_cycle' => $plan->billing_cycle,
                'description' => $plan->description,
            ]);

        return $this->success(['plans' => $plans]);
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

        $this->subscriptionService->upgrade($subscription, $plan);

        return $subscription->fresh();
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
