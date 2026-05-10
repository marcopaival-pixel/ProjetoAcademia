<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('planFeatures')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:student,professional,full,clinic',
            'price' => 'required|numeric|min:0',
            'ai_credits' => 'required|integer|min:0',
            'max_workouts' => 'required|integer|min:0',
            'max_diets' => 'required|integer|min:0',
            'max_assessments' => 'required|integer|min:0',
            'max_patients' => 'required|integer|min:0',
            'max_professionals' => 'required|integer|min:0',
            'is_corporate' => 'boolean',
            'price_per_professional' => 'nullable|numeric|min:0',
            'min_professionals' => 'nullable|integer|min:1',
            'trial_days' => 'required|integer|min:0',
            'features' => 'required|array',
            'features.*' => 'string'
        ]);

        $plan = Plan::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'price' => $validated['price'],
            'ai_credits' => $validated['ai_credits'],
            'max_workouts' => $validated['max_workouts'],
            'max_diets' => $validated['max_diets'],
            'max_assessments' => $validated['max_assessments'],
            'max_patients' => $validated['max_patients'],
            'max_professionals' => $validated['max_professionals'],
            'is_corporate' => $request->has('is_corporate'),
            'price_per_professional' => $validated['price_per_professional'] ?? 0,
            'min_professionals' => $validated['min_professionals'] ?? 1,
            'trial_days' => $validated['trial_days'],
            'is_active' => true,
        ]);

        foreach ($validated['features'] as $feature) {
            $plan->planFeatures()->create(['feature_key' => $feature]);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        $plan->load('planFeatures');
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:student,professional,full,clinic',
            'price' => 'required|numeric|min:0',
            'ai_credits' => 'required|integer|min:0',
            'max_workouts' => 'required|integer|min:0',
            'max_diets' => 'required|integer|min:0',
            'max_assessments' => 'required|integer|min:0',
            'max_patients' => 'required|integer|min:0',
            'max_professionals' => 'required|integer|min:0',
            'is_corporate' => 'boolean',
            'price_per_professional' => 'nullable|numeric|min:0',
            'min_professionals' => 'nullable|integer|min:1',
            'trial_days' => 'required|integer|min:0',
            'features' => 'required|array',
            'features.*' => 'string'
        ]);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'price' => $validated['price'],
            'ai_credits' => $validated['ai_credits'],
            'max_workouts' => $validated['max_workouts'],
            'max_diets' => $validated['max_diets'],
            'max_assessments' => $validated['max_assessments'],
            'max_patients' => $validated['max_patients'],
            'max_professionals' => $validated['max_professionals'],
            'is_corporate' => $request->has('is_corporate'),
            'price_per_professional' => $validated['price_per_professional'] ?? 0,
            'min_professionals' => $validated['min_professionals'] ?? 1,
            'trial_days' => $validated['trial_days'],
        ]);

        // Sincronizar features (abordagem simples: deleta e recria)
        $plan->planFeatures()->delete();
        foreach ($validated['features'] as $feature) {
            $plan->planFeatures()->create(['feature_key' => $feature]);
        }

        return redirect()->route('admin.plans.index')->with('success', 'Plano atualizado com sucesso!');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->status = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->save();

        return redirect()->back()->with('success', 'Status do plano alterado!');
    }
}
