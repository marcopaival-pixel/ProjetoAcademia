<?php

namespace Tests\Unit;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\User;
use App\Services\CommissionClawbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class CommissionClawbackServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_creates_clawback_when_commission_already_paid(): void
    {
        $representative = $this->userWithRole('representative');
        $buyer = $this->userWithRole('aluno');

        $payment = Payment::create([
            'user_id' => $buyer->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp_clawback_test',
            'amount' => 100,
            'status' => 'paid',
        ]);

        $commission = Commission::create([
            'representative_id' => $representative->id,
            'user_id' => $buyer->id,
            'payment_id' => $payment->id,
            'base_amount' => 100,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'status' => Commission::STATUS_PAGO,
            'paid_at' => now(),
        ]);

        app(CommissionClawbackService::class)->processForPayment($payment);

        $commission->refresh();
        $this->assertSame(Commission::STATUS_CLAWBACK, $commission->status);
        $this->assertEquals(-10.0, (float) $commission->commission_amount);
        $this->assertDatabaseHas('commissions', [
            'id' => $commission->id,
            'payment_id' => $payment->id,
            'status' => Commission::STATUS_CLAWBACK,
            'commission_amount' => -10,
        ]);
    }
}
