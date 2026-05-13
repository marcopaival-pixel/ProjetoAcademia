<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppFeature;
use App\Models\FeatureLimit;
use App\Models\Plan;
use App\Models\UpgradePopup;
use Illuminate\Http\Request;

class MonetizationController extends Controller
{
    public function features()
    {
        $features = AppFeature::all();
        return view('admin.monetization.features', compact('features'));
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:app_features,code',
            'category' => 'required|in:free,freemium,premium,ai_credits',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_lock' => 'boolean',
            'show_badge' => 'boolean',
        ]);

        AppFeature::create($validated);

        return redirect()->back()->with('success', 'Funcionalidade cadastrada com sucesso!');
    }

    public function updateFeature(Request $request, AppFeature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:free,freemium,premium,ai_credits',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_lock' => 'boolean',
            'show_badge' => 'boolean',
        ]);

        $feature->update($validated);

        return redirect()->back()->with('success', 'Funcionalidade atualizada!');
    }

    public function limits()
    {
        $plans = Plan::all();
        $features = AppFeature::where('is_active', true)->get();
        $limits = FeatureLimit::all()->groupBy('plan_id');

        return view('admin.monetization.limits', compact('plans', 'features', 'limits'));
    }

    public function storeLimit(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
            'feature_id' => 'required|exists:app_features,id',
            'limit_value' => 'required|integer|min:0',
            'limit_type' => 'required|in:day,week,month,lifetime,none',
            'action_type' => 'required|in:block,popup,credits',
            'custom_popup_text' => 'nullable|string',
        ]);

        FeatureLimit::updateOrCreate(
            ['plan_id' => $validated['plan_id'], 'feature_id' => $validated['feature_id']],
            $validated
        );

        return redirect()->back()->with('success', 'Limite configurado com sucesso!');
    }

    public function popups()
    {
        $features = AppFeature::all();
        $popups = UpgradePopup::all()->keyBy('feature_code');
        return view('admin.monetization.popups', compact('features', 'popups'));
    }

    public function storePopup(Request $request)
    {
        $validated = $request->validate([
            'feature_code' => 'required|string|exists:app_features,code',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'benefits' => 'nullable|array',
            'button_text' => 'required|string|max:100',
            'image_url' => 'nullable|string',
        ]);

        UpgradePopup::updateOrCreate(
            ['feature_code' => $validated['feature_code']],
            $validated
        );

        return redirect()->back()->with('success', 'Popup configurado com sucesso!');
    }
}
