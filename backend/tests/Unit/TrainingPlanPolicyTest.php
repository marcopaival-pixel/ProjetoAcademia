<?php

namespace Tests\Unit;

use App\Models\TrainingPlan;
use App\Models\User;
use App\Policies\TrainingPlanPolicy;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TrainingPlanPolicyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->unsignedBigInteger('academy_company_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->timestamps();
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('creator_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_without_impersonation_cannot_view_foreign_plan(): void
    {
        User::withoutEvents(fn () => User::create([
            'email' => 'admin@example.com',
            'name' => 'Admin',
            'password_hash' => 'hash',
            'is_admin' => true,
        ]));
        $admin = User::where('email', 'admin@example.com')->first();

        $owner = User::withoutEvents(fn () => User::create([
            'email' => 'owner@example.com',
            'name' => 'Owner',
            'password_hash' => 'hash',
            'academy_company_id' => 7,
        ]));

        $plan = TrainingPlan::create([
            'user_id' => $owner->id,
            'name' => 'Plano A',
        ]);

        $policy = new TrainingPlanPolicy;

        $this->assertFalse($policy->view($admin, $plan));
    }

    public function test_admin_with_impersonation_can_view_plan_in_tenant(): void
    {
        session(['impersonated_clinic_id' => 3, 'impersonated_company_id' => 7]);

        $admin = User::withoutEvents(fn () => User::create([
            'email' => 'admin2@example.com',
            'name' => 'Admin',
            'password_hash' => 'hash',
            'is_admin' => true,
        ]));

        $owner = User::withoutEvents(fn () => User::create([
            'email' => 'owner2@example.com',
            'name' => 'Owner',
            'password_hash' => 'hash',
            'academy_company_id' => 7,
            'clinic_id' => 3,
        ]));

        $plan = TrainingPlan::create([
            'user_id' => $owner->id,
            'name' => 'Plano B',
        ]);

        $policy = new TrainingPlanPolicy;

        $this->assertTrue($policy->view($admin, $plan));
    }

    public function test_owner_can_view_own_plan(): void
    {
        $user = User::withoutEvents(fn () => User::create([
            'email' => 'self@example.com',
            'name' => 'Self',
            'password_hash' => 'hash',
            'is_admin' => false,
        ]));
        $plan = TrainingPlan::create(['user_id' => $user->id, 'name' => 'Meu plano']);

        $policy = new TrainingPlanPolicy;

        $this->assertTrue($policy->view($user, $plan));
    }
}
