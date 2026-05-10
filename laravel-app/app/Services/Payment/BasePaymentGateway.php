<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BasePaymentGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Common method to log webhooks.
     */
    protected function logWebhook(Request $request, int $statusCode, ?string $message = null, ?string $error = null, ?float $processingTime = null)
    {
        try {
            PaymentWebhookLog::create([
                'gateway' => $this->getIdentifier(),
                'event_type' => $request->input('type') ?? $request->input('action'),
                'external_id' => $request->input('data.id') ?? $request->input('id'),
                'payload' => $request->all(),
                'headers' => $request->headers->all(),
                'status_code' => $statusCode,
                'status_message' => $message,
                'processing_time' => $processingTime,
                'error' => $error,
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log webhook: ' . $e->getMessage());
        }
    }
}
