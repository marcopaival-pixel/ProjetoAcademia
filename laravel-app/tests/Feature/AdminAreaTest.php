<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_redirects_guest_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_forbidden_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_dashboard_lists_users_for_administrator(): void
    {
        $admin = User::factory()->administrator()->create(['name' => 'Conta Admin']);
        User::factory()->create(['email' => 'outro@example.com', 'name' => 'Utilizador']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Conta Admin', false)
            ->assertSee('outro@example.com', false)
            ->assertSee('Administração', false);
    }

    public function test_admin_dashboard_includes_overview_counts(): void
    {
        $admin = User::factory()->administrator()->create();
        $logger = User::factory()->create();
        User::factory()->create(['is_premium' => true, 'premium_expires_at' => now()->addMonth()]);
        User::factory()->create(['is_premium' => true, 'premium_expires_at' => now()->subDay()]);

        DB::table('food_entries')->insert([
            'user_id' => $logger->id,
            'entry_date' => now()->toDateString(),
            'meal_type' => 'lunch',
            'food_name' => 'Teste',
            'calories' => 100,
            'protein_g' => 5,
            'carbs_g' => 10,
            'fat_g' => 2,
            'created_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('overview', function (array $o) {
                return $o['total_users'] === 4
                    && $o['administrators'] === 1
                    && $o['premium_subscriptions_active'] === 1
                    && $o['distinct_food_loggers_7d'] === 1
                    && $o['new_users_7d'] === 4;
            });
    }
}
