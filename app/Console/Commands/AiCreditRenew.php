<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AiCreditRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:credits-renew';

    protected $description = 'Renova os créditos mensais de IA dos usuários ativos';

    public function handle()
    {
        $wallets = \App\Models\AiCreditWallet::where('renewal_date', '<=', now()->toDateString())->get();
        $count = 0;

        foreach ($wallets as $wallet) {
            $user = $wallet->user;
            if ($user && $user->isActive()) {
                app(\App\Services\AiCreditService::class)->renewMonthly($user);
                $count++;
            }
        }

        $this->info("Renovação concluída para {$count} usuários.");
    }
}
