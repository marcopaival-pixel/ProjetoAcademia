<?php

namespace Tests\Feature;

use App\Models\AiCreditPackage;
use App\Models\AiCreditTransaction;
use App\Models\User;
use App\Services\Payment\PaymentProcessor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentProcessorIdempotencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
        Schema::dropIfExists('ai_credits_packages');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('representative_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_credits_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('credits');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('balance')->default(0);
            $table->integer('monthly_allowance')->default(0);
            $table->integer('extra_credits')->default(0);
            $table->date('renewal_date')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('type');
            $table->integer('credits');
            $table->integer('balance_before')->default(0);
            $table->integer('balance_after')->default(0);
            $table->string('feature_code')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('gateway');
            $table->string('gateway_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('currency', 8)->default('BRL');
            $table->string('status')->default('paid');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('action');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('origin')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('observation')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('representative_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->decimal('base_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 8, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('status')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_duplicate_ai_credit_webhook_does_not_double_credit(): void
    {
        $user = User::withoutEvents(fn () => User::create([
            'email' => 'buyer@example.com',
            'name' => 'Buyer',
            'password_hash' => 'hash',
        ]));

        $package = AiCreditPackage::create([
            'name' => 'Pacote 100',
            'credits' => 100,
            'price' => 49.90,
            'is_active' => true,
        ]);

        $processor = app(PaymentProcessor::class);
        $payload = [
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_id' => 'mp-test-12345',
            'amount' => 49.90,
            'reference' => 'ai_credits:'.$package->id,
        ];

        $processor->processApproved($payload);
        $processor->processApproved($payload);

        $this->assertSame(1, AiCreditTransaction::where('user_id', $user->id)->count());
        $wallet = \App\Models\AiCreditWallet::where('user_id', $user->id)->first();
        $this->assertNotNull($wallet);
        $this->assertSame(100, (int) $wallet->balance);
    }
}
