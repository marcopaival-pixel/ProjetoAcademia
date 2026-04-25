<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Core / Student Menus
            ['name' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'order' => 1, 'is_required' => true],
            ['name' => 'diary', 'label' => 'Diário', 'route' => 'nutrition.index', 'icon' => 'book', 'order' => 2, 'is_required' => true],
            ['name' => 'exercise', 'label' => 'Treinos', 'route' => 'exercise', 'icon' => 'activity', 'order' => 3, 'is_required' => true],
            ['name' => 'nutrition', 'label' => 'Nutrição', 'route' => 'nutrition.index', 'icon' => 'coffee', 'order' => 4, 'is_required' => true],
            ['name' => 'assessments', 'label' => 'Avaliações', 'route' => 'assessments.index', 'icon' => 'clipboard', 'order' => 5, 'is_required' => false],
            ['name' => 'weight', 'label' => 'Peso', 'route' => 'weight', 'icon' => 'scale', 'order' => 6, 'is_required' => true],
            ['name' => 'hydration', 'label' => 'Hidratação', 'route' => 'hydration.index', 'icon' => 'droplet', 'order' => 7, 'is_required' => false],
            ['name' => 'chat', 'label' => 'Chat IA', 'route' => 'chat.page', 'icon' => 'message-square', 'order' => 8, 'is_required' => false],
            ['name' => 'leaderboard', 'label' => 'Ranking', 'route' => 'leaderboard.index', 'icon' => 'award', 'order' => 9, 'is_required' => false],
            ['name' => 'active-rest', 'label' => 'Descanso Ativo', 'route' => 'active-rest.index', 'icon' => 'refresh-cw', 'order' => 10, 'is_required' => false],
            ['name' => 'messages', 'label' => 'Mensagens', 'route' => 'messages.index', 'icon' => 'mail', 'order' => 11, 'is_required' => false],
            ['name' => 'report', 'label' => 'Relatórios', 'route' => 'report', 'icon' => 'file-text', 'order' => 12, 'is_required' => false],
            ['name' => 'profile', 'label' => 'Perfil', 'route' => 'profile', 'icon' => 'user', 'order' => 13, 'is_required' => true],
            ['name' => 'plano', 'label' => 'Pagamentos', 'route' => 'plano', 'icon' => 'credit-card', 'order' => 14, 'is_required' => false],

            // Professional Menus
            ['name' => 'patients', 'label' => 'Pacientes', 'route' => 'professional.patients.index', 'icon' => 'users', 'order' => 20, 'is_required' => false],
            ['name' => 'calendar', 'label' => 'Agenda', 'route' => 'dashboard', 'icon' => 'calendar', 'order' => 21, 'is_required' => false], // Placeholder route
            
            // Receptionist Menus
            ['name' => 'user_registration', 'label' => 'Cadastro de Alunos', 'route' => 'admin.users.create', 'icon' => 'user-plus', 'order' => 30, 'is_required' => false],
            ['name' => 'presence', 'label' => 'Presença', 'route' => 'dashboard', 'icon' => 'check-square', 'order' => 31, 'is_required' => false], // Placeholder route
            
            // Financial Menus
            ['name' => 'billing', 'label' => 'Cobranças', 'route' => 'professional.billing.index', 'icon' => 'dollar-sign', 'order' => 40, 'is_required' => false],
            ['name' => 'financial_reports', 'label' => 'Relatórios Financeiros', 'route' => 'report', 'icon' => 'bar-chart-2', 'order' => 41, 'is_required' => false], // Placeholder route
            
            // Admin Menus
            ['name' => 'users', 'label' => 'Usuários', 'route' => 'admin.users', 'icon' => 'users', 'order' => 50, 'is_required' => false],
            ['name' => 'settings', 'label' => 'Configurações', 'route' => 'admin.settings', 'icon' => 'settings', 'order' => 51, 'is_required' => false],
            ['name' => 'finance_admin', 'label' => 'Financeiro', 'route' => 'admin.settings.payments', 'icon' => 'trello', 'order' => 52, 'is_required' => false],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['name' => $menu['name']], $menu);
        }
    }
}
