<?php

namespace Tests\Feature;

use App\Models\AiCreditTransaction;
use App\Models\AiCreditWallet;
use App\Models\AiFeatureCost;
use App\Models\User;
use App\Services\AiCreditService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AiCreditSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
        Schema::dropIfExists('ai_feature_costs');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('registration_approval_status')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->dateTime('premium_expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
        });

        Schema::create('ai_feature_costs', function (Blueprint $table) {
            $table->id();
            $table->string('feature_code')->unique();
            $table->string('feature_name');
            $table->integer('credits_required');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
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
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('feature_code')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function test_consume_persists_metadata_for_audit(): void
    {
        Notification::fake();

        AiFeatureCost::create([
            'feature_code' => 'nutrition_text_analysis',
            'feature_name' => 'Analise texto',
            'credits_required' => 1,
            'is_active' => true,
        ]);

        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 5,
            'monthly_allowance' => 5,
            'extra_credits' => 0,
        ]);

        $service = app(AiCreditService::class);
        $this->assertTrue($service->consume($user, 'nutrition_text_analysis', [
            'source' => 'test',
            'input' => 'frango com arroz',
        ], 'meal-ref-1'));

        $transaction = AiCreditTransaction::first();
        $this->assertSame('test', $transaction->metadata['source'] ?? null);
        $this->assertSame('frango com arroz', $transaction->metadata['input'] ?? null);
    }

    public function test_workout_import_billing_is_idempotent_per_log(): void
    {
        Notification::fake();

        AiFeatureCost::create([
            'feature_code' => 'workout_import_photo',
            'feature_name' => 'Importacao',
            'credits_required' => 50,
            'is_active' => true,
        ]);

        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 100,
            'monthly_allowance' => 100,
            'extra_credits' => 0,
        ]);

        $log = new \App\Models\WorkoutImportLog([
            'id' => 42,
            'user_id' => $user->id,
            'status' => 'WAITING_REVIEW',
        ]);
        $log->exists = true;

        $billing = app(\App\Services\WorkoutImportBillingService::class);
        $this->assertTrue($billing->chargeOnSuccessfulExtraction($user, $log, 'test_process'));
        $this->assertTrue($billing->chargeOnSuccessfulExtraction($user, $log, 'test_process'));

        $this->assertSame(50, AiCreditWallet::first()->balance);
        $this->assertDatabaseCount('ai_credit_transactions', 1);
        $this->assertSame('test_process', AiCreditTransaction::first()->metadata['source'] ?? null);
    }

    public function test_has_credits_is_fail_closed_when_feature_is_not_configured(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 500,
            'monthly_allowance' => 500,
            'extra_credits' => 0,
        ]);

        $service = app(AiCreditService::class);

        $this->assertFalse($service->hasCredits($user, 'unknown_feature'));
    }

    public function test_consume_is_fail_closed_when_feature_is_not_configured(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 500,
            'monthly_allowance' => 500,
            'extra_credits' => 0,
        ]);

        $service = app(AiCreditService::class);

        $this->assertFalse($service->consume($user, 'unknown_feature'));
        $this->assertSame(500, AiCreditWallet::first()->balance);
        $this->assertDatabaseCount('ai_credit_transactions', 0);
    }

    public function test_consume_is_idempotent_for_same_reference_id(): void
    {
        Notification::fake();

        AiFeatureCost::create([
            'feature_code' => 'ai_orchestrator',
            'feature_name' => 'Orquestrador',
            'credits_required' => 15,
            'is_active' => true,
        ]);

        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 30,
            'monthly_allowance' => 30,
            'extra_credits' => 0,
        ]);

        $service = app(AiCreditService::class);

        $this->assertTrue($service->consume($user, 'ai_orchestrator', [], 'ref-123'));
        $this->assertTrue($service->consume($user, 'ai_orchestrator', [], 'ref-123'));

        $this->assertSame(15, AiCreditWallet::first()->balance);
        $this->assertDatabaseCount('ai_credit_transactions', 1);
    }

    public function test_refund_restores_consumed_credits(): void
    {
        Notification::fake();

        AiFeatureCost::create([
            'feature_code' => 'evolution_ai_report',
            'feature_name' => 'Relatorio',
            'credits_required' => 80,
            'is_active' => true,
        ]);

        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 80,
            'monthly_allowance' => 80,
            'extra_credits' => 0,
        ]);

        $service = app(AiCreditService::class);
        $referenceId = 'evolution_report_99';

        $this->assertTrue($service->consume($user, 'evolution_ai_report', [], $referenceId));
        $this->assertSame(0, AiCreditWallet::first()->balance);

        $this->assertTrue($service->refund($user, 'evolution_ai_report', $referenceId));
        $this->assertSame(80, AiCreditWallet::first()->balance);

        $refund = AiCreditTransaction::query()->where('type', 'refund')->first();
        $this->assertNotNull($refund);
        $this->assertSame(80, $refund->credits);
    }
}
