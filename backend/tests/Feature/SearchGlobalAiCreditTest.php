<?php

namespace Tests\Feature;

use App\Models\AiCreditTransaction;
use App\Models\AiCreditWallet;
use App\Models\AiFeatureCost;
use App\Models\User;
use App\Services\AI\OrchestratorService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class SearchGlobalAiCreditTest extends TestCase
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
            $table->boolean('is_premium')->default(true);
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

        AiFeatureCost::create([
            'feature_code' => 'ai_orchestrator',
            'feature_name' => 'Orquestrador IA',
            'credits_required' => 15,
            'is_active' => true,
        ]);

        Schema::create('exercises_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('muscle_group')->nullable();
            $table->string('equipment')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('goal')->nullable();
        });

        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->nullable();
            $table->text('conteudo')->nullable();
            $table->boolean('ativo')->default(true);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
        });
    }

    public function test_global_search_does_not_call_ai_when_credits_are_insufficient(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_admin' => false,
        ]));

        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'monthly_allowance' => 0,
            'extra_credits' => 0,
        ]);

        $orchestrator = Mockery::mock(OrchestratorService::class);
        $orchestrator->shouldNotReceive('run');
        $this->app->instance(OrchestratorService::class, $orchestrator);

        $this->actingAs($user);

        $view = app(\App\Http\Controllers\SearchController::class)->search(
            \Illuminate\Http\Request::create('/global-search', 'GET', [
                'q' => 'como melhorar meu treino de pernas',
            ])
        );

        $data = $view->getData();
        $this->assertNull($data['aiResponse']);
        $this->assertSame('credits_exceeded', $data['aiCreditsNotice']['code']);
        $this->assertDatabaseCount('ai_credit_transactions', 0);
    }

    public function test_global_search_consumes_credits_when_ai_succeeds(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_admin' => false,
        ]));

        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 100,
            'monthly_allowance' => 100,
            'extra_credits' => 0,
        ]);

        $orchestrator = Mockery::mock(OrchestratorService::class);
        $orchestrator->shouldReceive('run')
            ->once()
            ->andReturn([
                'status' => 'success',
                'message' => 'Resposta da busca inteligente.',
            ]);
        $this->app->instance(OrchestratorService::class, $orchestrator);

        $this->actingAs($user);

        $view = app(\App\Http\Controllers\SearchController::class)->search(
            \Illuminate\Http\Request::create('/global-search', 'GET', [
                'q' => 'como melhorar meu treino de pernas',
            ])
        );

        $data = $view->getData();
        $this->assertSame('Resposta da busca inteligente.', $data['aiResponse']['text']);
        $this->assertDatabaseHas('ai_credit_transactions', [
            'user_id' => $user->id,
            'feature_code' => 'ai_orchestrator',
            'type' => 'usage',
        ]);
    }
}
