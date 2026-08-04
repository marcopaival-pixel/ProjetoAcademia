<?php

namespace Tests\Feature;

use App\Models\AiCreditTransaction;
use App\Models\AiCreditWallet;
use App\Models\BodyAnalysis;
use App\Models\User;
use App\Services\BodyPhotoValidationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class BodyAnalysisCreditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
        Schema::dropIfExists('ai_feature_costs');
        Schema::dropIfExists('body_analyses');
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('community_stickers');
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
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->dateTime('premium_expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('body_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->string('photo_path');
            $table->string('view_type', 20)->default('front');
            $table->json('landmarks')->nullable();
            $table->json('metrics')->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('analysis_version', 80)->nullable();
            $table->string('vision_model', 80)->nullable();
            $table->decimal('vision_confidence', 5, 4)->nullable();
            $table->json('vision_raw_payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
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

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_one_id')->nullable();
            $table->unsignedInteger('user_two_id')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedInteger('sender_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('community_stickers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(false);
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
            $table->date('expires_at')->nullable();
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

        \App\Models\AiFeatureCost::create([
            'feature_code' => 'analyze_body_photo',
            'feature_name' => 'Analise de Foto Corporal',
            'credits_required' => 50,
            'is_active' => true,
        ]);
    }

    private function grantBodyPhotoConsent(User $user): void
    {
        \App\Models\UserConsent::create([
            'user_id' => $user->id,
            'consent_type' => 'ai_body_photo_analysis',
            'version' => '1.0',
        ]);
    }

    public function test_rejected_body_analysis_photo_does_not_consume_credits(): void
    {
        $this->withoutMiddleware();
        Notification::fake();
        Storage::fake('public');
        Storage::fake('local');

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));
        $this->grantBodyPhotoConsent($user);
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 50,
            'monthly_allowance' => 50,
            'extra_credits' => 0,
        ]);

        $validator = Mockery::mock(BodyPhotoValidationService::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn([
                'approved' => false,
                'messages' => ['Nao foi possivel identificar uma pessoa na imagem.'],
            ]);

        $this->app->instance(BodyPhotoValidationService::class, $validator);

        $this->actingAs($user)->postJson(route('body-analysis.store'), [
            'image' => UploadedFile::fake()->image('invalid.jpg', 900, 900),
            'view_type' => 'front',
        ])->assertStatus(422)
            ->assertJsonPath('code', 'photo_rejected');

        $this->assertSame(50, AiCreditWallet::first()->balance);
        $this->assertDatabaseCount('ai_credit_transactions', 0);
        $this->assertDatabaseCount('body_analyses', 0);
    }

    public function test_approved_body_analysis_photo_consumes_credit_after_analysis_is_saved(): void
    {
        $this->withoutMiddleware();
        Notification::fake();
        Storage::fake('public');
        Storage::fake('local');

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));
        $this->grantBodyPhotoConsent($user);
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 50,
            'monthly_allowance' => 50,
            'extra_credits' => 0,
        ]);

        $validator = Mockery::mock(BodyPhotoValidationService::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn([
                'approved' => true,
                'status' => 'approved',
                'messages' => [],
            ]);

        $this->app->instance(BodyPhotoValidationService::class, $validator);

        $this->actingAs($user)->postJson(route('body-analysis.store'), [
            'image' => UploadedFile::fake()->image('valid.jpg', 900, 900),
            'view_type' => 'front',
            'metrics' => json_encode(['posture_score' => 95]),
        ])->assertOk()
            ->assertJsonPath('success', true);

        $analysis = BodyAnalysis::first();

        $this->assertNotNull($analysis);
        $this->assertSame('mediapipe_pose_rules_v2', $analysis->analysis_version);
        Storage::disk('local')->assertExists($analysis->photo_path);

        $wallet = AiCreditWallet::first();
        $this->assertSame(0, $wallet->balance);
        $this->assertSame(0, $wallet->monthly_allowance);

        $transaction = AiCreditTransaction::first();
        $this->assertSame('usage', $transaction->type);
        $this->assertSame(-50, $transaction->credits);
        $this->assertSame('analyze_body_photo', $transaction->feature_code);
    }

    public function test_user_can_load_saved_body_analysis_as_json(): void
    {
        $this->withoutMiddleware();
        Notification::fake();
        Storage::fake('public');
        Storage::fake('local');

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));
        $analysisId = DB::table('body_analyses')->insertGetId([
            'user_id' => $user->id,
            'photo_path' => 'body-analyses/example.jpg',
            'view_type' => 'front',
            'metrics' => json_encode(['posture_score' => 88]),
            'ai_summary' => json_encode([
                'summary' => 'Analise salva.',
                'diet' => 'Recuperacao geral.',
                'workout' => 'Treino tecnico.',
                'exercises' => ['Remada baixa'],
                'attention_points' => ['Observar ombros.'],
                'limitations' => ['Foto 2D.'],
            ]),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now(),
        ]);
        $analysis = BodyAnalysis::withoutGlobalScopes()->findOrFail($analysisId);
        $this->assertSame($user->id, (int) DB::table('body_analyses')->where('id', $analysisId)->value('user_id'));

        $this->actingAs($user)
            ->getJson(route('body-analysis.show', $analysisId))
            ->assertOk()
            ->assertJsonPath('analysis.summary', 'Analise salva.')
            ->assertJsonPath('analysis.metrics.posture_score', 88)
            ->assertJsonPath('analysis.attention_points.0', 'Observar ombros.');
    }

    public function test_user_cannot_load_another_users_body_analysis(): void
    {
        $this->withoutMiddleware();
        Notification::fake();

        $owner = User::withoutEvents(fn () => User::factory()->create());
        $intruder = User::withoutEvents(fn () => User::factory()->create());

        $analysisId = DB::table('body_analyses')->insertGetId([
            'user_id' => $owner->id,
            'photo_path' => 'body-analyses/private.jpg',
            'view_type' => 'front',
            'metrics' => json_encode(['posture_score' => 80]),
            'ai_summary' => json_encode(['summary' => 'Privado.']),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now(),
        ]);

        $this->actingAs($intruder)
            ->getJson(route('body-analysis.show', $analysisId))
            ->assertNotFound();
    }

    public function test_user_cannot_compare_another_users_body_analysis(): void
    {
        $this->withoutMiddleware();
        Notification::fake();

        $owner = User::withoutEvents(fn () => User::factory()->create());
        $intruder = User::withoutEvents(fn () => User::factory()->create());

        $ownAnalysisId = DB::table('body_analyses')->insertGetId([
            'user_id' => $intruder->id,
            'photo_path' => 'body-analyses/own.jpg',
            'view_type' => 'front',
            'metrics' => json_encode(['posture_score' => 80]),
            'ai_summary' => json_encode(['summary' => 'Propria.']),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now(),
        ]);

        $privateAnalysisId = DB::table('body_analyses')->insertGetId([
            'user_id' => $owner->id,
            'photo_path' => 'body-analyses/private.jpg',
            'view_type' => 'front',
            'metrics' => json_encode(['posture_score' => 70]),
            'ai_summary' => json_encode(['summary' => 'Privada.']),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now(),
        ]);

        $this->actingAs($intruder)
            ->get(route('body-analysis.compare', [
                'id1' => $ownAnalysisId,
                'id2' => $privateAnalysisId,
            ]))
            ->assertNotFound();
    }

    public function test_body_analysis_requires_ai_body_photo_consent(): void
    {
        $this->withoutMiddleware();
        Notification::fake();
        Storage::fake('public');
        Storage::fake('local');

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));
        AiCreditWallet::create([
            'user_id' => $user->id,
            'balance' => 50,
            'monthly_allowance' => 50,
            'extra_credits' => 0,
        ]);

        $this->actingAs($user)->postJson(route('body-analysis.store'), [
            'image' => UploadedFile::fake()->image('valid.jpg', 900, 900),
            'view_type' => 'front',
        ])->assertStatus(409)
            ->assertJsonPath('code', 'ai_body_photo_analysis_consent_required');
    }

    public function test_body_analysis_index_renders(): void
    {
        $this->withoutMiddleware();
        Notification::fake();

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));

        $this->actingAs($user)
            ->get(route('body-analysis.index'))
            ->assertOk()
            ->assertSee('Cyber-Fit', false)
            ->assertSee('Pontos de atencao', false);
    }

    public function test_body_analysis_compare_renders_with_new_metrics(): void
    {
        $this->withoutMiddleware();
        Notification::fake();

        $user = User::withoutEvents(fn () => User::factory()->create(['is_premium' => true]));

        $firstId = DB::table('body_analyses')->insertGetId([
            'user_id' => $user->id,
            'photo_path' => 'body-analyses/first.jpg',
            'view_type' => 'front',
            'metrics' => json_encode([
                'posture_score' => 75,
                'asymmetry_shoulders' => 7,
                'asymmetry_hips' => 6,
                'head_forward_score' => 0,
                'landmark_confidence' => 0.7,
            ]),
            'ai_summary' => json_encode(['summary' => 'Primeira analise.']),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now()->subDay(),
        ]);

        $secondId = DB::table('body_analyses')->insertGetId([
            'user_id' => $user->id,
            'photo_path' => 'body-analyses/second.jpg',
            'view_type' => 'front',
            'metrics' => json_encode([
                'posture_score' => 88,
                'asymmetry_shoulders' => 3,
                'asymmetry_hips' => 2,
                'head_forward_score' => 0,
                'landmark_confidence' => 0.9,
            ]),
            'ai_summary' => json_encode(['summary' => 'Analise recente.']),
            'analysis_version' => 'mediapipe_pose_rules_v2',
            'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('body-analysis.compare', ['id1' => $firstId, 'id2' => $secondId]))
            ->assertOk()
            ->assertSee('Assimetria do Quadril', false)
            ->assertSee('Confianca dos Pontos', false);
    }
}
