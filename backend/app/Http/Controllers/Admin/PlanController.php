<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Role;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /** Roles que podem ser vinculadas a planos — exclui roles internas/administrativas */
    private const ASSIGNABLE_ROLES = ['aluno', 'personal', 'nutricionista', 'academia', 'professional'];

    public function index()
    {
        $plans = Plan::withCount('planFeatures')->with('roles')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLES)->orderBy('label')->get();
        return view('admin.plans.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'type'                 => 'required|in:student,personal,nutritionist,professional,full,clinic',
            'price'                => 'required|numeric|min:0',
            'ai_credits'           => 'required|integer|min:0',
            'max_workouts'         => 'required|integer|min:0',
            'max_diets'            => 'required|integer|min:0',
            'max_assessments'      => 'required|integer|min:0',
            'max_patients'         => 'required|integer|min:0',
            'max_professionals'    => 'required|integer|min:0',
            'is_corporate'         => 'boolean',
            'price_per_professional' => 'nullable|numeric|min:0',
            'min_professionals'    => 'nullable|integer|min:1',
            'trial_days'           => 'required|integer|min:0',
            'features'             => 'required|array',
            'features.*'           => 'string',
            'role_ids'             => 'nullable|array',
            'role_ids.*'           => 'integer|exists:roles,id',
        ]);

        $plan = Plan::create([
            'name'                   => $validated['name'],
            'description'            => $validated['description'],
            'type'                   => $validated['type'],
            'price'                  => $validated['price'],
            'ai_credits'             => $validated['ai_credits'],
            'max_workouts'           => $validated['max_workouts'],
            'max_diets'              => $validated['max_diets'],
            'max_assessments'        => $validated['max_assessments'],
            'max_patients'           => $validated['max_patients'],
            'max_professionals'      => $validated['max_professionals'],
            'is_corporate'           => $request->has('is_corporate'),
            'price_per_professional' => $validated['price_per_professional'] ?? 0,
            'min_professionals'      => $validated['min_professionals'] ?? 1,
            'trial_days'             => $validated['trial_days'],
            'is_active'              => true,
        ]);

        foreach ($validated['features'] as $feature) {
            $plan->planFeatures()->create(['feature_key' => $feature]);
        }

        // Vincular perfis (roles) ao plano — Regra 4: um plano pode ter múltiplos perfis
        $plan->roles()->sync($request->input('role_ids', []));

        return redirect()->route('admin.plans.index')->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        $plan->load(['planFeatures', 'roles']);
        $roles = Role::whereIn('name', self::ASSIGNABLE_ROLES)->orderBy('label')->get();
        return view('admin.plans.edit', compact('plan', 'roles'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'type'                 => 'required|in:student,personal,nutritionist,professional,full,clinic',
            'price'                => 'required|numeric|min:0',
            'ai_credits'           => 'required|integer|min:0',
            'max_workouts'         => 'required|integer|min:0',
            'max_diets'            => 'required|integer|min:0',
            'max_assessments'      => 'required|integer|min:0',
            'max_patients'         => 'required|integer|min:0',
            'max_professionals'    => 'required|integer|min:0',
            'is_corporate'         => 'boolean',
            'price_per_professional' => 'nullable|numeric|min:0',
            'min_professionals'    => 'nullable|integer|min:1',
            'trial_days'           => 'required|integer|min:0',
            'features'             => 'required|array',
            'features.*'           => 'string',
            'role_ids'             => 'nullable|array',
            'role_ids.*'           => 'integer|exists:roles,id',
        ]);

        $plan->update([
            'name'                   => $validated['name'],
            'description'            => $validated['description'],
            'type'                   => $validated['type'],
            'price'                  => $validated['price'],
            'ai_credits'             => $validated['ai_credits'],
            'max_workouts'           => $validated['max_workouts'],
            'max_diets'              => $validated['max_diets'],
            'max_assessments'        => $validated['max_assessments'],
            'max_patients'           => $validated['max_patients'],
            'max_professionals'      => $validated['max_professionals'],
            'is_corporate'           => $request->has('is_corporate'),
            'price_per_professional' => $validated['price_per_professional'] ?? 0,
            'min_professionals'      => $validated['min_professionals'] ?? 1,
            'trial_days'             => $validated['trial_days'],
        ]);

        // Sincronizar features (deleta e recria)
        $plan->planFeatures()->delete();
        foreach ($validated['features'] as $feature) {
            $plan->planFeatures()->create(['feature_key' => $feature]);
        }

        // Sincronizar roles — mantém integridade da pivot plan_roles
        $plan->roles()->sync($request->input('role_ids', []));

        return redirect()->route('admin.plans.index')->with('success', 'Plano atualizado com sucesso!');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->status = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->save();

        return redirect()->back()->with('success', 'Status do plano alterado!');
    }
}
