<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthTokenExpirationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->string('registration_approval_status')->nullable();
            $table->boolean('force_password_change')->default(false);
            $table->timestamp('temp_password_expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
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
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function test_api_token_response_includes_expiration(): void
    {
        config(['sanctum.token_expiration_days' => 30]);

        $user = User::withoutEvents(function () {
            $user = new User([
                'email' => 'mobile@example.com',
                'name' => 'Mobile User',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $user->setPlainPassword('Secret123!');
            $user->save();

            return $user;
        });

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'mobile@example.com',
            'password' => 'Secret123!',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token_type', 'access_token', 'expires_at', 'user']);

        $this->assertNotNull($response->json('expires_at'));
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'phpunit',
        ]);
    }

    public function test_authenticated_user_can_refresh_token(): void
    {
        config(['sanctum.token_expiration_days' => 15]);

        $user = User::withoutEvents(function () {
            $user = new User([
                'email' => 'refresh@example.com',
                'name' => 'Refresh User',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            $user->setPlainPassword('Secret123!');
            $user->save();

            return $user;
        });

        $issued = $user->createToken('old-device', ['*'], now()->addDays(15));
        $oldTokenId = $issued->accessToken->id;

        $response = $this->withHeader('Authorization', 'Bearer '.$issued->plainTextToken)
            ->postJson('/api/v1/auth/token/refresh', [
                'device_name' => 'refreshed',
            ]);

        $response->assertOk()->assertJsonPath('token_type', 'Bearer');

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $oldTokenId]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'refreshed',
        ]);
    }
}
