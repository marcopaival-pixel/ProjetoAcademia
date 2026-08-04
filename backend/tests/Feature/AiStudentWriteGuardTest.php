<?php

namespace Tests\Feature;

use App\Models\TrainingPlan;
use App\Models\User;
use App\Services\AgentActionDispatcher;
use App\Support\AiStudentWriteGuard;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AiStudentWriteGuardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('professional_id')->nullable();
            $table->string('name')->nullable();
            $table->boolean('created_by_ai')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('action')->nullable();
            $table->text('payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function test_writes_disabled_blocks_dispatcher(): void
    {
        Config::set('services.ai.student_module_writes', false);

        $user = User::withoutEvents(fn () => User::create([
            'email' => 'student@example.com',
            'name' => 'Student',
            'password_hash' => 'hash',
        ]));

        $result = app(AgentActionDispatcher::class)->dispatch($user, [
            'acao' => 'criar_treino',
            'dados' => ['name' => 'Test'],
        ]);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('desativadas', $result['error']);
    }

    public function test_prescribed_plan_cannot_be_adjusted_by_ai(): void
    {
        Config::set('services.ai.student_module_writes', true);
        Config::set('services.ai.block_ai_modify_prescribed_plans', true);

        $student = User::withoutEvents(fn () => User::create([
            'email' => 'aluno@example.com',
            'name' => 'Aluno',
            'password_hash' => 'hash',
        ]));

        $professional = User::withoutEvents(fn () => User::create([
            'email' => 'pro@example.com',
            'name' => 'Pro',
            'password_hash' => 'hash',
        ]));

        $plan = TrainingPlan::create([
            'user_id' => $student->id,
            'creator_id' => $professional->id,
            'name' => 'Ficha Pro',
        ]);

        $this->assertTrue(AiStudentWriteGuard::isPrescribedByProfessional($plan));

        $result = app(AgentActionDispatcher::class)->dispatch($student, [
            'acao' => 'ajustar_treino',
            'dados' => ['plan_id' => $plan->id],
        ]);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('profissional', strtolower($result['error']));
    }

    public function test_diet_write_actions_are_blocked_with_clear_message(): void
    {
        Config::set('services.ai.student_module_writes', true);

        $user = User::withoutEvents(fn () => User::create([
            'email' => 'nutri@example.com',
            'name' => 'Aluno',
            'password_hash' => 'hash',
        ]));

        $result = app(AgentActionDispatcher::class)->dispatch($user, [
            'acao' => 'criar_dieta',
            'dados' => [],
        ]);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('nao grava dietas', strtolower($result['error']));
    }
}
