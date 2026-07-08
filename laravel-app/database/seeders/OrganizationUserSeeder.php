<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Migra vínculos legados (clinic_id / academy_company_id) para a tabela pivot organization_user.
 * Pode ser rodado com segurança múltiplas vezes (idempotente).
 */
class OrganizationUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Migrando vínculos legados para organization_user...');

        // 1. Vincular usuários com clinic_id
        User::whereNotNull('clinic_id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $org = Organization::where('type', 'clinic')
                    ->where('id', $user->clinic_id)
                    ->first();

                if (!$org) continue;

                $role = $user->hasRole(['professional', 'instructor', 'supervisor']) ? 'professional' : 'paciente';

                DB::table('organization_user')->updateOrInsert(
                    ['user_id' => $user->id, 'organization_id' => $org->id, 'role' => $role],
                    ['is_active' => true, 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]
                );
            }
        });

        // 2. Vincular usuários com academy_company_id
        User::whereNotNull('academy_company_id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $org = Organization::where('id', $user->academy_company_id)->first();

                if (!$org) continue;

                $role = $user->hasRole(['professional', 'instructor', 'supervisor']) ? 'professional' : 'paciente';

                DB::table('organization_user')->updateOrInsert(
                    ['user_id' => $user->id, 'organization_id' => $org->id, 'role' => $role],
                    ['is_active' => true, 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]
                );
            }
        });

        $total = DB::table('organization_user')->count();
        $this->command->info("Concluído! {$total} vínculos migrados para organization_user.");
    }
}
