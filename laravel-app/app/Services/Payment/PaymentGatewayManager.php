<?php

namespace App\Services\Payment;

use App\Models\PaymentSetting;
use App\Services\MercadoPagoService;
use App\Services\Payment\AsaasService;
use Illuminate\Support\Manager;

class PaymentGatewayManager extends Manager
{
    public function getDefaultDriver()
    {
        $activeSetting = PaymentSetting::where('status', 'active')
            ->orderBy('priority', 'desc')
            ->first();

        return $activeSetting ? $activeSetting->gateway : 'mercadopago';
    }

    public function createMercadopagoDriver()
    {
        $config = $this->getGatewayConfig('mercadopago');
        return new MercadoPagoService($config);
    }

    // Placeholders for future drivers
    public function createAsaasDriver()
    {
        return new AsaasService($this->getGatewayConfig('asaas'));
    }

    public function createStripeDriver()
    {
        // return new StripeService($this->getGatewayConfig('stripe'));
    }

    protected function getGatewayConfig(string $gateway)
    {
        $setting = PaymentSetting::where('gateway', $gateway)->first();
        
        if (!$setting) {
            return [];
        }

        return $setting->toArray();
    }
}
