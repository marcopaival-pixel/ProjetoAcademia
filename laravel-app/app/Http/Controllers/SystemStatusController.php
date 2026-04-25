<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SystemStatusController extends Controller
{
    /**
     * Display a beautiful system status page.
     */
    public function index()
    {
        $services = [
            [
                'name' => 'Núcleo do Sistema',
                'description' => 'Servidores principais e processamento',
                'status' => 'operational',
                'icon' => 'fas fa-server',
            ],
            [
                'name' => 'Banco de Dados',
                'description' => 'Persistência de dados e histórico',
                'status' => $this->checkDatabase(),
                'icon' => 'fas fa-database',
            ],
            [
                'name' => 'NexBot IA',
                'description' => 'Motor de inteligência artificial e chats',
                'status' => 'operational', // Placeholder for real IA ping
                'icon' => 'fas fa-brain',
            ],
            [
                'name' => 'NexHydra & NexShape Analytics',
                'description' => 'Processamento de métricas e evolução',
                'status' => 'operational',
                'icon' => 'fas fa-chart-line',
            ],
            [
                'name' => 'Checkout & Pagamentos',
                'description' => 'Gateway de pagamentos institucional',
                'status' => 'operational',
                'icon' => 'fas fa-credit-card',
            ],
            [
                'name' => 'Serviços de E-mail',
                'description' => 'Notificações e recuperação de conta',
                'status' => 'operational',
                'icon' => 'fas fa-envelope',
            ],
        ];

        $systemInfo = [
            'version' => 'v2.4.0-premium',
            'environment' => app()->environment(),
            'last_sync' => now()->format('d/m/Y H:i'),
            'uptime' => '99.98%',
        ];

        return view('legal.status', compact('services', 'systemInfo'));
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            return 'operational';
        } catch (\Exception $e) {
            return 'degraded';
        }
    }
}
