<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class CustomerSuccessController extends Controller
{
    public function index()
    {
        $this->updateHealthScores();

        $stats = [
            'high_risk' => User::where('churn_risk', 'High')->count(),
            'medium_risk' => User::where('churn_risk', 'Medium')->count(),
            'low_risk' => User::where('churn_risk', 'Low')->count(),
            'avg_health' => User::avg('health_score') ?? 0,
        ];

        $atRiskUsers = User::whereIn('churn_risk', ['High', 'Medium'])
            ->orderByRaw("FIELD(churn_risk, 'High', 'Medium', 'Low')")
            ->orderBy('health_score')
            ->paginate(20);

        return view('admin.cs.index', compact('stats', 'atRiskUsers'));
    }

    /**
     * Identification of Expansion (Upsell) and Retention opportunities.
     */
    public function retention()
    {
        // For expansion: finding users with many trainees but on lower plans (Hypothetical logic)
        $upsellOpportunities = User::whereNotNull('usage_stats')
            ->get()
            ->filter(function($user) {
                $usage = $user->usage_stats;
                // If usage of certain feature is > 80% of some limit (simple simulation)
                return ($usage['trainees_count'] ?? 0) > 10; 
            });

        $retentionList = User::where('churn_risk', 'High')
            ->where('last_activity_at', '<', now()->subDays(10))
            ->get();

        return view('admin.cs.retention', compact('upsellOpportunities', 'retentionList'));
    }

    private function updateHealthScores()
    {
        // Lógica simples: Se não loga há 7 dias = Medium Risk. Há 15 dias = High Risk.
        $users = User::whereNotNull('last_activity_at')->get();

        foreach ($users as $user) {
            $daysInactive = Carbon::parse($user->last_activity_at)->diffInDays(now());
            $score = 100;

            if ($daysInactive > 15) {
                $score = 20;
                $risk = 'High';
            } elseif ($daysInactive > 7) {
                $score = 50;
                $risk = 'Medium';
            } else {
                $score = 90 + (10 - min($daysInactive, 10)); // 90-100
                $risk = 'Low';
            }

            $user->update([
                'health_score' => $score,
                'churn_risk' => $risk
            ]);
        }
    }
}
