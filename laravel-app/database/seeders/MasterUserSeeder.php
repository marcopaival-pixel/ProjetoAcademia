<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'master@academia.com';
        $plainPassword = (string) env('MASTER_USER_PASSWORD', '');
        $forcePasswordChange = $plainPassword === '';

        if ($forcePasswordChange) {
            $plainPassword = Str::password(16);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = new User([
                'name' => 'Master Academia',
                'email' => $email,
                'is_admin' => true,
                'is_premium' => true,
                'premium_expires_at' => Carbon::now()->addYears(50),
                'created_at' => Carbon::now(),
            ]);
            $user->password_hash = \Illuminate\Support\Facades\Hash::make($plainPassword);
            $user->save();
        } else {
            $user->update([
                'is_admin' => true,
                'is_premium' => true,
            ]);
        }
    }
}
