<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Regista entradas do painel administrativo na tabela menus (portal=admin)
 * para permissões, middleware e sidebar.
 */
class AdminPortalMenusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'admin_nav_dashboard', 'label' => 'Visão Geral', 'route' => 'admin.dashboard', 'match_mode' => 'exact', 'order' => 10],
            ['name' => 'admin_nav_users', 'label' => 'Gestão de Usuários', 'route' => 'admin.users*', 'match_mode' => 'pattern', 'order' => 20],
            ['name' => 'admin_nav_registrations', 'label' => 'Cadastros pendentes', 'route' => 'admin.registrations.*', 'match_mode' => 'pattern', 'order' => 30],
            ['name' => 'admin_nav_roles', 'label' => 'Perfis e Permissões', 'route' => 'admin.roles.*', 'match_mode' => 'pattern', 'order' => 40],
            ['name' => 'admin_nav_plans', 'label' => 'Planos de Assinatura', 'route' => 'admin.plans.*', 'match_mode' => 'pattern', 'order' => 50],
            ['name' => 'admin_nav_settings', 'label' => 'Configurações Gerais', 'route' => 'admin.settings|admin.settings.store', 'match_mode' => 'exact', 'order' => 60],
            ['name' => 'admin_nav_settings_payments', 'label' => 'Config. de Pagamentos', 'route' => 'admin.settings.payments*', 'match_mode' => 'pattern', 'order' => 70],
            ['name' => 'admin_nav_email', 'label' => 'Config. E-mail', 'route' => 'admin.settings.email.*', 'match_mode' => 'pattern', 'order' => 80],
            ['name' => 'admin_nav_api_integrations', 'label' => 'APIs Externas', 'route' => 'admin.api-integrations.*', 'match_mode' => 'pattern', 'order' => 90],
            ['name' => 'admin_nav_coupons', 'label' => 'Liberação de Cupons', 'route' => 'admin.coupons.*', 'match_mode' => 'pattern', 'order' => 100],
            ['name' => 'admin_nav_pdf_suite', 'label' => 'Documentos PDF', 'route' => 'admin.pdf*', 'match_mode' => 'pattern', 'order' => 110],
            ['name' => 'admin_nav_security', 'label' => 'Segurança', 'route' => 'admin.security.*', 'match_mode' => 'pattern', 'order' => 120],
            ['name' => 'admin_nav_commercial', 'label' => 'Dash Comercial', 'route' => 'admin.commercial.dashboard', 'match_mode' => 'exact', 'order' => 200],
            ['name' => 'admin_nav_leads', 'label' => 'Gestão de Leads', 'route' => 'admin.leads.*', 'match_mode' => 'pattern', 'order' => 210],
            ['name' => 'admin_nav_proposals', 'label' => 'Propostas', 'route' => 'admin.proposals.*', 'match_mode' => 'pattern', 'order' => 220],
            ['name' => 'admin_nav_goals', 'label' => 'Metas e Performance', 'route' => 'admin.goals.*', 'match_mode' => 'pattern', 'order' => 230],
            ['name' => 'admin_nav_cs', 'label' => 'Saúde do Cliente (CS)', 'route' => 'admin.cs.*', 'match_mode' => 'pattern', 'order' => 300],
            ['name' => 'admin_nav_support', 'label' => 'Tickets de Suporte', 'route' => 'admin.support.*', 'match_mode' => 'pattern', 'order' => 310],
            ['name' => 'admin_nav_training', 'label' => 'Gestão da Academia', 'route' => 'admin.training.*', 'match_mode' => 'pattern', 'order' => 320],
            ['name' => 'admin_nav_exercises', 'label' => 'Catálogo de Exercícios', 'route' => 'admin.exercises.*', 'match_mode' => 'pattern', 'order' => 400],
            ['name' => 'admin_nav_announcements', 'label' => 'Comunicados / Avisos', 'route' => 'admin.announcements*', 'match_mode' => 'pattern', 'order' => 410],
            ['name' => 'admin_nav_backups', 'label' => 'Backup', 'route' => 'admin.backups.*', 'match_mode' => 'pattern', 'order' => 495],
            ['name' => 'admin_nav_deploy', 'label' => 'Deploy', 'route' => 'admin.deploy.*', 'match_mode' => 'pattern', 'order' => 496],
            ['name' => 'admin_nav_system_errors', 'label' => 'Logs de Erros', 'route' => 'admin.system-errors*', 'match_mode' => 'pattern', 'order' => 500],
            ['name' => 'admin_nav_ai', 'label' => 'Monitoramento IA', 'route' => 'admin.ai.monitoring', 'match_mode' => 'exact', 'order' => 510],
            ['name' => 'admin_nav_lgpd', 'label' => 'Privacidade / LGPD', 'route' => 'admin.lgpd.*', 'match_mode' => 'pattern', 'order' => 520],
            ['name' => 'admin_nav_omnichannel', 'label' => 'OmniChannel', 'route' => 'admin.omnichannel*', 'match_mode' => 'pattern', 'order' => 530],
        ];

        foreach ($rows as $row) {
            Menu::updateOrCreate(
                ['name' => $row['name']],
                [
                    'label' => $row['label'],
                    'route' => $row['route'],
                    'match_mode' => $row['match_mode'],
                    'icon' => null,
                    'order' => $row['order'],
                    'is_required' => false,
                    'parent_id' => null,
                    'portal' => 'admin',
                    'is_container' => false,
                ]
            );
        }
    }
}
