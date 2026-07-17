<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemErrorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $errors = [
            [
                'type' => 'integration',
                'message' => 'Connection timeout with SMTP server smtp.gmail.com',
                'stack_trace' => '#0 vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(521): ...',
                'user_id' => 1,
                'url' => '/admin/settings/test-email',
                'method' => 'POST',
                'ip' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0...',
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'system',
                'message' => 'Profile image upload exceeded max size for user 5',
                'stack_trace' => 'N/A',
                'user_id' => null,
                'url' => '/profile/upload',
                'method' => 'POST',
                'ip' => '192.168.1.5',
                'user_agent' => 'NexShape Mobile Client',
                'created_at' => now()->subDay(),
            ],
            [
                'type' => 'sql',
                'message' => 'Database migration failed: column already exists',
                'stack_trace' => 'SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name...',
                'user_id' => 1,
                'url' => 'artisan command',
                'method' => 'CLI',
                'ip' => '0.0.0.0',
                'user_agent' => 'Symfony Console',
                'created_at' => now()->subDays(5),
            ],
        ];

        foreach ($errors as $error) {
            DB::table('system_errors')->insert($error);
        }
    }
}
