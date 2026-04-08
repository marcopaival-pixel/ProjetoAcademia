<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Exibe a gestão de faturamento e planos do profissional (SaaS Billing).
     */
    public function index(): View
    {
        // Simulando dados da assinatura atual do profissional
        $subscription = [
            'plan_name' => 'Pro Plan',
            'status' => 'active',
            'next_billing' => '2026-05-07',
            'amount' => 'R$ 197,00/mês',
            'card_last4' => '4242',
            'card_brand' => 'Visa',
        ];

        // Histórico de faturas
        $invoices = [
            ['id' => '#INV-8821', 'date' => '2026-04-07', 'amount' => 'R$ 197,00', 'status' => 'Paga'],
            ['id' => '#INV-7710', 'date' => '2026-03-07', 'amount' => 'R$ 197,00', 'status' => 'Paga'],
        ];

        // Opções de Planos (Para Upgrade)
        $plans = [
            [
                'name' => 'Starter', 
                'price' => '97', 
                'features' => ['Até 20 Pacientes', 'IA Limitada', 'Suporte via Chat'],
                'current' => false
            ],
            [
                'name' => 'Pro', 
                'price' => '197', 
                'features' => ['Pacientes Ilimitados', 'IA Full Access', 'White Label Completo'],
                'current' => true
            ],
            [
                'name' => 'Enterprise', 
                'price' => '497', 
                'features' => ['Multi-Clínicas', 'API Externas', 'Gerente de Contas'],
                'current' => false
            ],
        ];

        return view('professional.billing.index', compact('subscription', 'invoices', 'plans'));
    }
}
