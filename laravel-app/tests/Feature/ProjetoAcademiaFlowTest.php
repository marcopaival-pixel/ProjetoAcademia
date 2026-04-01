<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetoAcademiaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_app_name_for_guest(): void
    {
        $this->get('/')->assertOk()->assertSee('ProjetoAcademia', false);
    }

    public function test_dashboard_redirects_guest_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_register_creates_user_profile_and_reaches_dashboard(): void
    {
        $this->post('/register', [
            'name' => 'Usuário Teste',
            'email' => 'flow@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'flow@example.com']);
        $this->assertDatabaseHas('user_profiles', ['user_id' => User::where('email', 'flow@example.com')->first()->id]);

        $this->get('/dashboard')->assertOk()->assertSee('Hoje', false);
    }

    public function test_legacy_index_redirects_guest_to_home(): void
    {
        $this->get('/index.php')->assertRedirect('/');
    }

    public function test_legacy_index_redirects_authenticated_user_to_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/index.php')->assertRedirect('/dashboard');
    }

    public function test_legacy_logout_php_signs_out(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/logout.php')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_legacy_diary_php_same_as_diary_route(): void
    {
        $user = User::factory()->create();
        $date = now()->format('Y-m-d');
        $this->actingAs($user)->post('/diary.php', [
            'entry_date' => $date,
            'meal_type' => 'lunch',
            'food_name' => 'Feijão',
            'calories' => 180,
            'protein_g' => 9,
            'carbs_g' => 30,
            'fat_g' => 1,
        ]);

        $this->assertDatabaseHas('food_entries', [
            'user_id' => $user->id,
            'food_name' => 'Feijão',
            'entry_date' => $date,
        ]);
    }

    public function test_dashboard_add_water(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/dashboard', ['water_add' => 500])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('water_entries', [
            'user_id' => $user->id,
            'amount_ml' => 500,
        ]);
    }

    public function test_export_csv_forbidden_without_premium(): void
    {
        $user = User::factory()->create(['is_premium' => false]);
        $this->actingAs($user)->get(route('export', ['kind' => 'food']))->assertStatus(403);
    }

    public function test_mp_webhook_returns_503_when_token_missing(): void
    {
        config(['projeto.mp_access_token' => '']);

        $this->postJson('/mp/webhook', [])->assertStatus(503);
    }

    public function test_mp_webhook_legacy_path_also_excluded_from_csrf(): void
    {
        config(['projeto.mp_access_token' => '']);

        $this->post('/mp_webhook.php', [])->assertStatus(503);
    }
}
