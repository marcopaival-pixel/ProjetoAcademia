<?php

namespace Tests\Feature;

use App\Models\BodyAssessment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssessmentApiStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('body_assessments');
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
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
        });

        Schema::create('body_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('professional_id')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->date('assessment_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->enum('created_by', ['patient', 'professional'])->default('patient');
            $table->timestamps();
        });
    }

    public function test_student_can_store_assessment_via_api_with_valid_enums(): void
    {
        $user = User::withoutEvents(function () {
            $user = new User(['name' => 'Aluno API', 'email' => 'aluno-api@test.local']);
            $user->password_hash = bcrypt('secret');
            $user->save();

            return $user;
        });
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/assessments', [
            'assessment_date' => '2026-08-03',
            'weight_kg' => 78.5,
        ])->assertCreated()
            ->assertJsonPath('data.weight_kg', 78.5);

        $this->assertDatabaseHas('body_assessments', [
            'user_id' => $user->id,
            'created_by' => 'patient',
            'status' => 'approved',
            'weight_kg' => 78.5,
        ]);
    }
}
