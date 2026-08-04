<?php

namespace Tests\Feature;

use App\Models\AIChat;
use App\Models\AiCreditWallet;
use App\Models\AiFeatureCost;
use App\Models\User;
use App\Models\UserConsent;
use App\Services\AI\OrchestratorService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class ChatConsentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('ai_chats');
        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
        Schema::dropIfExists('ai_feature_costs');
        Schema::dropIfExists('user_consents');
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
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
        });

        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('consent_type', 50);
            $table->string('version')->default('1.0');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
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

        Schema::create('ai_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('role');
            $table->text('message');
            $table->timestamps();
        });

        AiFeatureCost::create([
            'feature_code' => 'chat_response',
            'feature_name' => 'Chat IA',
            'credits_required' => 10,
            'is_active' => true,
        ]);
    }

    public function test_api_chat_requires_health_data_consent(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 100,
            'monthly_allowance' => 100,
            'extra_credits' => 0,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/chat/send', [
            'message' => 'Como melhorar meu treino?',
        ])->assertStatus(409)
            ->assertJsonPath('code', 'ai_chat_health_data_consent_required');

        $this->assertDatabaseCount('ai_chats', 0);
    }

    public function test_api_chat_accepts_inline_consent_and_processes_message(): void
    {
        Notification::fake();

        $user = User::withoutEvents(fn () => User::factory()->create());
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 100,
            'monthly_allowance' => 100,
            'extra_credits' => 0,
        ]);
        Sanctum::actingAs($user);

        $orchestrator = Mockery::mock(OrchestratorService::class);
        $orchestrator->shouldReceive('run')
            ->once()
            ->andReturn([
                'status' => 'success',
                'message' => 'Resposta de teste.',
            ]);
        $this->app->instance(OrchestratorService::class, $orchestrator);

        $this->postJson('/api/v1/chat/send', [
            'message' => 'Como melhorar meu treino?',
            'accept_ai_chat_health_data' => true,
        ])->assertOk()
            ->assertJsonPath('data.message', 'Resposta de teste.');

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'ai_chat_health_data',
        ]);
        $this->assertDatabaseCount('ai_chats', 2);
    }
}
