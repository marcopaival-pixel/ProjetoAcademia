<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfessionalApiAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('professional_appointments');
        Schema::dropIfExists('body_assessments');
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('meal_templates');
        Schema::dropIfExists('health_alerts');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
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
            $table->string('service_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('body_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('professional_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('meal_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
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

        Role::create(['name' => 'professional', 'label' => 'Profissional']);
        Role::create(['name' => 'aluno', 'label' => 'Aluno']);
    }

    public function test_professional_can_access_dashboard(): void
    {
        $pro = User::withoutEvents(function () {
            $user = User::create(['email' => 'pro@test.com', 'name' => 'Pro', 'password_hash' => 'x']);
            $user->roles()->attach(Role::where('name', 'professional')->first()->id);

            return $user;
        });

        Sanctum::actingAs($pro);

        $response = $this->getJson('/api/v1/dashboard');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['metrics', 'today_appointments', 'next_appointments']]);
    }

    public function test_student_cannot_access_professional_dashboard(): void
    {
        $student = User::withoutEvents(function () {
            $user = User::create(['email' => 'student@test.com', 'name' => 'Student', 'password_hash' => 'x']);
            $user->roles()->attach(Role::where('name', 'aluno')->first()->id);

            return $user;
        });

        Sanctum::actingAs($student);

        $this->getJson('/api/v1/dashboard')->assertForbidden();
    }
}
