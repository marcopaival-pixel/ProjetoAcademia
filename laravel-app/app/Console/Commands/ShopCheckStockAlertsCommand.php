<?php

namespace App\Console\Commands;

use App\Services\Shop\ShopStockAlertService;
use Illuminate\Console\Command;

class ShopCheckStockAlertsCommand extends Command
{
    protected $signature = 'app:shop:check-stock-alerts';

    protected $description = 'Notifica administradores sobre produtos do shopping com estoque baixo.';

    public function handle(ShopStockAlertService $service): int
    {
        $low = $service->lowStockProducts()->count();
        $notified = $service->notifyAdminsLowStock();

        if ($low === 0) {
            $this->info('Nenhum produto com estoque baixo.');

            return self::SUCCESS;
        }

        $this->info("{$low} produto(s) com estoque baixo; {$notified} admin(s) notificado(s).");

        return self::SUCCESS;
    }
}
