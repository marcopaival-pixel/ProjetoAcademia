<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommissionReleaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('commissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('gateway')->default('mercadopago');
            $table->string('gateway_id')->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('paid');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('representative_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('payment_id');
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('status', 32)->default('PENDENTE');
            $table->timestamp('available_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_release_command_promotes_pending_commissions(): void
    {
        $user = User::withoutEvents(fn () => User::create(['email' => 'u@test.com']));
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway_id' => 'pay-release-1',
            'amount' => 100,
        ]);

        $commission = Commission::create([
            'representative_id' => $user->id,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'base_amount' => 100,
            'commission_rate' => 10,
            'commission_amount' => 10,
            'status' => Commission::STATUS_PENDENTE,
            'available_at' => now()->subHour(),
        ]);

        $this->artisan('commissions:release-available')->assertSuccessful();

        $commission->refresh();
        $this->assertSame(Commission::STATUS_DISPONIVEL, $commission->status);
    }

    public function test_cleanup_command_cancels_orphan_commissions(): void
    {
        $user = User::withoutEvents(fn () => User::create(['email' => 'u2@test.com']));

        $orphan = Commission::create([
            'representative_id' => $user->id,
            'user_id' => $user->id,
            'payment_id' => 99999,
            'base_amount' => 50,
            'commission_rate' => 10,
            'commission_amount' => 5,
            'status' => Commission::STATUS_PENDENTE,
        ]);

        $this->artisan('commissions:cleanup-orphans')->assertSuccessful();

        $orphan->refresh();
        $this->assertSame(Commission::STATUS_CANCELADO, $orphan->status);
    }
}
