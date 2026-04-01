<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $uid = (int) $user->id;

        $dateRaw = (string) $request->query('date', '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
            $date = now()->format('Y-m-d');
        } else {
            $date = $dateRaw;
        }

        $notice = match ($request->query('flash')) {
            'added' => 'Exercício registrado.',
            'removed' => 'Exercício removido.',
            'updated' => 'Exercício atualizado.',
            'copied' => $request->query('n', 0) > 0
                ? 'Copiado(s) '.(int) $request->query('n').' exercício(s) de outro dia.'
                : 'Registros copiados.',
            default => '',
        };

        $editId = (int) $request->query('edit', 0);
        $editRow = null;
        if ($editId > 0) {
            $editRow = DB::table('exercise_entries')
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

        $rows = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $sumRow = DB::table('exercise_entries')
            ->where('user_id', $uid)
            ->where('entry_date', $date)
            ->selectRaw('COALESCE(SUM(duration_min), 0) as dm, COALESCE(SUM(calories_burned), 0) as bk')
            ->first();
        $sumMin = (int) ($sumRow->dm ?? 0);
        $sumBurn = (int) ($sumRow->bk ?? 0);

        return view('exercise', [
            'date' => $date,
            'rows' => $rows,
            'sumMin' => $sumMin,
            'sumBurn' => $sumBurn,
            'editRow' => $editRow,
            'notice' => $notice,
            'error' => session('error'),
        ]);
    }

    private function handlePost(Request $request, int $uid, string $date): RedirectResponse
    {
        $action = (string) $request->input('action', '');

        if ($action === 'copy_exercises') {
            $targetDate = (string) $request->input('target_date');
            $sourceDate = (string) $request->input('source_date');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $sourceDate)) {
                return back()->with('error', 'Datas inválidas.');
            }
            if ($sourceDate === $targetDate) {
                return back()->with('error', 'O dia de origem deve ser diferente do dia selecionado.');
            }
            $items = DB::table('exercise_entries')
                ->where('user_id', $uid)
                ->where('entry_date', $sourceDate)
                ->get();
            if ($items->isEmpty()) {
                return back()->with('error', 'Não há exercícios no dia de origem.');
            }
            foreach ($items as $it) {
                DB::table('exercise_entries')->insert([
                    'user_id' => $uid,
                    'entry_date' => $targetDate,
                    'activity_type' => $it->activity_type,
                    'duration_min' => $it->duration_min,
                    'calories_burned' => $it->calories_burned,
                    'notes' => $it->notes,
                ]);
            }

            return redirect()->route('exercise', ['date' => $targetDate, 'flash' => 'copied', 'n' => $items->count()]);
        }

        if ($action === 'delete_exercise') {
            $delDate = (string) $request->input('entry_date');
            $eid = (int) $request->input('exercise_id');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $delDate) || $eid <= 0) {
                return back()->with('error', 'Dados inválidos.');
            }
            $n = DB::table('exercise_entries')
                ->where('id', $eid)
                ->where('user_id', $uid)
                ->where('entry_date', $delDate)
                ->delete();
            if ($n === 0) {
                return back()->with('error', 'Não foi possível excluir.');
            }

            return redirect()->route('exercise', ['date' => $delDate, 'flash' => 'removed']);
        }

        $date = (string) $request->input('entry_date', $date);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return back()->with('error', 'Data inválida.');
        }
        $type = trim((string) $request->input('activity_type'));
        $dur = (int) $request->input('duration_min', 0);
        $cb = $request->input('calories_burned');
        $cbVal = $cb === '' || $cb === null ? null : (int) $cb;
        $notes = trim((string) $request->input('notes', ''));
        $exEditId = (int) $request->input('exercise_edit_id', 0);

        if ($type === '') {
            return back()->with('error', 'Informe o tipo de atividade.');
        }
        if ($dur < 0 || $dur > 1440) {
            return back()->with('error', 'Duração inválida (minutos).');
        }

        $notesVal = $notes === '' ? null : substr($notes, 0, 500);

        if ($exEditId > 0) {
            $own = DB::table('exercise_entries')
                ->where('id', $exEditId)
                ->where('user_id', $uid)
                ->where('entry_date', $date)
                ->exists();
            if (! $own) {
                return back()->with('error', 'Registro não encontrado.');
            }
            DB::table('exercise_entries')->where('id', $exEditId)->update([
                'activity_type' => $type,
                'duration_min' => $dur,
                'calories_burned' => $cbVal,
                'notes' => $notesVal,
            ]);

            return redirect()->route('exercise', ['date' => $date, 'flash' => 'updated']);
        }

        DB::table('exercise_entries')->insert([
            'user_id' => $uid,
            'entry_date' => $date,
            'activity_type' => $type,
            'duration_min' => $dur,
            'calories_burned' => $cbVal,
            'notes' => $notesVal,
        ]);

        return redirect()->route('exercise', ['date' => $date, 'flash' => 'added']);
    }
}
