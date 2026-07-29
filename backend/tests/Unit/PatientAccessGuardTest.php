<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientAccessGuardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_admin_without_impersonation_cannot_access_student_data(): void
    {
        $admin = User::withoutEvents(fn () => User::create([
            'email' => 'admin@example.com',
            'name' => 'Admin',
            'password_hash' => 'hash',
            'is_admin' => true,
        ]));

        $student = User::withoutEvents(fn () => User::create([
            'email' => 'student@example.com',
            'name' => 'Student',
            'password_hash' => 'hash',
            'academy_company_id' => 10,
            'clinic_id' => 5,
        ]));

        $this->assertFalse(PatientAccessGuard::canAccessStudentData($admin, $student->id));

        $this->expectException(AuthorizationException::class);
        PatientAccessGuard::assertStudentDataAccess($admin, $student->id);
    }

    public function test_admin_with_impersonation_can_access_student_in_tenant(): void
    {
        session(['impersonated_clinic_id' => 5, 'impersonated_company_id' => 10]);

        $admin = User::withoutEvents(fn () => User::create([
            'email' => 'admin2@example.com',
            'name' => 'Admin',
            'password_hash' => 'hash',
            'is_admin' => true,
        ]));

        $student = User::withoutEvents(fn () => User::create([
            'email' => 'student2@example.com',
            'name' => 'Student',
            'password_hash' => 'hash',
            'academy_company_id' => 10,
            'clinic_id' => 5,
        ]));

        $this->assertTrue(PatientAccessGuard::canAccessStudentData($admin, $student->id));
    }

    public function test_student_can_access_own_data(): void
    {
        $user = User::withoutEvents(fn () => User::create([
            'email' => 'self@example.com',
            'name' => 'Self',
            'password_hash' => 'hash',
            'is_admin' => false,
        ]));

        $this->assertTrue(PatientAccessGuard::canAccessStudentData($user, $user->id));
    }
}
