<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RepresentativeProfile;

class FixRepProfileSeeder extends Seeder
{
    public function run()
    {
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'representative');
        })->get();

        foreach ($users as $u) {
            if (!$u->representativeProfile) {
                RepresentativeProfile::create([
                    'user_id' => $u->id,
                    'code' => 'REP' . $u->id . rand(100, 999),
                    'commission_rate' => 15.00,
                    'max_discount_rate' => 10.00,
                    'code_expires_at' => now()->addYear()
                ]);
                echo 'Perfil criado para: ' . $u->email . PHP_EOL;
            }
        }
    }
}
