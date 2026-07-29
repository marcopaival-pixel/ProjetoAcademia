<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payment\CommissionClawbackService;
use App\Services\Payment\PaymentProcessor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommissionClawbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('representative_id')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('gateway')->default('mercadopago');
            $table->string('gateway_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('BRL');
            $table->string('status', 32)->default('paid');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('representative_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->decimal('base_amount', 12, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->string('status', 32)->default('PENDENTE');
            $table->timestamp('available_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('action');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('origin')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('observation')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function test_pending_commission_is_cancelled_on_refund(): void
    {
        $rep = User::withoutEvents(fn () => User::create(['email' => 'rep@test.com', 'name' => 'Rep']));
        $user = User::withoutEvents(fn () => User::create([
            'email' => 'user@test.com',
            'name' => 'User',
            'representative_id' => $rep->id,
        ]));

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'pay-123',
            'amount' => 100,
            'net_amount' => 100,
            'status' => 'paid',
        ]);

        $commission = Commission::create([
            'representative_id' => $rep->id,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'base_amount' => 100,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'status' => Commission::STATUS_PENDENTE,
        ]);

        app(PaymentProcessor::class)->processRefund([
            'gateway' => 'mercadopago',
            'gateway_id' => 'pay-123',
            'reason' => 'refunded',
        ]);

        $commission->refresh();
        $payment->refresh();

        $this->assertSame(Commission::STATUS_CANCELADO, $commission->status);
        $this->assertSame('refunded', $payment->status);
        $this->assertSame(0, Commission::where('commission_amount', '<', 0)->count());
    }

    public function test_paid_commission_generates_clawback_entry(): void
    {
        $rep = User::withoutEvents(fn () => User::create(['email' => 'rep2@test.com', 'name' => 'Rep']));
        $user = User::withoutEvents(fn () => User::create([
            'email' => 'user2@test.com',
            'name' => 'User',
            'representative_id' => $rep->id,
        ]));

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'asaas',
            'gateway_id' => 'pay-456',
            'amount' => 200,
            'net_amount' => 200,
            'status' => 'paid',
        ]);

        Commission::create([
            'representative_id' => $rep->id,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'base_amount' => 200,
            'commission_rate' => 10,
            'commission_amount' => 20,
            'status' => Commission::STATUS_PAGO,
            'paid_at' => now(),
        ]);

        app(CommissionClawbackService::class)->reverseForPayment($payment, 'charged_back');

        $this->assertSame(1, Commission::where('status', Commission::STATUS_CANCELADO)->count());
        $clawback = Commission::where('commission_amount', '<', 0)->first();
        $this->assertNotNull($clawback);
        $this->assertSame(-20.0, (float) $clawback->commission_amount);
        $this->assertSame(Commission::STATUS_DISPONIVEL, $clawback->status);
    }

    public function test_refund_processing_is_idempotent(): void
    {
        $user = User::withoutEvents(fn () => User::create(['email' => 'user3@test.com', 'name' => 'User']));

        Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'pay-789',
            'amount' => 50,
            'net_amount' => 50,
            'status' => 'paid',
        ]);

        $processor = app(PaymentProcessor::class);
        $processor->processRefund(['gateway' => 'mercadopago', 'gateway_id' => 'pay-789']);
        $second = $processor->processRefund(['gateway' => 'mercadopago', 'gateway_id' => 'pay-789']);

        $this->assertStringContainsString('idempotente', $second['message']);
        $this->assertSame(1, Payment::where('gateway_id', 'pay-789')->where('status', 'refunded')->count());
    }
}
