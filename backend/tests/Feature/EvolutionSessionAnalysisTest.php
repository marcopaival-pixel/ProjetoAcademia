<?php

namespace Tests\Feature;

use App\Models\EvolutionPhoto;
use App\Models\EvolutionReport;
use App\Models\EvolutionSessionAnalysis;
use App\Models\User;
use App\Jobs\GenerateEvolutionReport;
use App\Models\BodyAssessment;
use App\Services\BodyPhotoValidationService;
use App\Services\AI\Agents\VisionAgent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class EvolutionSessionAnalysisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('evolution_session_analyses');
        Schema::dropIfExists('ai_execution_logs');
        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
        Schema::dropIfExists('ai_feature_costs');
        Schema::dropIfExists('evolution_reports');
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('evolution_photos');
        Schema::dropIfExists('body_assessments');
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

        Schema::create('evolution_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('photo_path');
            $table->string('type')->default('front');
            $table->date('registered_date');
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('body_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('bf_percent', 5, 2)->nullable();
            $table->decimal('muscle_percent', 5, 2)->nullable();
            $table->decimal('waist', 6, 2)->nullable();
            $table->decimal('abdomen', 6, 2)->nullable();
            $table->decimal('hips', 6, 2)->nullable();
            $table->date('assessment_date')->nullable();
            $table->timestamps();
        });

        Schema::create('evolution_session_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('session_date');
            $table->string('photo_hash', 64);
            $table->json('analysis');
            $table->string('model_name')->nullable();
            $table->unsignedInteger('total_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->default(0);
            $table->timestamps();
        });

        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('version')->default('1.0');
            $table->string('consent_type', 50);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('evolution_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('consent_id')->nullable();
            $table->date('current_session_date')->nullable();
            $table->date('previous_session_date')->nullable();
            $table->string('status', 40);
            $table->string('provider', 40)->nullable();
            $table->json('validation_result')->nullable();
            $table->json('objective_metrics')->nullable();
            $table->json('comparison_result')->nullable();
            $table->json('audit_result')->nullable();
            $table->json('final_report')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('limited_reason')->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->string('schema_version', 30)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evolution_report_id');
            $table->string('agent', 80);
            $table->string('provider', 40)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('reasoning_effort', 20)->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->string('schema_version', 30)->nullable();
            $table->string('request_hash', 128)->nullable();
            $table->string('response_id', 128)->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->decimal('cost', 10, 6)->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('status', 30);
            $table->string('error_code', 80)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::create('ai_feature_costs', function (Blueprint $table) {
            $table->id();
            $table->string('feature_code')->unique();
            $table->string('feature_name')->nullable();
            $table->unsignedInteger('credits_required')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('balance')->default(1000);
            $table->integer('monthly_allowance')->default(0);
            $table->integer('extra_credits')->default(1000);
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
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        \App\Models\AiFeatureCost::create([
            'feature_code' => 'evolution_session_analysis',
            'feature_name' => 'Analise de Sessao de Evolucao',
            'credits_required' => 60,
            'is_active' => true,
        ]);

        \App\Models\AiFeatureCost::create([
            'feature_code' => 'evolution_ai_report',
            'feature_name' => 'Relatorio Inteligente de Evolucao',
            'credits_required' => 80,
            'is_active' => true,
        ]);
    }

    private function seedWalletFor(User $user, int $balance = 1000): void
    {
        \App\Models\AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => $balance,
            'monthly_allowance' => $balance,
            'extra_credits' => 0,
        ]);
    }

    public function test_session_analysis_is_cached_by_photo_hash(): void
    {
        $this->withoutMiddleware();

        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));
        $this->seedWalletFor($user);

        EvolutionPhoto::create([
            'user_id' => $user->id,
            'photo_path' => 'evolution/front.jpg',
            'type' => 'front',
            'registered_date' => '2026-07-15',
        ]);

        EvolutionPhoto::create([
            'user_id' => $user->id,
            'photo_path' => 'evolution/back.jpg',
            'type' => 'back',
            'registered_date' => '2026-07-15',
        ]);

        $agent = Mockery::mock(VisionAgent::class);
        $agent->shouldReceive('execute')
            ->once()
            ->andReturn([
                'ok' => true,
                'structured_data' => [
                    'summary' => 'Sessão consistente.',
                    'next_recommendations' => ['Repita os mesmos ângulos na próxima sessão.'],
                ],
                'model' => 'test-model',
                'tokens' => 123,
                'cost' => 0.001,
            ]);

        $this->app->instance(VisionAgent::class, $agent);

        $payload = ['date' => '2026-07-15'];

        $this->actingAs($user)->postJson(route('evolution.analyze-session'), $payload)
            ->assertOk()
            ->assertJsonPath('cached', false)
            ->assertJsonPath('analysis.summary', 'Sessão consistente.');

        $this->actingAs($user)->postJson(route('evolution.analyze-session'), $payload)
            ->assertOk()
            ->assertJsonPath('cached', true)
            ->assertJsonPath('analysis.summary', 'Sessão consistente.');

        $this->assertDatabaseCount('evolution_session_analyses', 1);
    }

    public function test_prune_command_removes_old_and_excess_session_analyses(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());

        EvolutionSessionAnalysis::create([
            'user_id' => $user->id,
            'session_date' => now()->subMonths(13)->toDateString(),
            'photo_hash' => 'old',
            'analysis' => ['summary' => 'old'],
        ]);

        foreach (range(1, 3) as $index) {
            EvolutionSessionAnalysis::create([
                'user_id' => $user->id,
                'session_date' => now()->subDays($index)->toDateString(),
                'photo_hash' => 'recent-' . $index,
                'analysis' => ['summary' => 'recent'],
            ]);
        }

        $this->artisan('evolution:prune-session-analyses', [
            '--months' => 12,
            '--per-user' => 2,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('evolution_session_analyses', ['photo_hash' => 'old']);
        $this->assertDatabaseCount('evolution_session_analyses', 2);
    }

    public function test_rejected_manual_evolution_photo_is_not_saved(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));

        $validator = Mockery::mock(BodyPhotoValidationService::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn([
                'approved' => false,
                'messages' => ['A imagem enviada não é uma fotografia de uma pessoa.'],
            ]);

        $this->app->instance(BodyPhotoValidationService::class, $validator);

        $this->actingAs($user)->post(route('evolution.store'), [
            'photo' => UploadedFile::fake()->image('invalid.jpg', 900, 900),
            'type' => 'front',
            'registered_date' => '2026-07-15',
        ])->assertRedirect()
          ->assertSessionHas('error', 'A imagem enviada não é uma fotografia de uma pessoa.');

        $this->assertDatabaseCount('evolution_photos', 0);
        Storage::disk('public')->assertMissing('evolution/invalid.jpg');
    }

    public function test_approved_manual_evolution_photo_is_saved_with_validation_notes(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));

        $validator = Mockery::mock(BodyPhotoValidationService::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn([
                'approved' => true,
                'status' => 'approved',
                'checks' => ['quality' => 'good'],
                'classification' => ['view' => 'front', 'framing' => 'half_body'],
                'confidence' => 0.91,
            ]);

        $this->app->instance(BodyPhotoValidationService::class, $validator);

        $this->actingAs($user)->post(route('evolution.store'), [
            'photo' => UploadedFile::fake()->image('valid.jpg', 900, 900),
            'type' => 'front',
            'registered_date' => '2026-07-15',
            'weight_kg' => 82.5,
        ])->assertRedirect()
          ->assertSessionHas('success');

        $photo = EvolutionPhoto::first();

        $this->assertNotNull($photo);
        $this->assertSame($user->id, $photo->user_id);
        $this->assertSame('front', $photo->type);
        $this->assertSame('2026-07-15', (string) $photo->registered_date);
        $this->assertEquals(82.5, (float) $photo->weight_kg);
        Storage::disk('public')->assertExists($photo->photo_path);

        $notes = json_decode($photo->notes, true);
        $this->assertSame('evolution_manual_upload', $notes['source']);
        $this->assertTrue($notes['validation']['approved']);
        $this->assertSame('good', $notes['validation']['checks']['quality']);
    }

    public function test_api_can_request_and_poll_async_evolution_report(): void
    {
        Queue::fake();

        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));
        $this->seedWalletFor($user);
        Sanctum::actingAs($user);

        foreach (['front', 'back', 'right_side', 'left_side'] as $type) {
            EvolutionPhoto::create([
                'user_id' => $user->id,
                'photo_path' => "evolution/current-{$type}.jpg",
                'type' => $type,
                'registered_date' => '2026-07-15',
            ]);

            EvolutionPhoto::create([
                'user_id' => $user->id,
                'photo_path' => "evolution/previous-{$type}.jpg",
                'type' => $type,
                'registered_date' => '2026-06-15',
            ]);
        }

        $response = $this->postJson('/api/v1/evolution-reports', [
            'accept_ai_body_photo_analysis' => true,
        ])
            ->assertAccepted()
            ->assertJsonPath('data.status', EvolutionReport::STATUS_PENDING)
            ->assertJsonPath('data.current_session_date', '2026-07-15')
            ->assertJsonPath('data.previous_session_date', '2026-06-15');

        Queue::assertPushed(GenerateEvolutionReport::class);

        $reportId = $response->json('data.report_id');

        $this->getJson("/api/v1/evolution-reports/{$reportId}")
            ->assertOk()
            ->assertJsonPath('data.id', $reportId)
            ->assertJsonPath('data.status', EvolutionReport::STATUS_PENDING);

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'ai_body_photo_analysis',
        ]);
    }

    public function test_api_requires_specific_ai_body_photo_consent(): void
    {
        Queue::fake();

        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));
        $this->seedWalletFor($user);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/evolution-reports')
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'ai_body_photo_analysis_consent_required');

        Queue::assertNothingPushed();
    }

    public function test_generate_evolution_report_job_persists_final_report_and_execution_logs(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ]));
        $this->seedWalletFor($user);

        $consentId = \App\Models\UserConsent::create([
            'user_id' => $user->id,
            'consent_type' => 'ai_body_photo_analysis',
            'version' => '1.0',
        ])->id;

        foreach (['front', 'back', 'right_side', 'left_side'] as $type) {
            EvolutionPhoto::create([
                'user_id' => $user->id,
                'photo_path' => "evolution/current-{$type}.jpg",
                'type' => $type,
                'registered_date' => '2026-07-15',
            ]);

            EvolutionPhoto::create([
                'user_id' => $user->id,
                'photo_path' => "evolution/previous-{$type}.jpg",
                'type' => $type,
                'registered_date' => '2026-06-15',
            ]);
        }

        BodyAssessment::create([
            'user_id' => $user->id,
            'weight_kg' => 80,
            'waist' => 88,
            'abdomen' => 92,
            'assessment_date' => '2026-07-15',
        ]);

        BodyAssessment::create([
            'user_id' => $user->id,
            'weight_kg' => 82,
            'waist' => 90,
            'abdomen' => 95,
            'assessment_date' => '2026-06-15',
        ]);

        EvolutionSessionAnalysis::create([
            'user_id' => $user->id,
            'session_date' => '2026-07-15',
            'photo_hash' => 'hash-current',
            'analysis' => [
                'summary' => 'diferença sutil no contorno geral',
                'comparison_quality' => 'fotos com enquadramento comparável',
            ],
        ]);

        $report = EvolutionReport::create([
            'user_id' => $user->id,
            'consent_id' => $consentId,
            'current_session_date' => '2026-07-15',
            'previous_session_date' => '2026-06-15',
            'status' => EvolutionReport::STATUS_PENDING,
            'provider' => 'local',
            'prompt_version' => 'evolution-report-orchestrator:v2',
            'schema_version' => 'evolution-report:v1',
        ]);

        (new GenerateEvolutionReport($report->id))->handle(
            app(\App\Services\AI\EvolutionReportOrchestratorService::class),
            app(\App\Services\AiCreditService::class),
        );

        $report->refresh();

        $this->assertContains($report->status, [
            EvolutionReport::STATUS_COMPLETED,
            EvolutionReport::STATUS_COMPLETED_WITH_LIMITATIONS,
        ]);
        $this->assertNotEmpty($report->final_report);
        $this->assertNotEmpty($report->objective_metrics);
        $this->assertNotEmpty($report->audit_result);
        $this->assertDatabaseHas('ai_execution_logs', [
            'evolution_report_id' => $report->id,
            'agent' => 'evolution_report_orchestrator',
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('ai_execution_logs', [
            'evolution_report_id' => $report->id,
            'agent' => 'anti_hallucination_auditor',
        ]);
    }
}
