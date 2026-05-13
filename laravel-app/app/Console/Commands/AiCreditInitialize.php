<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\AiCreditWallet;
use App\Models\AiCreditTransaction;
use App\Services\AiCreditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AiCreditInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:credits-initialize {--force : Force initialization for all users even if wallet exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize AI credit wallets for all users and migrate legacy credits';

    /**
     * Execute the console command.
     */
    public function handle(AiCreditService $service)
    {
        $users = User::all();
        $this->info("Initializing wallets for {$users->count()} users...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            DB::transaction(function () use ($user, $service) {
                $wallet = $user->aiWallet;
                $isNew = false;

                if (!$wallet) {
                    $wallet = new AiCreditWallet(['user_id' => $user->id]);
                    $isNew = true;
                } elseif (!$this->option('force')) {
                    return;
                }

                // 1. Determine monthly allowance based on current plan
                $allowance = 0;
                $activePlan = null;

                // Check Subscription (SaaS/Pro)
                if ($user->relationLoaded('currentSubscription') || $user->currentSubscription()->exists()) {
                    $sub = $user->currentSubscription()->first();
                    if ($sub && $sub->isActive()) {
                        $activePlan = $sub->plan;
                    }
                }

                // Check UserPlan (Student/Patient)
                if (!$activePlan && ($user->relationLoaded('activePlan') || $user->activePlan()->exists())) {
                    $up = $user->activePlan()->first();
                    if ($up && $up->status === 'active') {
                        $activePlan = $up->plan;
                    }
                }

                if ($activePlan) {
                    $allowance = $activePlan->ai_credits ?? 0;
                }

                $wallet->monthly_allowance = $allowance;
                $wallet->balance = $allowance; // Initial balance
                
                // 2. Migrate legacy credits (as extra_credits)
                $legacyCredits = (int) ($user->ai_credits ?? 0);
                if ($legacyCredits > 0) {
                    $wallet->extra_credits = $legacyCredits;
                    $wallet->balance += $legacyCredits;
                }

                // 3. Set renewal date to next month if they have an allowance
                if ($allowance > 0) {
                    $wallet->renewal_date = now()->addMonth()->startOfDay();
                }

                $wallet->save();

                // 4. Log initial transaction if credits were added
                if ($isNew && $wallet->balance > 0) {
                    AiCreditTransaction::create([
                        'user_id' => $user->id,
                        'credits' => $wallet->balance,
                        'type' => 'bonus',
                        'balance_before' => 0,
                        'balance_after' => $wallet->balance,
                        'description' => "Migração inicial: {$allowance} plano + {$legacyCredits} legado",
                    ]);
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('AI credit wallets initialized successfully.');
    }
}
