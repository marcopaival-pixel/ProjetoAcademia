<?php

namespace Tests\Feature;

use App\Models\AIOrchestratorLog;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PruneAiOrchestratorLogsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('ai_orchestrator_logs');
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
            $table->timestamps();
        });

        Schema::create('ai_orchestrator_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('model_name')->nullable();
            $table->text('user_message')->nullable();
            $table->text('ai_response')->nullable();
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('cost_usd', 10, 6)->default(0);
            $table->integer('execution_time_ms')->default(0);
            $table->string('status')->nullable();
            $table->json('context')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function test_prune_command_removes_old_orchestrator_logs(): void
    {
        $user = User::withoutEvents(fn () => User::factory()->create());

        $old = AIOrchestratorLog::create([
            'user_id' => $user->id,
            'agent_name' => 'support',
            'model_name' => 'gpt-4o-mini',
            'user_message' => 'mensagem antiga',
            'ai_response' => 'resposta antiga',
            'status' => 'success',
        ]);
        $old->forceFill([
            'created_at' => now()->subDays(120),
            'updated_at' => now()->subDays(120),
        ])->save();

        AIOrchestratorLog::create([
            'user_id' => $user->id,
            'agent_name' => 'support',
            'model_name' => 'gpt-4o-mini',
            'user_message' => 'mensagem recente',
            'ai_response' => 'resposta recente',
            'status' => 'success',
        ]);

        $this->artisan('ai:prune-orchestrator-logs', [
            '--days' => 90,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount('ai_orchestrator_logs', 1);
        $this->assertDatabaseHas('ai_orchestrator_logs', [
            'user_message' => 'mensagem recente',
        ]);
    }
}
