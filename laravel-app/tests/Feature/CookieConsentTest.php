<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_store_cookie_consent_in_session(): void
    {
        $response = $this->postJson(route('legal.cookie-consent'), [
            'analytics' => false,
            'marketing' => false,
            'preferences' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('preferences.analytics', false)
            ->assertJsonPath('preferences.preferences', true);

        $this->assertDatabaseCount('user_consents', 0);
        $this->assertEquals(
            ['schema' => '1.0', 'essential' => true, 'analytics' => false, 'marketing' => false, 'preferences' => true],
            session('cookie_consent')
        );
    }

    public function test_authenticated_user_persists_cookie_consent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('legal.cookie-consent'), [
            'analytics' => true,
            'marketing' => false,
            'preferences' => true,
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_consents', [
            'user_id' => $user->id,
            'consent_type' => 'cookies',
        ]);

        $consent = UserConsent::where('user_id', $user->id)->where('consent_type', 'cookies')->first();
        $this->assertNotNull($consent);
        $this->assertSame(
            ['schema' => '1.0', 'essential' => true, 'analytics' => true, 'marketing' => false, 'preferences' => true],
            json_decode($consent->version, true)
        );
    }

    public function test_unauthenticated_diary_legacy_route_is_blocked(): void
    {
        $this->post('/diary.php', [
            'entry_date' => now()->format('Y-m-d'),
            'meal_type' => 'lunch',
            'food_name' => 'Feijão',
            'calories' => 180,
        ])->assertRedirect(route('login'));
    }
}
