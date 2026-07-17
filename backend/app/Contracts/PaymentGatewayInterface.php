<?php

namespace App\Contracts;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Get the gateway unique identifier.
     */
    public function getIdentifier(): string;

    /**
     * Create a checkout session/preference.
     */
    public function createCheckout(User $user, float $amount, array $options = []): array;

    /**
     * Create a subscription.
     */
    public function createSubscription(User $user, $plan, array $options = []): array;

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription($gatewaySubscriptionId): bool;

    /**
     * Fetch payment details from gateway.
     */
    public function fetchPayment(string $paymentId): array;

    /**
     * Handle webhook notification.
     */
    public function handleWebhook(Request $request): array;

    /**
     * Validate webhook signature.
     */
    public function validateSignature(Request $request): bool;

    /**
     * Refund a payment.
     */
    public function refund(string $paymentId, ?float $amount = null): bool;
}
