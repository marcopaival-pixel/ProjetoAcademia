<?php

namespace Tests\Feature;

use App\Models\FoodEntry;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use App\Services\StudentContextService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentContextServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('food_entries');
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('workout_sessions');
        Schema::dropIfExists('water_entries');
        Schema::dropIfExists('weight_entries');
        Schema::dropIfExists('body_assessments');
        Schema::dropIfExists('user_profiles');
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
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->date('birth_date')->nullable();
            $table->string('sex', 1)->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->string('activity_level')->nullable();
            $table->string('climate')->nullable();
            $table->string('goal')->nullable();
            $table->unsignedInteger('daily_calorie_target')->nullable();
            $table->decimal('target_weight_kg', 8, 2)->nullable();
            $table->boolean('has_injury')->default(false);
            $table->text('injury_details')->nullable();
            $table->boolean('has_allergy')->default(false);
            $table->text('allergy_details')->nullable();
            $table->string('physical_level')->nullable();
            $table->string('experience_level')->nullable();
            $table->string('training_location')->nullable();
            $table->unsignedTinyInteger('training_days_per_week')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('weight_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->decimal('weight_kg', 8, 2);
            $table->date('weighed_at');
            $table->timestamps();
        });

        Schema::create('food_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('entry_date');
            $table->string('food_name')->nullable();
            $table->unsignedInteger('calories')->default(0);
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('carbs_g', 8, 2)->default(0);
            $table->decimal('fat_g', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->string('frequency')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('training_plan_id')->nullable();
            $table->timestamps();
        });

        Schema::create('water_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('amount_ml');
            $table->timestamps();
        });

        Schema::create('body_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('assessment_date');
            $table->decimal('bf_percent', 8, 2)->nullable();
            $table->decimal('muscle_mass_kg', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('smart_stacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->string('goal')->nullable();
            $table->string('status')->default('ativo');
            $table->timestamps();
        });

        Schema::create('supplements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('smart_stack_id');
            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('unit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_metrics_include_profile_health_and_nutrition_totals(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'email' => 'aluno@test.com',
            'name' => 'Maria',
            'is_premium' => true,
        ]));

        UserProfile::create([
            'user_id' => $user->id,
            'birth_date' => '1995-05-10',
            'sex' => 'F',
            'height_cm' => 165,
            'activity_level' => 'moderate',
            'goal' => 'lose',
            'has_injury' => true,
            'injury_details' => 'Desconforto no joelho direito',
            'has_allergy' => true,
            'allergy_details' => 'Lactose',
            'physical_level' => 'intermediario',
            'training_location' => 'academia',
            'training_days_per_week' => 4,
        ]);

        WeightEntry::create([
            'user_id' => $user->id,
            'weight_kg' => 68.5,
            'weighed_at' => now()->toDateString(),
        ]);

        FoodEntry::create([
            'user_id' => $user->id,
            'entry_date' => now()->toDateString(),
            'food_name' => 'Frango',
            'calories' => 450,
            'protein_g' => 40,
            'carbs_g' => 10,
            'fat_g' => 8,
        ]);

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Upper/Lower',
            'frequency' => '4x/semana',
            'is_active' => true,
        ]);

        $service = app(StudentContextService::class);
        $metrics = $service->metrics($user);

        $this->assertSame('Maria', $metrics['name']);
        $this->assertSame('lose', $metrics['objective']);
        $this->assertSame(68.5, $metrics['current_weight_kg']);
        $this->assertSame(450, $metrics['consumed_calories_today']);
        $this->assertSame('sim', $metrics['has_injury']);
        $this->assertSame('Desconforto no joelho direito', $metrics['injury_details']);
        $this->assertSame('Lactose', $metrics['allergy_details']);
        $this->assertSame('Upper/Lower', $metrics['active_training_plan']);
        $this->assertArrayHasKey('remaining_calories_today', $metrics);
    }

    public function test_prompt_block_includes_injury_for_training_focus(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'email' => 'treino@test.com',
            'name' => 'Joao',
        ]));

        UserProfile::create([
            'user_id' => $user->id,
            'goal' => 'gain',
            'has_injury' => true,
            'injury_details' => 'Ombro sensivel',
            'physical_level' => 'iniciante',
        ]);

        $prompt = app(StudentContextService::class)->promptBlock($user, 'training');

        $this->assertStringContainsString('nao invente valores', $prompt);
        $this->assertStringContainsString('Ombro sensivel', $prompt);
        $this->assertStringContainsString('Joao', $prompt);
    }

    public function test_active_supplements_summary_lists_active_stack_items(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create([
            'email' => 'stack@test.com',
            'name' => 'Ana',
        ]));

        $stack = \App\Models\SmartStack::create([
            'user_id' => $user->id,
            'name' => 'Stack Base',
            'goal' => 'performance',
            'status' => 'ativo',
        ]);

        \App\Models\Supplement::create([
            'user_id' => $user->id,
            'smart_stack_id' => $stack->id,
            'name' => 'Creatina',
            'dosage' => '5',
            'unit' => 'g',
            'is_active' => true,
        ]);

        $summary = app(StudentContextService::class)->activeSupplementsSummary($user);

        $this->assertStringContainsString('Creatina (5 g)', $summary);
    }

    public function test_resolve_query_intent_detects_nutrition_and_training(): void
    {
        $service = app(StudentContextService::class);

        $this->assertSame('nutrition', $service->resolveQueryIntent('NUTRICAO', 'GERAL'));
        $this->assertSame('training', $service->resolveQueryIntent('TREINO', 'PERIODIZACAO'));
        $this->assertNull($service->resolveQueryIntent('GERAL', 'GERAL'));
    }
}
