<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MasterUserSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $adminEmail = strtolower(trim((string) env('ADMIN_EMAIL', '')));
        if ($adminEmail !== '') {
            User::query()->whereRaw('LOWER(email) = ?', [$adminEmail])->update(['is_admin' => true]);
        }
    }
}
