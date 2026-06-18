<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Support\SubscriptionStatus;
use Illuminate\Console\Command;

class NormalizeSubscriptionStatusesCommand extends Command
{
    protected $signature = 'subscription:normalize-statuses {--dry-run : Apenas simula, sem gravar}';

    protected $description = 'Normaliza subscriptions.status para valores canônicos SaaS';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        Subscription::query()
            ->select(['id', 'status'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($dryRun, &$updated) {
                foreach ($rows as $subscription) {
                    $canonical = SubscriptionStatus::normalize($subscription->status);

                    if ($canonical === $subscription->status) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("#{$subscription->id}: {$subscription->status} → {$canonical}");
                    } else {
                        Subscription::withoutEvents(function () use ($subscription, $canonical) {
                            Subscription::whereKey($subscription->id)->update(['status' => $canonical]);
                        });
                    }

                    $updated++;
                }
            });

        $this->info($dryRun
            ? "Simulação: {$updated} registo(s) seriam normalizados."
            : "Concluído: {$updated} assinatura(s) normalizadas.");

        return self::SUCCESS;
    }
}
