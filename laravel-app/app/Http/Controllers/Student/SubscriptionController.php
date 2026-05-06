<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(\App\Services\SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        $user = auth()->user();
        $subscription = $this->getOrCreateSubscription($user);

        // Se o usuário não tem plano ativo e não é premium, redireciona para escolha de planos
        if ($subscription->status !== 'active' && !$user->isPremiumActive()) {
            return redirect()->route('patient.subscription.plans');
        }

        $allPlans = \App\Models\Plan::where('is_active', true)->where('type', 'student')->get();

        return view('student.subscription.index', compact('subscription', 'allPlans'));
    }

    public function plans()
    {
        $allPlans = \App\Models\Plan::where('is_active', true)
            ->where('type', 'student')
            ->orderBy('price', 'asc')
            ->get();

        return view('student.subscription.pricing', compact('allPlans'));
    }

    public function checkout(\App\Models\Plan $plan)
    {
        return view('student.subscription.checkout', compact('plan'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
        ]);

        $user = auth()->user();
        $plan = \App\Models\Plan::findOrFail($request->plan_id);
        $subscription = $this->getOrCreateSubscription($user);

        // Simulação de processamento bem-sucedido
        // Em produção, aqui integraria com Stripe ou Mercado Pago
        $cardLastFour = substr(str_replace(' ', '', $request->card_number), -4);
        
        $this->subscriptionService->updatePaymentMethod($subscription, [
            'method' => 'card',
            'card_brand' => 'Mastercard', // Detectado no front ou vindo da API do gateway
            'card_last_four' => $cardLastFour,
            'card_expiry' => $request->card_expiry,
        ]);

        $this->subscriptionService->upgrade($subscription, $plan);

        return redirect()->route('patient.subscription.index')
            ->with('success', 'Assinatura realizada com sucesso! Bem-vindo ao NexShape Premium.');
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

        // Simulação de dados do cartão vindo do Gateway
        $cardData = [];
        if ($validated['method'] === 'card') {
            $cardLastFour = $request->card_number ? substr(str_replace(' ', '', $request->card_number), -4) : '8888';
            $cardData = [
                'card_brand' => 'Mastercard',
                'card_last_four' => $cardLastFour,
                'card_expiry' => $request->card_expiry ?? '05/2029',
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
        $newPlan = \App\Models\Plan::findOrFail($request->plan_id);

        if ($newPlan->price > ($subscription->plan->price ?? 0)) {
            $this->subscriptionService->upgrade($subscription, $newPlan);
            return back()->with('success', 'Upgrade realizado com sucesso!');
        } else {
            $this->subscriptionService->downgrade($subscription, $newPlan);
            return back()->with('success', 'Downgrade agendado para o próximo ciclo.');
        }
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

    /**
     * Busca a assinatura atual ou cria uma básica se o usuário for Aluno mas não tiver registro financeiro.
     */
    private function getOrCreateSubscription($user)
    {
        $subscription = Subscription::with(['plan', 'logs'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$subscription) {
            $plan = $user->plan ?? \App\Models\Plan::where('name', 'Free')->first();
            
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
