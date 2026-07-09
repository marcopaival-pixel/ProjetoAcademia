<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Models\FoodEntry;
use App\Models\TrainingPlan;
use App\Models\WaterEntry;
use App\Models\WeightEntry;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RoleMenuPermissionDefaultsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        SystemSetting::query()->where('key', 'verificacao_email_ativa')->update(['value' => 'false']);
    }

    public function test_professional_demo_starts_and_opens_professional_dashboard(): void
    {
        $this
            ->get(route('demo.start', ['profile' => 'professional']))
            ->assertRedirect(route('professional.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->is_demo);
        $this->assertFalse(auth()->user()->force_password_change);
        $this->assertTrue(auth()->user()->hasRole('professional'));
        $this->assertFalse(auth()->user()->hasRole('aluno'));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'professional', 'active_role' => 'professional'])
            ->get(route('professional.dashboard'))
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => 'aluno_demo@nexshape.com.br',
            'is_demo' => true,
            'force_password_change' => false,
        ]);

        $patient = User::where('email', 'aluno_demo@nexshape.com.br')->firstOrFail();
        $this->assertGreaterThanOrEqual(4, FoodEntry::where('user_id', $patient->id)->count());
        $this->assertGreaterThanOrEqual(1, TrainingPlan::where('user_id', $patient->id)->count());
    }

    public function test_student_demo_starts_and_opens_student_dashboard(): void
    {
        $this
            ->get(route('demo.start', ['profile' => 'aluno']))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->is_demo);
        $this->assertFalse(auth()->user()->force_password_change);
        $this->assertTrue(auth()->user()->hasRole('aluno'));
        $this->assertFalse(auth()->user()->hasRole('professional'));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'aluno', 'active_role' => 'aluno'])
            ->get(route('dashboard'))
            ->assertOk();

        $user = User::where('email', 'demo@nexshape.com.br')->firstOrFail();
        $this->assertGreaterThanOrEqual(4, FoodEntry::where('user_id', $user->id)->count());
        $this->assertGreaterThanOrEqual(4, WaterEntry::where('user_id', $user->id)->count());
        $this->assertGreaterThanOrEqual(4, WeightEntry::where('user_id', $user->id)->count());
    }

    public function test_student_demo_dashboard_ignores_blocked_dashboard_placeholder_menu(): void
    {
        $this->seed(MenuSeeder::class);
        $this->seed(RoleMenuPermissionDefaultsSeeder::class);

        $studentRole = \App\Models\Role::where('name', 'aluno')->firstOrFail();
        $dashboardMenu = \App\Models\Menu::where('name', 'dashboard')->firstOrFail();
        $calendarMenu = \App\Models\Menu::where('name', 'calendar')->firstOrFail();

        \App\Models\RoleMenuPermission::updateOrCreate(
            ['role_id' => $studentRole->id, 'menu_id' => $dashboardMenu->id, 'academy_company_id' => null],
            ['pode_visualizar' => true]
        );
        \App\Models\RoleMenuPermission::updateOrCreate(
            ['role_id' => $studentRole->id, 'menu_id' => $calendarMenu->id, 'academy_company_id' => null],
            ['pode_visualizar' => false]
        );

        app(\App\Services\MenuAccessService::class)->bumpPermissionCacheVersion();

        $this
            ->get(route('demo.start', ['profile' => 'aluno']))
            ->assertRedirect(route('dashboard'));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'aluno', 'active_role' => 'aluno'])
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_demo_session_bypasses_portal_permission_gate(): void
    {
        $this->seed(MenuSeeder::class);
        $this->seed(RoleMenuPermissionDefaultsSeeder::class);

        $this->get(route('demo.start', ['profile' => 'aluno']));

        $user = User::where('email', 'demo@nexshape.com.br')->firstOrFail();
        $user->roles()->detach();
        \Illuminate\Support\Facades\Cache::forget("user_permissions_v2_{$user->id}");

        $this
            ->actingAs($user->fresh())
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'aluno', 'active_role' => 'aluno'])
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_demo_can_switch_between_student_and_professional(): void
    {
        $this->get(route('demo.start', ['profile' => 'professional']));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'professional', 'active_role' => 'professional'])
            ->post(route('demo.switch'), ['profile' => 'aluno'])
            ->assertRedirect(route('dashboard'));

        $user = User::where('email', 'demo@nexshape.com.br')->firstOrFail();
        $this->assertTrue($user->hasRole('aluno'));
        $this->assertFalse($user->hasRole('professional'));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'aluno', 'active_role' => 'aluno'])
            ->post(route('demo.switch'), ['profile' => 'professional'])
            ->assertRedirect(route('professional.dashboard'));

        $user->refresh();
        $this->assertTrue($user->hasRole('professional'));
        $this->assertFalse($user->hasRole('aluno'));
    }

    public function test_demo_can_stop_and_return_to_public_home(): void
    {
        $this->get(route('demo.start', ['profile' => 'aluno']));

        $this
            ->withSession(['is_demo_mode' => true, 'demo_profile' => 'aluno'])
            ->get(route('demo.stop'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
