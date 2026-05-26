<?php

namespace Tests\Feature;

use App\Support\PaymentGatewayRegistry;
use Tests\TestCase;

class PaymentGatewayRegistryTest extends TestCase
{
    public function test_only_implemented_gateways_are_registered(): void
    {
        $this->assertSame(['mercadopago', 'asaas'], PaymentGatewayRegistry::IMPLEMENTED);
        $this->assertFalse(PaymentGatewayRegistry::isImplemented('stripe'));
        $this->assertFalse(PaymentGatewayRegistry::isImplemented('pagseguro'));
    }
}
