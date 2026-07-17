<?php

namespace App\Livewire\Pulse;

use App\Models\AIOrchestratorLog;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class AiUsageCard extends Card
{
    public function render()
    {
        $stats = AIOrchestratorLog::query()
            ->select([
                'agent_name',
                DB::raw('count(*) as total_calls'),
                DB::raw('sum(total_tokens) as total_tokens'),
                DB::raw('sum(cost_usd) as total_cost'),
            ])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('agent_name')
            ->orderByDesc('total_calls')
            ->get();

        $totals = [
            'calls' => $stats->sum('total_calls'),
            'tokens' => $stats->sum('total_tokens'),
            'cost' => $stats->sum('total_cost'),
        ];

        return view('livewire.pulse.ai-usage-card', [
            'stats' => $stats,
            'totals' => $totals,
        ]);
    }
}
