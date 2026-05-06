<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfessionalAvailability;
use App\Models\Plan;
use App\Models\ConfiguracaoEmail;
use App\Models\AdminSetting;

class ProfessionalReadinessService
{
    /**
     * Verifica a prontidão do profissional para uso do sistema.
     * 
     * @param User $user
     * @return array
     */
    public function getReadinessStatus(User $user): array
    {
        $profile = $user->professionalProfile;

        $items = [
            'personal_data' => [
                'label' => 'Dados Pessoais',
                'description' => 'Nome, e-mail e telefone completos.',
                'status' => !empty($user->name) && !empty($user->email) && !empty($user->phone),
                'route' => route('professional.profile.edit'),
            ],
            'professional_data' => [
                'label' => 'Dados Profissionais',
                'description' => 'Especialidade e número de registro.',
                'status' => $profile && !empty($profile->specialty) && !empty($profile->registration_number),
                'route' => route('professional.profile.edit'),
            ],
            'payment_integration' => [
                'label' => 'Integração de Pagamento',
                'description' => 'Mercado Pago configurado e ativo.',
                'status' => (bool) AdminSetting::isTrue('pagamento_ativo', true) && !empty(config('projeto.mp_access_token')),
                'route' => '#', // Rota de config de pagamento se existisse para o profissional
            ],
            'agenda' => [
                'label' => 'Agenda de Atendimento',
                'description' => 'Configuração de horários de trabalho.',
                'status' => $user->availabilities()->exists(),
                'route' => route('professional.agenda.index'),
            ],
            'plans' => [
                'label' => 'Planos de Alunos',
                'description' => 'Pelo menos um plano ativo para venda.',
                'status' => Plan::where('type', 'student')->where('is_active', true)->exists(),
                'route' => route('plano'), // Ajustar se houver rota específica de gestão de planos student
            ],
            'email' => [
                'label' => 'Configuração de E-mail',
                'description' => 'Serviço de disparo de e-mails configurado.',
                'status' => ConfiguracaoEmail::where('empresa_id', $user->academy_company_id)->where('ativo', true)->exists(),
                'route' => route('professional.branding'), // Geralmente fica em branding/config
            ],
            'email_verified' => [
                'label' => 'E-mail Verificado',
                'description' => 'Confirmação de propriedade do e-mail.',
                'status' => $user->isEmailVerified(),
                'route' => '#',
            ],
        ];

        $completedCount = collect($items)->filter(fn($item) => $item['status'])->count();
        $totalCount = count($items);
        $percentage = round(($completedCount / $totalCount) * 100);

        return [
            'items' => $items,
            'percentage' => $percentage,
            'is_ready' => $percentage === 100,
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
        ];
    }
}
