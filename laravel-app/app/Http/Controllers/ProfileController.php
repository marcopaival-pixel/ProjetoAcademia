<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\UserProfile;
use App\Models\WeightEntry;
use App\Services\Nutrition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        $profile = $user->profile ?? new UserProfile();

        $u = (object) [
            'name' => $user->name,
            'email' => $user->email,
            'birth_date' => $profile->birth_date?->format('Y-m-d') ?? $profile->birth_date,
            'sex' => $profile->sex,
            'height_cm' => $profile->height_cm,
            'activity_level' => $profile->activity_level,
            'climate' => $profile->climate,
            'goal' => $profile->goal,
            'daily_calorie_target' => $profile->daily_calorie_target,
            'protein_target_g' => $profile->protein_target_g,
            'carbs_target_g' => $profile->carbs_target_g,
            'fat_target_g' => $profile->fat_target_g,
            'water_target_ml' => $profile->water_target_ml,
            'is_water_target_auto' => $profile->is_water_target_auto,
            'target_weight_kg' => $profile->target_weight_kg,
            'training_days_per_week' => $profile->training_days_per_week,
        ];

        $latestWeightRow = $user->weightEntries()->orderByDesc('weighed_at')->first();

        $calPreview = null;
        if ($latestWeightRow && ! empty($u->birth_date) && $u->height_cm !== null) {
            $est = Nutrition::estimateTarget(
                (string) $u->birth_date,
                (int) $u->height_cm,
                (string) ($u->sex ?? ''),
                (string) ($u->activity_level ?? 'moderate'),
                (string) ($u->goal ?? 'maintain'),
                (float) $latestWeightRow->weight_kg
            );
            if ($est['ok']) {
                $calPreview = array_merge($est, ['weighed_at' => $latestWeightRow->weighed_at->format('Y-m-d')]);
            }
        }

        $freeMacroPrev = null;
        if (! $isPremium && $u->daily_calorie_target !== null && (int) $u->daily_calorie_target > 0) {
            $freeMacroPrev = Nutrition::defaultMacroTargetsFromKcal((int) $u->daily_calorie_target);
        }

        $age = ! empty($u->birth_date) ? Nutrition::ageYears((string) $u->birth_date) : null;

        return view('profile', [
            'u' => $u,
            'isPremium' => $isPremium,
            'calPreview' => $calPreview,
            'freeMacroPrev' => $freeMacroPrev,
            'latestWeight' => $latestWeightRow ? (float) $latestWeightRow->weight_kg : null,
            'age' => $age,
            'notice' => session('notice'),
            'error' => session('error'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isPremium = $user->hasPremiumAccess();

        // 1. Alteração de Senha
        if ($request->input('profile_action') === 'password') {
            $request->validate([
                'current_password' => ['required'],
                'new_password' => ['required', 'min:8'],
                'new_password_confirm' => ['required', 'same:new_password'],
            ]);

            if (! Hash::check($request->input('current_password'), $user->password_hash)) {
                return back()->with('error', 'Senha atual incorreta.')->withInput();
            }

            $user->password_hash = Hash::make($request->input('new_password'));
            $user->save();

            return back()->with('notice', 'Senha alterada com sucesso.');
        }

        // 2. Atualização de Macros (Atalho Premium)
        if ($request->input('profile_action') === 'macros' && $isPremium) {
             UserProfile::updateOrCreate(
                 ['user_id' => $user->id],
                 [
                     'protein_target_g' => $request->input('protein_target_g'),
                     'carbs_target_g' => $request->input('carbs_target_g'),
                     'fat_target_g' => $request->input('fat_target_g'),
                 ]
             );

             return back()->with('notice', 'Metas de macros atualizadas.');
        }

        // 3. Atualização de Perfil Geral
        $data = $request->validated();
        $autoCalorie = $request->boolean('auto_calorie');
        $autoWater = $request->boolean('auto_water');
        
        $est = null;
        $lw = $user->weightEntries()->orderByDesc('weighed_at')->first();
        $currentWeight = $data['current_weight_kg'] ?? ($lw ? (float) $lw->weight_kg : null);

        // Cálculos Automáticos de Nutrição
        if ($autoCalorie) {
            $est = Nutrition::estimateTarget(
                $data['birth_date'], $data['height_cm'], $data['sex'],
                $data['activity_level'], $data['goal'], $currentWeight
            );
            if (! $est['ok']) {
                return back()->with('error', $est['message'])->withInput();
            }
            $data['daily_calorie_target'] = $est['target'];
        }

        if ($autoWater && $currentWeight !== null) {
            $data['water_target_ml'] = Nutrition::calculateWaterTarget(
                $currentWeight, $data['birth_date'], $data['sex'],
                $data['activity_level'], $data['climate']
            );
        }

        // Persistência em Transação usando Eloquent via DB::transaction global
        DB::transaction(function () use ($user, $data, $autoWater) {
            $user->update(['name' => $data['name']]);

            if (!empty($data['current_weight_kg'])) {
                WeightEntry::updateOrCreate(
                    ['user_id' => $user->id, 'weighed_at' => date('Y-m-d')],
                    ['weight_kg' => $data['current_weight_kg']]
                );
            }

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'birth_date' => $data['birth_date'],
                    'sex' => $data['sex'],
                    'height_cm' => $data['height_cm'] ?? null,
                    'activity_level' => $data['activity_level'],
                    'climate' => $data['climate'],
                    'goal' => $data['goal'],
                    'target_weight_kg' => $data['target_weight_kg'] ?? null,
                    'training_days_per_week' => $data['training_days_per_week'] ?? null,
                    'daily_calorie_target' => $data['daily_calorie_target'] ?? null,
                    'protein_target_g' => $data['protein_target_g'] ?? null,
                    'carbs_target_g' => $data['carbs_target_g'] ?? null,
                    'fat_target_g' => $data['fat_target_g'] ?? null,
                    'water_target_ml' => $data['water_target_ml'] ?? null,
                    'is_water_target_auto' => $autoWater,
                ]
            );
        });

        $notice = 'Perfil atualizado.';
        if ($autoCalorie && $est) {
            $bmrR = (int) round($est['bmr']);
            $tdeeR = (int) round($est['tdee']);
            $notice .= " Meta estimada: {$est['target']} kcal (TMB ≈ {$bmrR}, gasto estimado ≈ {$tdeeR} kcal/dia).";
        }

        return back()->with('notice', $notice);
    }
}
