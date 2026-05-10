<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\PaymentWebhookLog;
use App\Models\AdminSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestEnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Usuário de Teste (Admin)
        User::updateOrCreate(
            ['email' => 'teste@nexshape.com.br'],
            [
                'name' => 'Usuário Beta Tester',
                'password_hash' => Hash::make('Mudar@123'),
                'is_admin' => true,
                'status' => 'active',
            ]
        );

        // 2. Criar Logs de Webhook para inspeção
        PaymentWebhookLog::create([
            'gateway' => 'mercadopago',
            'event_type' => 'payment.created',
            'external_id' => 'mp_778899',
            'payload' => [
                'action' => 'payment.created',
                'data' => ['id' => '778899'],
                'user_id' => '123456789'
            ],
            'headers' => [
                'user-agent' => 'MercadoPago-Webhook/1.0',
                'x-signature' => 'abc123def456'
            ],
            'status_code' => 200,
            'status_message' => 'OK',
            'processing_time' => 0.1450,
            'ip_address' => '127.0.0.1'
        ]);

        PaymentWebhookLog::create([
            'gateway' => 'asaas',
            'event_type' => 'PAYMENT_RECEIVED',
            'external_id' => 'pay_554433',
            'payload' => [
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_554433',
                    'customer' => 'cus_001',
                    'value' => 149.90
                ]
            ],
            'headers' => [
                'host' => 'nexshape.com.br',
                'content-type' => 'application/json'
            ],
            'status_code' => 200,
            'status_message' => 'OK',
            'processing_time' => 0.0890,
            'ip_address' => '127.0.0.1'
        ]);

        PaymentWebhookLog::create([
            'gateway' => 'stripe',
            'event_type' => 'checkout.session.completed',
            'external_id' => 'cs_test_9988',
            'payload' => ['error' => 'Malformed JSON simulation'],
            'status_code' => 500,
            'status_message' => 'Internal Server Error',
            'error' => 'Syntax error: unexpected token { in JSON at position 45',
            'processing_time' => 0.0120,
            'ip_address' => '127.0.0.1'
        ]);

        // 3. Garantir configurações de segurança dinâmicas
        AdminSetting::set('password_min_length', '8');
        AdminSetting::set('password_require_uppercase', 'true');
        AdminSetting::set('password_require_numeric', 'true');
        AdminSetting::set('password_require_special', 'true');
        
        // 4. Configurações de Ambiente
        AdminSetting::set('app_debug', 'true');
        AdminSetting::set('app_url', 'http://localhost');
    }
}
