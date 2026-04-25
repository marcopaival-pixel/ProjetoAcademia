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
        
        $subscription = Subscription::with(['plan', 'logs'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$subscription) {
            $plan = $user->plan ?? \App\Models\Plan::where('name', 'Free')->first();
            
            // Se o usuário tem o plano PRO ou acesso premium ativo manualmente, 
            // mostramos como Ativo para evitar confusão, mesmo sem registro de cobrança.
            $status = ($user->isPremiumActive() && $plan && $plan->name !== 'FREE') ? 'Ativo (Sistema)' : 'inactive';

            $subscription = new Subscription([
                'plan_id' => $plan->id ?? null,
                'status' => $status,
                'payment_method' => 'N/A',
                'start_date' => $user->created_at,
            ]);
            
            if ($plan) {
                $subscription->setRelation('plan', $plan);
            }
        }

        $allPlans = \App\Models\Plan::where('status', 'active')->where('type', 'student')->get();

        return view('student.subscription.index', compact('subscription', 'allPlans'));
    }

    public function updatePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'method' => 'required|in:card,pix,boleto',
            'card_token' => 'nullable', // No MP seria o token do cartão
        ]);

        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$subscription) {
            return back()->with('error', 'Assinatura não encontrada.');
        }

        // Simulação de dados do cartão vindo do Gateway
        $cardData = $validated['method'] === 'card' ? [
            'card_brand' => 'Mastercard',
            'card_last_four' => '8888',
            'card_expiry' => '05/2029',
        ] : [];

        $this->subscriptionService->updatePaymentMethod($subscription, array_merge($validated, $cardData));

        return back()->with('success', 'Forma de pagamento atualizada com sucesso!');
    }

    public function changePlan(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)->latest()->first();
        $newPlan = \App\Models\Plan::findOrFail($request->plan_id);

        if (!$subscription) return back()->with('error', 'Assinatura não encontrada.');

        if ($newPlan->price > $subscription->plan->price) {
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
            return back()->with('success', 'Cancelamento agendado com sucesso. Você manterá acesso até o fim do período.');
        }

        return back()->with('error', 'Assinatura não encontrada.');
    }
}
