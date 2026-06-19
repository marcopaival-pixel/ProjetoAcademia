<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Services\Payment\PaymentGatewayManager;
use App\Support\PaymentGatewayRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function __invoke(Request $request, PaymentGatewayManager $gateways): JsonResponse
    {
        $user = $request->user();
        $activeGateway = $gateways->getDefaultDriver();
        $setting = PaymentSetting::where('gateway', $activeGateway)->first();

        if (! $user->isAdministrator()) {
            return response()->json([
                'active_gateway' => $activeGateway,
                'active_label' => PaymentGatewayRegistry::options()[$activeGateway] ?? $activeGateway,
                'methods' => [
                    'credit_card' => (bool) ($setting?->enable_credit_card),
                    'pix' => (bool) ($setting?->enable_pix),
                    'boleto' => (bool) ($setting?->enable_boleto),
                ],
            ]);
        }

        $configured = PaymentSetting::query()
            ->whereIn('gateway', PaymentGatewayRegistry::IMPLEMENTED)
            ->get()
            ->map(fn (PaymentSetting $row) => [
                'gateway' => $row->gateway,
                'label' => PaymentGatewayRegistry::options()[$row->gateway] ?? $row->gateway,
                'status' => $row->status,
                'environment' => $row->environment,
                'enable_credit_card' => (bool) $row->enable_credit_card,
                'enable_pix' => (bool) $row->enable_pix,
                'enable_boleto' => (bool) $row->enable_boleto,
                'is_configured' => $this->isConfigured($row),
            ]);

        return response()->json([
            'active_gateway' => $activeGateway,
            'active_label' => PaymentGatewayRegistry::options()[$activeGateway] ?? $activeGateway,
            'methods' => [
                'credit_card' => (bool) ($setting?->enable_credit_card),
                'pix' => (bool) ($setting?->enable_pix),
                'boleto' => (bool) ($setting?->enable_boleto),
            ],
            'gateways' => $configured->values(),
            'implemented' => PaymentGatewayRegistry::IMPLEMENTED,
        ]);
    }

    private function isConfigured(PaymentSetting $setting): bool
    {
        if ($setting->gateway === 'mercadopago') {
            return filled($setting->access_token) || filled($setting->public_key);
        }

        if ($setting->gateway === 'asaas') {
            return filled($setting->access_token) || filled($setting->client_secret);
        }

        return false;
    }
}
