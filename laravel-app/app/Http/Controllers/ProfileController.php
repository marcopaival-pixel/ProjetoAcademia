<?php

namespace App\Http\Controllers;

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
        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();

        $u = DB::table('users as u')
            ->leftJoin('user_profiles as p', 'p.user_id', '=', 'u.id')
            ->where('u.id', $uid)
            ->select([
                'u.name', 'u.email', 'p.birth_date', 'p.sex', 'p.height_cm', 'p.activity_level', 'p.climate', 'p.goal',
                'p.daily_calorie_target', 'p.protein_target_g', 'p.carbs_target_g', 'p.fat_target_g', 'p.water_target_ml',
                'p.is_water_target_auto', 'p.target_weight_kg', 'p.training_days_per_week',
            ])
            ->first();

        abort_if(! $u, 404);

        $latestWeightRow = DB::table('weight_entries')
            ->where('user_id', $uid)
            ->orderByDesc('weighed_at')
            ->first();

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
                $calPreview = array_merge($est, ['weighed_at' => $latestWeightRow->weighed_at]);
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

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();

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

        if ($request->input('profile_action') === 'macros' && $isPremium) {
             // Only update macros
             $pt = (string) $request->input('protein_target_g', '');
             $ct = (string) $request->input('carbs_target_g', '');
             $ft = (string) $request->input('fat_target_g', '');
             $p = $pt === '' ? null : (float) str_replace(',', '.', $pt);
             $c = $ct === '' ? null : (float) str_replace(',', '.', $ct);
             $f = $ft === '' ? null : (float) str_replace(',', '.', $ft);

             DB::table('user_profiles')->where('user_id', $uid)->update([
                 'protein_target_g' => $p,
                 'carbs_target_g' => $c,
                 'fat_target_g' => $f,
             ]);

             return back()->with('notice', 'Metas de macros atualizadas.');
        }

        $name = trim((string) $request->input('name'));
        $birth = (string) $request->input('birth_date');
        $birthSql = $birth === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) ? null : $birth;
        $sex = (string) $request->input('sex');
        if (! in_array($sex, ['', 'M', 'F', 'O'], true)) {
            $sex = '';
        }
        $height = $request->input('height_cm');
        $heightSql = $height === '' || $height === null ? null : (int) $height;
        if ($heightSql !== null && ($heightSql < 50 || $heightSql > 260)) {
            return back()->with('error', 'Altura inválida.')->withInput();
        }

        $activity = (string) $request->input('activity_level', 'moderate');
        $actAllowed = ['sedentary', 'light', 'moderate', 'active', 'very_active'];
        if (! in_array($activity, $actAllowed, true)) {
            $activity = 'moderate';
        }

        $climate = (string) $request->input('climate', 'moderate');
        if (! in_array($climate, ['cold', 'moderate', 'hot'])) {
            $climate = 'moderate';
        }

        $goal = (string) $request->input('goal', 'maintain');
        if (! in_array($goal, ['lose', 'gain', 'maintain'], true)) {
            $goal = 'maintain';
        }

        $weight = (string) $request->input('current_weight_kg', '');
        $weightSql = $weight === '' ? null : (float) str_replace(',', '.', $weight);
        if ($weightSql !== null && ($weightSql < 20 || $weightSql > 500)) {
            return back()->with('error', 'Peso atual inválido (20-500kg).')->withInput();
        }

        $targetWeight = (string) $request->input('target_weight_kg', '');
        $targetWeightSql = $targetWeight === '' ? null : (float) str_replace(',', '.', $targetWeight);
        if ($targetWeightSql !== null && ($targetWeightSql < 20 || $targetWeightSql > 500)) {
            return back()->with('error', 'Peso objetivo inválido.')->withInput();
        }

        $training = (string) $request->input('training_days_per_week', '');
        if (! in_array($training, ['', '1-2', '3-4', '5-6', 'all'], true)) {
            $training = '';
        }
        $autoCalorie = $request->boolean('auto_calorie');
        $autoWater = $request->boolean('auto_water');
        $target = $request->input('daily_calorie_target');
        $targetSql = $target === '' || $target === null ? null : (int) $target;
        $est = null;
        $error = '';

        // Busca o peso mais recente se qualquer um dos cálculos automáticos estiver ativo
        $lw = null;
        if ($autoCalorie || $autoWater) {
            $lw = DB::table('weight_entries')->where('user_id', $uid)->orderByDesc('weighed_at')->first();
        }

        if ($autoCalorie) {
            $lwKg = $lw ? (float) $lw->weight_kg : $weightSql;
            $est = Nutrition::estimateTarget($birthSql, $heightSql, $sex, $activity, $goal, $lwKg);
            if (! $est['ok']) {
                $error = $est['message'];
            } else {
                $targetSql = $est['target'];
            }
        }

        if ($error === '' && $targetSql !== null && ($targetSql < 500 || $targetSql > 20000)) {
            $error = 'Meta calórica fora do intervalo (500–20000).';
        }

        $proteinT = null;
        $carbsT = null;
        $fatT = null;
        if ($isPremium) {
            $pt = (string) $request->input('protein_target_g', '');
            $ct = (string) $request->input('carbs_target_g', '');
            $ft = (string) $request->input('fat_target_g', '');
            $proteinT = $pt === '' ? null : (float) str_replace(',', '.', $pt);
            $carbsT = $ct === '' ? null : (float) str_replace(',', '.', $ct);
            $fatT = $ft === '' ? null : (float) str_replace(',', '.', $ft);
        }
        $wt = (string) $request->input('water_target_ml', '');
        $waterT = $wt === '' ? null : (int) $wt;

        if ($autoWater) {
            $lwKg = isset($lw) && $lw ? (float) $lw->weight_kg : $weightSql;
            if ($lwKg !== null) {
                $waterT = Nutrition::calculateWaterTarget($lwKg, $birthSql, $sex, $activity, $climate);
            }
        }

        foreach (['Proteína' => $proteinT, 'Carboidrato' => $carbsT, 'Gordura' => $fatT] as $lab => $v) {
            if ($v !== null && ($v < 0 || $v > 600)) {
                $error = "Meta de {$lab} inválida (0–600 g).";
                break;
            }
        }
        if ($error === '' && $waterT !== null && ($waterT < 500 || $waterT > 10000)) {
            $error = 'Meta de água inválida (500–10000 ml).';
        }
        if ($error === '' && $name === '') {
            $error = 'Nome obrigatório.';
        }
        if ($error === '' && ($sex === '' || $sex === 'O')) {
             // 'O' is allowed in db, but user wants sex mandatory (typical for calculations)
             // We'll treat '' as missing
             $error = 'O campo sexo é obrigatório.';
        }
        if ($error !== '') {
            return back()->with('error', $error)->withInput();
        }

        DB::transaction(function () use ($uid, $name, $birthSql, $sex, $heightSql, $activity, $climate, $goal, $targetSql, $proteinT, $carbsT, $fatT, $waterT, $autoWater, $weightSql, $targetWeightSql, $training) {
            DB::table('users')->where('id', $uid)->update(['name' => $name]);
            $exists = DB::table('user_profiles')->where('user_id', $uid)->exists();
            if ($weightSql !== null) {
                $today = date('Y-m-d');
                DB::table('weight_entries')->updateOrInsert(
                    ['user_id' => $uid, 'weighed_at' => $today],
                    ['weight_kg' => $weightSql]
                );
            }

            // Ensure profile row exists
            DB::table('user_profiles')->updateOrInsert(
                ['user_id' => $uid],
                [
                    'birth_date' => $birthSql,
                    'sex' => $sex,
                    'height_cm' => $heightSql,
                    'activity_level' => $activity,
                    'climate' => $climate,
                    'goal' => $goal,
                    'target_weight_kg' => $targetWeightSql,
                    'training_days_per_week' => $training,
                    'daily_calorie_target' => $targetSql,
                    'protein_target_g' => $proteinT,
                    'carbs_target_g' => $carbsT,
                    'fat_target_g' => $fatT,
                    'water_target_ml' => $waterT,
                    'is_water_target_auto' => $autoWater,
                    'updated_at' => now(),
                ]
            );
        });

        $notice = 'Perfil atualizado.';
        if ($autoCalorie && is_array($est) && ($est['ok'] ?? false)) {
            $bmrR = (int) round($est['bmr']);
            $tdeeR = (int) round($est['tdee']);
            $notice .= " Meta estimada: {$est['target']} kcal (TMB ≈ {$bmrR}, gasto estimado ≈ {$tdeeR} kcal/dia).";
        }

        return back()->with('notice', $notice);
    }
}
