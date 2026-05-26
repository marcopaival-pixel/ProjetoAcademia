<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Permissões Básicas
        $permissions = [
            ['name' => 'admin.access', 'label' => 'Acesso ao Painel Admin'],
            ['name' => 'portal.access', 'label' => 'Acesso ao Portal Usuário'],
            ['name' => 'users.view', 'label' => 'Visualizar Usuários'],
            ['name' => 'users.create', 'label' => 'Cadastrar Usuários'],
            ['name' => 'users.edit', 'label' => 'Editar Usuários'],
            ['name' => 'users.delete', 'label' => 'Excluir Usuários'],
            ['name' => 'finance.access', 'label' => 'Acesso ao Financeiro'],
            ['name' => 'support.access', 'label' => 'Acesso ao Suporte'],
            ['name' => 'training.manage', 'label' => 'Gerenciar Treinos'],
            ['name' => 'reception.access', 'label' => 'Acesso à Recepção'],
            ['name' => 'pdf.templates.manage', 'label' => 'Gerir modelos de PDF'],
            ['name' => 'pdf.documents.generate', 'label' => 'Gerar documentos PDF'],
            ['name' => 'pdf.history.view', 'label' => 'Ver histórico de PDFs oficiais'],
            ['name' => 'pdf.document.cancel', 'label' => 'Cancelar documento PDF (validação)'],
            ['name' => 'pdf.document.sign', 'label' => 'Assinar documentos PDF'],
            ['name' => 'pdf.delivery.email', 'label' => 'Enviar / reenviar PDF por e-mail'],
            ['name' => 'pdf.delivery.whatsapp', 'label' => 'Enviar / reenviar PDF por WhatsApp'],
            ['name' => 'pdf.companies.manage', 'label' => 'Gerir empresas e unidades (PDF / multi-tenant)'],
            ['name' => 'pdf.integrations.view', 'label' => 'Ver integrações e configuração técnica PDF'],
            ['name' => 'configuration-center.access', 'label' => 'Centro de Configuração (CRUD dinâmico e auditoria)'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // 2. Criar Perfis (Roles)
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Administrador',
                'description' => 'Acesso total ao sistema',
                'permissions' => Permission::all()->pluck('id'),
            ],
            [
                'name' => 'manager',
                'label' => 'Administrador da Clínica',
                'description' => 'Gestão operacional, usuários e configurações da clínica',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'configuration-center.access',
                    'users.view',
                    'pdf.templates.manage',
                    'pdf.documents.generate',
                    'pdf.history.view',
                    'pdf.document.cancel',
                    'pdf.document.sign',
                    'pdf.delivery.email',
                    'pdf.delivery.whatsapp',
                    'pdf.companies.manage',
                    'pdf.integrations.view',
                ])->pluck('id'),
            ],
            [
                'name' => 'instructor',
                'label' => 'Instrutor',
                'description' => 'Treinos, alunos e emissão / assinatura de PDFs',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'portal.access',
                    'training.manage',
                    'users.view',
                    'pdf.documents.generate',
                    'pdf.document.sign',
                    'pdf.history.view',
                ])->pluck('id'),
            ],
            [
                'name' => 'professional',
                'label' => 'Profissional',
                'description' => 'Gerenciamento de pacientes, treinos e avaliações',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'portal.access',
                    'training.manage',
                    'users.view',
                    'pdf.documents.generate',
                    'pdf.document.sign',
                    'pdf.history.view',
                ])->pluck('id'),
            ],
            [
                'name' => 'aluno',
                'label' => 'Aluno',
                'description' => 'Perfil básico de acesso à plataforma',
                'permissions' => Permission::whereIn('name', ['portal.access'])->pluck('id'),
            ],
            [
                'name' => 'receptionist',
                'label' => 'Recepcionista / Atendente',
                'description' => 'Atendimento, cadastro de alunos e presença',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'portal.access',
                    'reception.access',
                    'users.view',
                    'users.create',
                    'pdf.documents.generate',
                    'pdf.history.view',
                    'pdf.delivery.email',
                    'pdf.delivery.whatsapp',
                ])->pluck('id'),
            ],
            [
                'name' => 'finance',
                'label' => 'Financeiro',
                'description' => 'Acesso a relatórios financeiros e cobranças',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'portal.access',
                    'finance.access',
                    'users.view',
                    'pdf.documents.generate',
                    'pdf.history.view',
                    'pdf.delivery.email',
                ])->pluck('id'),
            ],
            [
                'name' => 'supervisor',
                'label' => 'Supervisor',
                'description' => 'Supervisão operacional (alinhado ao gerente)',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'users.view',
                    'pdf.templates.manage',
                    'pdf.documents.generate',
                    'pdf.history.view',
                    'pdf.document.cancel',
                    'pdf.document.sign',
                    'pdf.delivery.email',
                    'pdf.delivery.whatsapp',
                    'pdf.companies.manage',
                    'pdf.integrations.view',
                ])->pluck('id'),
            ],
            [
                'name' => 'representative',
                'label' => 'Representante',
                'description' => 'Parceiro comercial com foco em indicação e comissões',
                'permissions' => Permission::whereIn('name', [
                    'admin.access',
                    'portal.access',
                ])->pluck('id'),
            ],
            [
                'name' => 'paciente',
                'label' => 'Paciente',
                'description' => 'Acesso ao portal do paciente e acompanhamento',
                'permissions' => Permission::whereIn('name', ['portal.access'])->pluck('id'),
            ],
            [
                'name' => 'des',
                'label' => 'DES',
                'description' => 'Perfil de Desenvolvedor / Suporte Técnico',
                'permissions' => Permission::all()->pluck('id'), // Full access like admin
            ],
        ];

        foreach ($roles as $r) {
            $rolePermissions = $r['permissions'];
            unset($r['permissions']);

            $role = Role::updateOrCreate(['name' => $r['name']], $r);
            $role->permissions()->sync($rolePermissions);
        }
    }
}
