<?php

namespace App\Http\Controllers;

use App\Support\CsvExporter;
use App\Support\ExportRangeParser;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __invoke(Request $request): View|StreamedResponse|\Illuminate\Http\Response
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $user->hasPremiumAccess();
        $kind = (string) $request->query('kind', '');

        if ($kind !== '') {
            if (! $isPremium) {
                return response(
                    'Exportação em CSV é um recurso Premium. Abra Meu Plano para assinar.',
                    403,
                    ['Content-Type' => 'text/plain; charset=UTF-8']
                );
            }
            $parsed = ExportRangeParser::parse($request->query('from'), $request->query('to'));
            if (! $parsed['ok']) {
                return response($parsed['message'], 400, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }
            $range = ['from' => $parsed['from'], 'to' => $parsed['to']];
            $pf = $parsed['from'];
            $pt = $parsed['to'];
            $rangeSuffix = '';
            if ($pf !== null || $pt !== null) {
                $rangeSuffix = '_'.($pf ?? 'inicio').'_a_'.($pt ?? 'fim');
            }
            $stamp = (new DateTimeImmutable('now'))->format('Y-m-d_His');

            if ($kind === 'food') {
                $extra = ExportRangeParser::sqlDateRangeClause('entry_date', $range);
                $sql = 'SELECT id, entry_date, meal_type, food_name, calories, protein_g, carbs_g, fat_g, created_at
                     FROM food_entries WHERE user_id = ?'.$extra['clause'].' ORDER BY entry_date ASC, id ASC';
                $rows = DB::select($sql, array_merge([$uid], $extra['params']));
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        $r->id,
                        $r->entry_date,
                        $r->meal_type,
                        $r->food_name,
                        $r->calories,
                        $r->protein_g,
                        $r->carbs_g,
                        $r->fat_g,
                        $r->created_at,
                    ];
                }

                return CsvExporter::download(
                    'projetoacademia_alimentacao'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'refeicao', 'alimento', 'kcal', 'proteina_g', 'carbo_g', 'gordura_g', 'criado_em'],
                    $data
                );
            }

            if ($kind === 'exercise') {
                $extra = ExportRangeParser::sqlDateRangeClause('entry_date', $range);
                $sql = 'SELECT id, entry_date, activity_type, duration_min, calories_burned, notes, created_at
                     FROM exercise_entries WHERE user_id = ?'.$extra['clause'].' ORDER BY entry_date ASC, id ASC';
                $rows = DB::select($sql, array_merge([$uid], $extra['params']));
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        $r->id,
                        $r->entry_date,
                        $r->activity_type,
                        $r->duration_min,
                        $r->calories_burned ?? '',
                        $r->notes ?? '',
                        $r->created_at,
                    ];
                }

                return CsvExporter::download(
                    'projetoacademia_exercicios'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'atividade', 'minutos', 'kcal_gasto', 'observacoes', 'criado_em'],
                    $data
                );
            }

            if ($kind === 'weight') {
                $extra = ExportRangeParser::sqlDateRangeClause('weighed_at', $range);
                $sql = 'SELECT id, weighed_at, weight_kg, created_at FROM weight_entries WHERE user_id = ?'
                    .$extra['clause'].' ORDER BY weighed_at ASC, id ASC';
                $rows = DB::select($sql, array_merge([$uid], $extra['params']));
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [$r->id, $r->weighed_at, $r->weight_kg, $r->created_at];
                }

                return CsvExporter::download(
                    'projetoacademia_peso'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'peso_kg', 'criado_em'],
                    $data
                );
            }

            return response('Tipo de exportação inválido.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return view('export', [
            'isPremium' => $isPremium,
            'formFrom' => (string) $request->query('f_from', ''),
            'formTo' => (string) $request->query('f_to', ''),
        ]);
    }
}
