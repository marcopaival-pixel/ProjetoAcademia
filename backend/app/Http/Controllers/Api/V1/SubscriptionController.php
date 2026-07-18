<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans(): JsonResponse
    {
        $plans = Plan::query()
            ->whereIn('type', ['student', 'aluno'])
            ->orWhereNull('type')
            ->orderBy('price')
            ->get();

        return response()->json([
            'data' => [
                'plans' => $plans->map(fn (Plan $plan) => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => (float) $plan->price,
                    'billing_cycle' => 'monthly',
                    'description' => $plan->description,
                ])->values(),
            ],
        ]);
    }

    public function current(Request $request): JsonResponse
    {
        $subscription = UserPlan::with('plan')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->first();

        return response()->json([
            'data' => [
                'subscription' => $subscription ? $this->payload($subscription) : null,
                'is_premium' => $request->user()->hasPremiumAccess(),
            ],
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'payment_method' => ['nullable', 'string', 'max:40'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        return response()->json([
            'data' => [
                'status' => 'pending',
                'subscription_id' => null,
                'plan' => $plan->name,
                'checkout_url' => route('checkout.index', $plan),
                'gateway' => 'web_checkout',
                'app_return_links' => [
                    'success' => url('/dashboard'),
                    'pending' => url('/dashboard'),
                    'failure' => url('/plans'),
                ],
            ],
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $subscription = UserPlan::where('user_id', $request->user()->id)
            ->latest('id')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
        }

        return response()->json(['data' => ['cancelled' => (bool) $subscription]]);
    }

    private function payload(UserPlan $subscription): array
    {
        return [
            'id' => $subscription->id,
            'status' => $subscription->status,
            'financial_status' => $subscription->status,
            'payment_method' => null,
            'gateway_type' => null,
            'start_date' => optional($subscription->start_date)->toDateString(),
            'end_date' => optional($subscription->end_date)->toDateString(),
            'next_billing_date' => optional($subscription->end_date)->toDateString(),
            'cancelled_at' => null,
            'days_overdue' => null,
            'retry_count' => null,
            'plan' => $subscription->plan ? [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'price' => (float) $subscription->plan->price,
                'billing_cycle' => 'monthly',
                'description' => $subscription->plan->description,
            ] : null,
            'pending_plan' => null,
        ];
    }
}
