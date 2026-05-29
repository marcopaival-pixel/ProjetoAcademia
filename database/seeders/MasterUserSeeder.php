<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MasterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'master@academia.com';

        // Verifica se o utilizador já existe para não criar duplicados
        $user = User::where('email', $email)->first();

        if (!$user) {
            User::create([
                'name' => 'Master Academia',
                'email' => $email,
                'password_hash' => Hash::make('master123'),
                'is_admin' => true,
                'is_premium' => true,
                'premium_expires_at' => Carbon::now()->addYears(50), // Vitalício
                'created_at' => Carbon::now(),
            ]);
        } else {
            // Se já existe, apenas garante que é Admin e Premium
            $user->update([
                'is_admin' => true,
                'is_premium' => true,
            ]);
        }
    }
}
