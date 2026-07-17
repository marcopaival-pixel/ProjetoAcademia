<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Console\Command;

class CheckExpiredPlans extends Command
{
    protected $signature = 'plans:check-expirations';
    protected $description = 'Check for expired plans and revert users to FREE plan';

    public function handle()
    {
        $expiredPlans = UserPlan::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->get();

        if ($expiredPlans->isEmpty()) {
            $this->info('No expired plans found.');
            return;
        }

        $freePlan = Plan::where('name', 'FREE')->first();

        if (!$freePlan) {
            $this->error('FREE plan not found in database. Please run PlanSeeder.');
            return;
        }

        foreach ($expiredPlans as $userPlan) {
            $user = $userPlan->user;

            // Mark current plan as expired
            $userPlan->update(['status' => 'expired']);

            // Create new FREE plan for the user
            $user->userPlans()->create([
                'plan_id' => $freePlan->id,
                'start_date' => now(),
                'status' => 'active',
            ]);

            $this->info("User {$user->name} (ID: {$user->id}) reverted to FREE plan.");
        }

        $this->info('Expiration check completed.');
    }
}
