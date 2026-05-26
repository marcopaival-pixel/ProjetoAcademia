<?php

namespace App\Services\Payment;

use App\Models\PaymentSetting;
use App\Services\MercadoPagoService;
use App\Services\Payment\AsaasService;
use App\Support\PaymentGatewayRegistry;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class PaymentGatewayManager extends Manager
{
    public function getDefaultDriver()
    {
        $activeSetting = PaymentSetting::where('status', 'active')
            ->orderBy('priority', 'desc')
            ->first();

        $gateway = $activeSetting ? $activeSetting->gateway : 'mercadopago';

        return PaymentGatewayRegistry::isImplemented($gateway) ? $gateway : 'mercadopago';
    }

    public function createDriver($driver)
    {
        if (! PaymentGatewayRegistry::isImplemented($driver)) {
            throw new InvalidArgumentException("Gateway de pagamento não implementado: {$driver}");
        }

        return parent::createDriver($driver);
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

    protected function getGatewayConfig(string $gateway)
    {
        $setting = PaymentSetting::where('gateway', $gateway)->first();
        
        if (!$setting) {
            return [];
        }

        return $setting->toArray();
    }
}
