<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentApiAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('exercise_entries');
        Schema::dropIfExists('water_entries');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('user_plans');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('training_plan_exercises');
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('professional_appointments');
        Schema::dropIfExists('health_alerts');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->timestamp('premium_expires_at')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->date('start_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('professional_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('training_plan_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_plan_id');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->integer('water_target_ml')->nullable();
            $table->boolean('is_water_target_auto')->default(false);
            $table->timestamps();
        });

        Schema::create('exercise_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('entry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('water_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->date('entry_date');
            $table->timestamp('drank_at')->nullable();
            $table->integer('amount_ml');
            $table->string('source')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('profissional_id');
            $table->string('status')->default('Sim');
            $table->timestamps();
        });

        Schema::create('professional_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professional_id');
            $table->unsignedInteger('patient_id')->nullable();
            $table->timestamp('appointment_at')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('health_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('type')->nullable();
            $table->string('severity')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        $portalAccess = Permission::create(['name' => 'portal.access', 'label' => 'Portal']);

        $studentRole = Role::create(['name' => 'aluno', 'label' => 'Aluno']);
        $studentRole->permissions()->attach($portalAccess->id);

        Role::create(['name' => 'professional', 'label' => 'Profissional']);
    }

    public function test_health_endpoint_is_public(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_unauthenticated_user_cannot_access_me(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_student_can_access_me_profile(): void
    {
        $student = $this->makeStudent('student@test.com');

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'student@test.com')
            ->assertJsonPath('data.is_student', true)
            ->assertJsonPath('data.is_professional', false);
    }

    public function test_student_can_list_own_training_plans(): void
    {
        $student = $this->makeStudent('plans@test.com');

        TrainingPlan::create([
            'user_id' => $student->id,
            'name' => 'Treino QA',
            'is_active' => true,
            'status' => 'active',
        ]);

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/training-plans')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Treino QA');
    }

    public function test_student_cannot_view_other_students_training_plan(): void
    {
        $student = $this->makeStudent('viewer@test.com');
        $other = $this->makeStudent('owner@test.com');

        $foreignPlan = TrainingPlan::create([
            'user_id' => $other->id,
            'name' => 'Plano privado',
            'is_active' => true,
            'status' => 'active',
        ]);

        Sanctum::actingAs($student);

        $this->getJson("/api/v1/training-plans/{$foreignPlan->id}")->assertForbidden();
    }

    public function test_student_cannot_access_professional_patients(): void
    {
        $student = $this->makeStudent('blocked@test.com');

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/professional/patients')
            ->assertForbidden()
            ->assertJsonPath('message', 'Acesso restrito a profissionais.');
    }

    public function test_student_cannot_access_professional_alerts(): void
    {
        $student = $this->makeStudent('alerts@test.com');

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/professional/alerts')->assertForbidden();
    }

    public function test_student_can_read_hydration_status(): void
    {
        $student = $this->makeStudent('hydra@test.com');

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/hydration/status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['date', 'target_ml', 'consumed_ml', 'percentage', 'entries'],
            ]);
    }

    private function makeStudent(string $email): User
    {
        return User::withoutEvents(function () use ($email) {
            $user = User::create([
                'email' => $email,
                'name' => 'Student QA',
                'password_hash' => 'hash',
                'is_premium' => false,
                'status' => 'active',
            ]);
            $user->roles()->attach(Role::where('name', 'aluno')->first()->id);

            return $user;
        });
    }
}
