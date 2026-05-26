<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\User;
use App\Support\TenantContext;
use Database\Seeders\RolesAndPermissionsSeeder;

trait SeedsRbacForTests
{
    protected function seedRbac(): void
    {
        TenantContext::set(null);
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    protected function userWithRole(string $roleName, array $userOverrides = []): User
    {
        $this->seedRbac();

        $role = Role::query()->where('name', $roleName)->firstOrFail();

        $user = User::factory()->create(array_merge([
            'profile_id' => $role->id,
            'clinic_id' => null,
            'academy_company_id' => null,
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ], $userOverrides));

        $user->assignRole($roleName);

        return $user;
    }
}
