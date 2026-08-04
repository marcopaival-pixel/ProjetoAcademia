<?php

namespace Tests\Feature;

use App\Models\BodyAssessment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssessmentDownloadPdfTest extends TestCase
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
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->date('assessment_date');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function test_download_pdf_finds_assessment_by_user_id_not_patient_id(): void
    {
        $user = User::withoutEvents(function () {
            $user = new User([
                'name' => 'Aluno Teste',
                'email' => 'aluno-pdf@test.local',
            ]);
            $user->password_hash = bcrypt('secret');
            $user->save();

            return $user;
        });
        Sanctum::actingAs($user);

        $assessment = BodyAssessment::create([
            'user_id' => $user->id,
            'weight_kg' => 75,
            'assessment_date' => now()->toDateString(),
        ]);

        $this->getJson("/api/v1/student/assessments/{$assessment->id}/pdf")
            ->assertNotFound()
            ->assertJsonPath('message', 'Nenhum PDF associado a esta avaliação.');
    }

    public function test_download_pdf_returns_not_found_for_other_users_assessment(): void
    {
        $owner = User::withoutEvents(function () {
            $user = new User(['name' => 'Dono', 'email' => 'owner-pdf@test.local']);
            $user->password_hash = bcrypt('secret');
            $user->save();

            return $user;
        });
        $other = User::withoutEvents(function () {
            $user = new User(['name' => 'Outro', 'email' => 'other-pdf@test.local']);
            $user->password_hash = bcrypt('secret');
            $user->save();

            return $user;
        });
        Sanctum::actingAs($other);

        $assessment = BodyAssessment::create([
            'user_id' => $owner->id,
            'weight_kg' => 80,
            'assessment_date' => now()->toDateString(),
        ]);

        $this->getJson("/api/v1/student/assessments/{$assessment->id}/pdf")
            ->assertNotFound()
            ->assertJsonPath('message', 'Avaliação não encontrada.');
    }
}
