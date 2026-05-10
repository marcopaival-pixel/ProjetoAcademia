<?php

namespace App\Services;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DemoDataService
{
    /**
     * Prepara o ambiente de demonstração para um perfil específico.
     */
    public function setupDemoEnvironment(string $profile, ?User $user = null): User
    {
        $demoUser = $user ?? Auth::user();

        if (!$demoUser || !$demoUser->is_demo) {
            // Se não houver usuário, criar um temporário para a sessão
            $company = \App\Models\AcademyCompany::first();
            $demoUser = User::firstOrCreate(
                ['email' => 'demo@nexshape.com.br'],
                [
                    'name' => 'Usuário Demonstração',
                    'username' => 'demo_user',
                    'password_hash' => Hash::make('demo123'),
                    'is_demo' => true,
                    'is_premium' => true,
                    'academy_company_id' => $company ? $company->id : null,
                    'status' => 'active',
                    'email_verified' => true,
                    'plan_id' => 2, // Premium
                    'professional_plan_id' => 3, // Profissional Premium
                ]
            );
        }

        // Atribuir papel correto e remover o anterior para evitar conflito de redirecionamento
        if ($profile === 'aluno' || $profile === 'student') {
            $demoUser->assignRole('aluno');
            $demoUser->removeRole('professional');
            $demoUser->removeRole('manager');
            $demoUser->is_admin = false;
            $profile = 'student';
        } elseif ($profile === 'clinic' || $profile === 'gestor') {
            $demoUser->assignRole('manager');
            $demoUser->removeRole('aluno');
            $demoUser->removeRole('professional');
            $demoUser->is_admin = true; // Necessário para acessar rotas do painel admin/clínica
            $profile = 'clinic';

            // Garantir permissões críticas para demo gestor
            $permissions = Permission::whereIn('name', ['portal.access', 'admin.access'])->get();
            $demoUser->permissions()->syncWithoutDetaching($permissions->pluck('id'));

            // Limpar cache de permissões para que a mudança seja imediata
            Cache::forget("user_permissions_v2_{$demoUser->id}");
        } else {
            $demoUser->assignRole('professional');
            $demoUser->removeRole('aluno');
            $demoUser->removeRole('manager');
            $demoUser->is_admin = false;
            $profile = 'professional';
        }

        $demoUser->save();

        // Forçar is_admin via DB direto para evitar observers ou travas de modelo
        DB::table('users')
            ->where('id', $demoUser->id)
            ->update(['is_admin' => ($profile === 'clinic' ? 1 : 0)]);

        // Limpar cache global e específico para garantir leitura fresca
        Artisan::call('cache:clear');
        Cache::forget("user_permissions_v2_{$demoUser->id}");

        // Limpar dados anteriores de demo deste usuário para um "Fresh Start"
        $this->clearDemoData($demoUser);

        // 3. Gerar massa de dados baseada no perfil
        $this->generateMockData($demoUser, $profile);

        return $demoUser;
    }

    public function clearDemoData(User $user)
    {
        // Remove apenas dados vinculados ao usuário demo
        $user->foodEntries()->delete();
        $user->exerciseEntries()->delete();
        $user->weightEntries()->delete();
        $user->waterEntries()->delete();
        $user->trainingPlans()->delete();
    }

    private function generateMockData(User $user, $profile)
    {
        // Aqui você pode adicionar lógica para criar refeições, treinos, etc.
        // conforme o perfil desejado para a demonstração.
    }
}
