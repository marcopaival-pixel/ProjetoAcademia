<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('system:monitor')->everyMinute();

// Database Backups
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('02:00');

// AI Credit Renewal
Schedule::command('ai:credits-renew')->daily()->at('00:01');

// AI Cost Audit & Alerts
Schedule::command('ai:cost-audit')->hourly();

// User Cleanup
Schedule::command('app:clean-unverified-users')->hourly();

// Log Maintenance (Manter banco leve - Prioridade 3 da Auditoria)
Schedule::command('app:purge-old-logs --days=15 --force')->dailyAt('03:00');
Schedule::command('app:purge-pulse --force')->dailyAt('03:15');

// Verificação de backups nativos vazios (auditoria BD)
Schedule::command('app:backup:verify --fail-on-empty')->weeklyOn(1, '04:30');

// Comissões: PENDENTE → DISPONIVEL após carência (available_at)
Schedule::command('commission:release')->hourly();

// Conciliação financeira (pagamentos vs logs)
Schedule::command('finance:reconcile --days=30 --stale=7')->dailyAt('04:00');

// Inadimplência: escalação de status e retentativas de cobrança
Schedule::command('financial:check-status')->dailyAt('05:00');
Schedule::command('subscription:process-retries')->dailyAt('06:00');

// LGPD: processamento automático de pedidos de exclusão após prazo legal (15 dias)
Schedule::command('app:lgpd:process-deletions --older-than-days=15')->dailyAt('07:00');

// Shopping: alertas de estoque baixo para administradores
Schedule::command('app:shop:check-stock-alerts')->dailyAt('08:00');

// Shopping: recálculo de recomendações (cache expirado)
Schedule::command('app:shop:refresh-recommendations')->dailyAt('09:00');

// System Health Heartbeat
Schedule::command('pulse:check')->everyMinute();
Schedule::command('pulse:work')->everyMinute(); // Garante que o Pulse processe dados se não houver worker dedicado
