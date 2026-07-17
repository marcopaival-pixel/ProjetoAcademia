<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GoalController extends Controller
{
    /**
     * Display a listing of goals with their progress.
     */
    public function index()
    {
        $goals = Goal::orderBy('end_date', 'asc')->get();
        
        // Dynamic calculations for current values if needed
        foreach ($goals as $goal) {
            if ($goal->is_active) {
                switch ($goal->type) {
                    case 'revenue':
                        $goal->current_value = Payment::where('status', 'paid')
                            ->whereBetween('updated_at', [$goal->start_date, $goal->end_date])
                            ->sum('amount');
                        break;
                    case 'new_users':
                        $goal->current_value = User::role('user')
                            ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                            ->count();
                        break;
                    case 'active_users':
                        $goal->current_value = User::role('user')
                            ->where('last_activity_at', '>=', now()->subDays(7))
                            ->count();
                        break;
                }
                $goal->save();
            }
        }

        return view('admin.commercial.goals.index', compact('goals'));
    }

    /**
     * Store a newly created goal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:revenue,new_users,active_users,tickets_resolved,custom',
            'target_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Goal::create($request->all());

        return back()->with('success', 'Meta estratégica definida com sucesso!');
    }

    /**
     * Remove the specified goal.
     */
    public function destroy(Goal $goal)
    {
        $goal->delete();
        return back()->with('success', 'Meta removida do planejamento.');
    }
}
