<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct(
        protected \App\Services\SubscriptionService $subscriptionService,
        protected \App\Services\Payment\PaymentGatewayManager $paymentManager
    ) {}

    public function index()
    {
        $user = auth()->user();
        $subscription = $this->getOrCreateSubscription($user);

        $allPlans = Plan::where('is_active', true)->where('type', 'student')->get();

        return view('student.subscription.index', compact('subscription', 'allPlans'));
    }

    public function plans()
    {
        $allPlans = Plan::where('is_active', true)
            ->where('type', 'student')
            ->orderBy('price', 'asc')
            ->get();

        return view('student.subscription.pricing', compact('allPlans'));
    }

    public function checkout(Plan $plan)
    {
        return view('student.subscription.checkout', compact('plan'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'nullable|in:credit_card,pix,boleto,card',
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        if ($plan->type !== 'student') {
            return back()->with('error', 'Plano inválido.');
        }

        try {
            return DB::transaction(function () use ($user, $plan, $pagamentoAtivo, $request) {
                if (! $pagamentoAtivo || $plan->price <= 0) {
                    $subscription = $this->getOrCreateSubscription($user);
                    $this->subscriptionService->upgrade($subscription, $plan);

                    return redirect()->route('patient.subscription.index')
                        ->with('success', 'Plano ativado com sucesso!');
                }

                $gateway = $this->paymentManager->driver();
                $checkout = $gateway->createSubscription($user, $plan, []);

                if (! ($checkout['ok'] ?? false)) {
                    return back()->with('error', $checkout['error'] ?? 'Erro no gateway de pagamento.');
                }

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

                $initPoint = $checkout['init_point'] ?? null;
                if ($initPoint) {
                    return redirect()->away($initPoint);
                }

                return redirect()->route('patient.subscription.index')
                    ->with('success', 'Assinatura iniciada. Aguarde a confirmação do pagamento.');
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Falha ao processar pagamento: '.$e->getMessage());
        }
    }

    public function updatePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'method' => 'required|in:card,pix,boleto',
            'card_token' => 'nullable',
            'card_number' => 'nullable|string',
            'card_expiry' => 'nullable|string',
            'card_cvv' => 'nullable|string',
        ]);

        $user = auth()->user();
        $subscription = $this->getOrCreateSubscription($user);

        $cardData = [];
        if ($validated['method'] === 'card' && $request->card_number) {
            $cardData = [
                'card_brand' => 'card',
                'card_last_four' => substr(str_replace(' ', '', $request->card_number), -4),
                'card_expiry' => $request->card_expiry,
            ];
        }

        $this->subscriptionService->updatePaymentMethod($subscription, array_merge($validated, $cardData));

        return back()->with('success', 'Forma de pagamento atualizada com sucesso!');
    }

    public function changePlan(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $user = auth()->user();
        $subscription = $this->getOrCreateSubscription($user);
        $newPlan = Plan::findOrFail($request->plan_id);
        $pagamentoAtivo = AdminSetting::isTrue('pagamento_ativo', true);

        if ($newPlan->price > ($subscription->plan->price ?? 0)) {
            if (!$pagamentoAtivo || $newPlan->price <= 0) {
                $this->subscriptionService->upgrade($subscription, $newPlan);
                return back()->with('success', 'Upgrade realizado com sucesso (Simulação Dev)!');
            }

            try {
                return DB::transaction(function () use ($user, $newPlan, $subscription) {
                    $gateway = $this->paymentManager->driver();
                    $checkout = $gateway->createSubscription($user, $newPlan, [
                        'external_reference' => "pa:{$user->id}:{$newPlan->name}"
                    ]);

                    if (! ($checkout['ok'] ?? false)) {
                        return back()->with('error', $checkout['error'] ?? 'Erro no gateway de pagamento ao realizar upgrade.');
                    }

                    $subscription->update([
                        'plan_id' => $newPlan->id,
                        'status' => Subscription::STATUS_FIN_PENDENTE,
                        'payment_method' => 'gateway',
                        'gateway_type' => $gateway->getIdentifier(),
                        'gateway_id' => $checkout['id'] ?? null,
                    ]);

                    $initPoint = $checkout['init_point'] ?? null;
                    if ($initPoint) {
                        return redirect()->away($initPoint);
                    }

                    return redirect()->route('patient.subscription.index')
                        ->with('success', 'Upgrade iniciado. Aguarde confirmação do pagamento no gateway.');
                });
            } catch (\Throwable $e) {
                return back()->with('error', 'Falha ao processar upgrade: '.$e->getMessage());
            }
        }

        $this->subscriptionService->downgrade($subscription, $newPlan);

        return back()->with('success', 'Downgrade agendado para o próximo ciclo.');
    }

    public function cancel()
    {
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)->latest()->first();

        if ($subscription) {
            $this->subscriptionService->cancel($subscription);

            return back()->with('success', 'Cancelamento agendado com sucesso.');
        }

        return back()->with('error', 'Nenhuma assinatura ativa encontrada para cancelamento.');
    }

    private function getOrCreateSubscription($user)
    {
        $subscription = Subscription::with(['plan', 'logs'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (! $subscription) {
            $plan = $user->plan ?? Plan::where('name', 'Free')->first();

            $status = ($user->isPremiumActive() && $plan && $plan->name !== 'FREE') ? 'active' : 'inactive';

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id ?? null,
                'status' => $status,
                'payment_method' => 'N/A',
                'start_date' => $user->created_at,
            ]);

            if ($plan) {
                $subscription->setRelation('plan', $plan);
            }
        }

        return $subscription;
    }
}
