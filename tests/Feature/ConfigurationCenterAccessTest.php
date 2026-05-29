<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigurationCenterAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_configuration_center_requires_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $role = Role::where('name', 'finance')->firstOrFail();
        $user = User::factory()->create([
            'profile_id' => $role->id,
            'is_admin' => false,
        ]);
        $user->assignRole('finance');

        $this->actingAs($user)
            ->get(route('admin.configuration-center.dashboard'))
            ->assertForbidden();
    }

    public function test_administrator_can_access_configuration_center(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->get(route('admin.configuration-center.dashboard'))
            ->assertOk();
    }
}
