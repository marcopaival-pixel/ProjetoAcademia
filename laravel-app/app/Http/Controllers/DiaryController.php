<?php

namespace App\Http\Controllers;

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
        $isPremium = $user->isPremiumActive();

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

        return view('diary', [
            'date' => $date,
            'rows' => $rows,
            'sumCal' => $sumCal,
            'sumP' => $sumP,
            'sumC' => $sumC,
            'sumF' => $sumF,
            'macroTargets' => $macroTargets,
            'hasMacroTargets' => $hasMacroTargets,
            'mealLabels' => $mealLabels,
            'editRow' => $editRow,
            'notice' => $notice,
            'error' => session('error'),
            'formMeal' => $formMeal,
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
            'calories' => $calories,
            'protein_g' => $p,
            'carbs_g' => $c,
            'fat_g' => $f,
        ]);

        return redirect()->route('diary', ['date' => $date, 'flash' => 'added']);
    }
}
