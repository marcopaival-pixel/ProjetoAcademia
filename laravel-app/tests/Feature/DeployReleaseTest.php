<?php

namespace Tests\Feature;

use App\Models\DeployRelease;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployReleaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_deploy_panel(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->get(route('admin.deploy.index'))
            ->assertOk()
            ->assertSee('Deploy & Versões');
    }

    public function test_admin_can_register_production_release(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->post(route('admin.deploy.store'), [
                'version' => '1.0.1',
                'environment' => 'production',
                'status' => 'success',
                'impact_level' => 'low',
                'risk_level' => 'low',
                'git_branch' => 'main',
            ])
            ->assertRedirect(route('admin.deploy.index'));

        $this->assertDatabaseHas('deploy_releases', [
            'version' => '1.0.1',
            'environment' => 'production',
            'status' => 'success',
            'is_current' => 1,
        ]);
    }

    public function test_homolog_release_can_be_approved(): void
    {
        $admin = User::factory()->administrator()->create();

        $release = DeployRelease::create([
            'version' => '1.1.0',
            'environment' => DeployRelease::ENV_HOMOLOG,
            'status' => DeployRelease::STATUS_SUCCESS,
            'homolog_status' => DeployRelease::HOMOLOG_PENDING,
            'impact_level' => 'medium',
            'risk_level' => 'low',
            'deployed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.deploy.homolog', $release), [
                'homolog_status' => 'approved',
            ])
            ->assertRedirect();

        $this->assertSame('approved', $release->fresh()->homolog_status);
    }
}
