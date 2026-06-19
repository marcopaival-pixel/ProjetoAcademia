<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_admin_login_redirects_to_admin_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertSame('admin', session('active_role'));
    }

    public function test_professional_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $user->assignRole('professional');

        $this->actingAs($user)
            ->withSession(['active_role' => 'professional'])
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_aluno_is_redirected_away_from_professional_panel(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $user->assignRole('aluno');

        $this->actingAs($user)
            ->withSession(['active_role' => 'aluno'])
            ->get(route('professional.dashboard'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_paciente_is_redirected_away_from_student_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
            'registration_approval_status' => 'approved',
        ]);
        $user->assignRole('paciente');

        $this->actingAs($user)
            ->withSession(['active_role' => 'paciente'])
            ->get(route('dashboard'))
            ->assertRedirect(route('patient.portal'));
    }
}
