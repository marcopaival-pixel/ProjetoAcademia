<?php

namespace App\Http\Controllers;

use App\Models\MealTemplate;
use App\Services\Nutrition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();

        $dateRaw = (string) $request->query('date', '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
            $date = now()->format('Y-m-d');
        } else {
            $date = $dateRaw;
        }

        $notice = match ($request->query('flash')) {
            'added' => 'Registro adicionado.',
            'removed' => 'Item removido.',
            'updated' => 'Registro atualizado.',
            'copied' => $request->query('n', 0) > 0
                ? 'Copiado(s) '.(int) $request->query('n').' item(ns) de outro dia.'
                : 'Registros copiados.',
            'template_saved' => 'Modelo de refeição guardado.',
            'template_applied' => (int) $request->query('n', 0) > 0
                ? 'Adicionado(s) '.(int) $request->query('n').' item(ns) do modelo ao dia.'
                : 'Itens do modelo adicionados ao dia.',
            'template_deleted' => 'Modelo removido.',
            default => '',
        };

        $editId = (int) $request->query('edit', 0);
        $editRow = null;
        if ($editId > 0) {
            $editRow = DB::table('food_entries')
                ->where('id', $editId)
                ->where('user_id', $uid)
                ->where('entry_date', $date)
                ->first();
            if (! $editRow) {
                $editId = 0;
            }
        }

        if ($request->isMethod('post')) {
            return $this->handlePost($request, $uid, $date);
        }

        $rows = DB::table('food_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $sums = DB::table('food_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->selectRaw('COALESCE(SUM(calories),0) as c, COALESCE(SUM(protein_g),0) as p, COALESCE(SUM(carbs_g),0) as cb, COALESCE(SUM(fat_g),0) as f')
            ->first();
        $sumCal = (int) ($sums->c ?? 0);
        $sumP = (float) ($sums->p ?? 0);
        $sumC = (float) ($sums->cb ?? 0);
        $sumF = (float) ($sums->f ?? 0);

        $macroProf = (array) (DB::table('user_profiles')->where('user_id', $uid)->first() ?? []);
        $macroTargets = Nutrition::macroTargetsForDisplay($isPremium, $macroProf);
        $hasMacroTargets = $isPremium
            ? (($macroTargets['p'] ?? 0) > 0 || ($macroTargets['c'] ?? 0) > 0 || ($macroTargets['f'] ?? 0) > 0)
            : (isset($macroProf['daily_calorie_target']) && $macroProf['daily_calorie_target'] !== null && (int) $macroProf['daily_calorie_target'] > 0);

        $mealLabels = [
            'breakfast' => 'Café da manhã',
            'lunch' => 'Almoço',
            'dinner' => 'Jantar',
            'snack' => 'Lanche',
            'other' => 'Outro',
        ];

        $formMeal = (string) ($editRow->meal_type ?? 'other');
        if (! in_array($formMeal, ['breakfast', 'lunch', 'dinner', 'snack', 'other'], true)) {
            $formMeal = 'other';
        }

        $mealTemplates = $isPremium
            ? MealTemplate::query()->where('user_id', $uid)->orderBy('name')->get(['id', 'name'])
            : collect();

        $calorieTarget = isset($macroProf['daily_calorie_target']) ? (int) $macroProf['daily_calorie_target'] : null;

        return view('diary', [
            'date' => $date,
            'rows' => $rows,
            'sumCal' => $sumCal,
            'sumP' => $sumP,
            'sumC' => $sumC,
            'sumF' => $sumF,
            'macroTargets' => $macroTargets,
            'calorieTarget' => $calorieTarget,
            'hasMacroTargets' => $hasMacroTargets,
            'mealLabels' => $mealLabels,
            'editRow' => $editRow,
            'notice' => $notice,
            'error' => session('error'),
            'formMeal' => $formMeal,
            'isPremium' => $isPremium,
            'mealTemplates' => $mealTemplates,
        ]);
    }

    private function handlePost(Request $request, int $uid, string $date): RedirectResponse
    {
        $action = (string) $request->input('action', '');

        if ($action === 'copy_day') {
            $targetDate = (string) $request->input('target_date');
            $sourceDate = (string) $request->input('source_date');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sourceDate)) {
                return back()->with('error', 'Datas inválidas.');
            }
            if ($sourceDate === $targetDate) {
                return back()->with('error', 'O dia de origem deve ser diferente do dia do diário.');
            }
            $items = DB::table('food_entries')
                ->where('user_id', $uid)
                ->where('entry_date', $sourceDate)
                ->get();
            if ($items->isEmpty()) {
                return back()->with('error', 'Não há alimentos no dia de origem.');
            }
            foreach ($items as $it) {
                DB::table('food_entries')->insert([
                    'user_id' => $uid,
                    'entry_date' => $targetDate,
                    'meal_type' => $it->meal_type,
                    'food_name' => $it->food_name,
                    'calories' => $it->calories,
                    'protein_g' => $it->protein_g,
                    'carbs_g' => $it->carbs_g,
                    'fat_g' => $it->fat_g,
                ]);
            }

            return redirect()->route('diary', ['date' => $targetDate, 'flash' => 'copied', 'n' => $items->count()]);
        }

        if ($action === 'save_meal_template') {
            if (! $request->user()->hasPremiumAccess()) {
                return back()->with('error', 'Modelos de refeição são um recurso Premium.');
            }
            $name = trim((string) $request->input('template_name'));
            if ($name === '' || mb_strlen($name) > 120) {
                return back()->with('error', 'Indique um nome válido para o modelo (até 120 caracteres).');
            }
            $srcDate = (string) $request->input('template_source_date');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $srcDate)) {
                return back()->with('error', 'Data do modelo inválida.');
            }
            $srcItems = DB::table('food_entries')
                ->where('user_id', $uid)
                ->where('entry_date', $srcDate)
                ->orderBy('id')
                ->get();
            if ($srcItems->isEmpty()) {
                return back()->with('error', 'Não há itens neste dia para guardar como modelo.');
            }
            DB::beginTransaction();
            try {
                $tpl = MealTemplate::create([
                    'user_id' => $uid,
                    'name' => $name,
                ]);
                $pos = 0;
                foreach ($srcItems as $it) {
                    $tpl->items()->create([
                        'meal_type' => $it->meal_type,
                        'food_name' => $it->food_name,
                        'calories' => (int) $it->calories,
                        'protein_g' => (float) $it->protein_g,
                        'carbs_g' => (float) $it->carbs_g,
                        'fat_g' => (float) $it->fat_g,
                        'position' => $pos++,
                    ]);
                }
                DB::commit();
            } catch (\Throwable) {
                DB::rollBack();

                return back()->with('error', 'Não foi possível guardar o modelo.');
            }

            return redirect()->route('diary', ['date' => $srcDate, 'flash' => 'template_saved']);
        }

        if ($action === 'apply_meal_template') {
            if (! $request->user()->hasPremiumAccess()) {
                return back()->with('error', 'Modelos de refeição são um recurso Premium.');
            }
            $tid = (int) $request->input('meal_template_id');
            $targetDate = (string) $request->input('target_date');
            if ($tid <= 0 || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate)) {
                return back()->with('error', 'Dados inválidos para aplicar o modelo.');
            }
            $tpl = MealTemplate::with('items')
                ->where('user_id', $uid)
                ->where('id', $tid)
                ->first();
            if ($tpl === null) {
                return back()->with('error', 'Modelo não encontrado.');
            }
            if ($tpl->items->isEmpty()) {
                return back()->with('error', 'Este modelo não tem itens.');
            }
            foreach ($tpl->items as $it) {
                DB::table('food_entries')->insert([
                    'user_id' => $uid,
                    'entry_date' => $targetDate,
                    'meal_type' => $it->meal_type,
                    'food_name' => $it->food_name,
                    'calories' => $it->calories,
                    'protein_g' => $it->protein_g,
                    'carbs_g' => $it->carbs_g,
                    'fat_g' => $it->fat_g,
                ]);
            }

            return redirect()->route('diary', [
                'date' => $targetDate,
                'flash' => 'template_applied',
                'n' => $tpl->items->count(),
            ]);
        }

        if ($action === 'delete_meal_template') {
            if (! $request->user()->hasPremiumAccess()) {
                return back()->with('error', 'Modelos de refeição são um recurso Premium.');
            }
            $tid = (int) $request->input('meal_template_id');
            $redirDate = (string) $request->input('redirect_date');
            if ($tid <= 0 || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $redirDate)) {
                return back()->with('error', 'Dados inválidos para remover o modelo.');
            }
            $n = MealTemplate::query()->where('user_id', $uid)->where('id', $tid)->delete();
            if ($n === 0) {
                return back()->with('error', 'Modelo não encontrado.');
            }

            return redirect()->route('diary', ['date' => $redirDate, 'flash' => 'template_deleted']);
        }

        if ($action === 'delete_food') {
            $delDate = (string) $request->input('entry_date');
            $fid = (int) $request->input('food_id');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $delDate) || $fid <= 0) {
                return back()->with('error', 'Dados inválidos.');
            }
            $n = DB::table('food_entries')
                ->where('id', $fid)
                ->where('user_id', $uid)
                ->where('entry_date', $delDate)
                ->delete();
            if ($n === 0) {
                return back()->with('error', 'Não foi possível excluir o item.');
            }

            return redirect()->route('diary', ['date' => $delDate, 'flash' => 'removed']);
        }

        $date = (string) $request->input('entry_date', $date);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return back()->with('error', 'Data inválida.');
        }
        $meal = (string) $request->input('meal_type', 'other');
        $allowed = ['breakfast', 'lunch', 'dinner', 'snack', 'other'];
        if (! in_array($meal, $allowed, true)) {
            $meal = 'other';
        }
        $name = trim((string) $request->input('food_name'));
        $amountRaw = (string) $request->input('amount');
        $amount = (float) str_replace(',', '.', $amountRaw);
        $unit = (string) $request->input('unit', 'g');
        $calories = (int) $request->input('calories');
        $p = (float) $request->input('protein_g', 0);
        $c = (float) $request->input('carbs_g', 0);
        $f = (float) $request->input('fat_g', 0);
        $foodEditId = (int) $request->input('food_edit_id', 0);

        if ($name === '') {
            return back()->with('error', 'Informe o nome do alimento.');
        }
        if ($calories < 0 || $calories > 20000) {
            return back()->with('error', 'Calorias fora do intervalo esperado.');
        }

        if ($foodEditId > 0) {
            $own = DB::table('food_entries')
                ->where('id', $foodEditId)
                ->where('user_id', $uid)
                ->where('entry_date', $date)
                ->exists();
            if (! $own) {
                return back()->with('error', 'Item não encontrado.');
            }
            DB::table('food_entries')
                ->where('id', $foodEditId)
                ->update([
                    'meal_type' => $meal,
                    'food_name' => $name,
                    'amount' => $amount,
                    'unit' => $unit,
                    'calories' => $calories,
                    'protein_g' => $p,
                    'carbs_g' => $c,
                    'fat_g' => $f,
                ]);

            return redirect()->route('diary', ['date' => $date, 'flash' => 'updated']);
        }

        DB::table('food_entries')->insert([
            'user_id' => $uid,
            'entry_date' => $date,
            'meal_type' => $meal,
            'food_name' => $name,
            'amount' => $amount,
            'unit' => $unit,
            'calories' => $calories,
            'protein_g' => $p,
            'carbs_g' => $c,
            'fat_g' => $f,
        ]);

        return redirect()->route('diary', ['date' => $date, 'flash' => 'added']);
    }
}
