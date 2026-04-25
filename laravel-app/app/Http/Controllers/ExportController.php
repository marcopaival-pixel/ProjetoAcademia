<?php

namespace App\Http\Controllers;

use App\Support\CsvExporter;
use App\Support\ExportRangeParser;
use App\Models\FoodEntry;
use App\Models\ExerciseEntry;
use App\Models\WeightEntry;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __invoke(Request $request, \App\Services\ReportMonetizationService $monetizationService): View|StreamedResponse|\Illuminate\Http\Response
    {
        $user = $request->user();
        $uid = (int) $user->id;
        $isPremium = $monetizationService->hasPremium($user);
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
            $pf = $parsed['from'];
            $pt = $parsed['to'];
            
            $rangeSuffix = '';
            if ($pf !== null || $pt !== null) {
                $rangeSuffix = '_'.($pf ?? 'inicio').'_a_'.($pt ?? 'fim');
            }
            $stamp = (new DateTimeImmutable('now'))->format('Y-m-d_His');

            if ($kind === 'food') {
                // Registrar log
                $monetizationService->logGeneration($user, 'Export CSV Food', ['from' => $request->query('from'), 'to' => $request->query('to')]);
                $query = FoodEntry::where('user_id', $uid);
                if ($pf) $query->where('entry_date', '>=', $pf);
                if ($pt) $query->where('entry_date', '<=', $pt);
                $rows = $query->orderBy('entry_date')->orderBy('id')->get();
                
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        $r->id,
                        $r->entry_date->format('Y-m-d'),
                        $r->meal_type,
                        $r->food_name,
                        $r->calories,
                        $r->protein_g,
                        $r->carbs_g,
                        $r->fat_g,
                        $r->created_at ? $r->created_at->format('Y-m-d H:i:s') : '',
                    ];
                }

                return CsvExporter::download(
                    'projetoacademia_alimentacao'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'refeicao', 'alimento', 'kcal', 'proteina_g', 'carbo_g', 'gordura_g', 'criado_em'],
                    $data
                );
            }

            if ($kind === 'exercise') {
                // Registrar log
                $monetizationService->logGeneration($user, 'Export CSV Exercise', ['from' => $request->query('from'), 'to' => $request->query('to')]);
                $query = ExerciseEntry::where('user_id', $uid);
                if ($pf) $query->where('entry_date', '>=', $pf);
                if ($pt) $query->where('entry_date', '<=', $pt);
                $rows = $query->orderBy('entry_date')->orderBy('id')->get();
                
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        $r->id,
                        $r->entry_date->format('Y-m-d'),
                        $r->activity_type,
                        $r->duration_min,
                        $r->calories_burned ?? '',
                        $r->notes ?? '',
                        $r->created_at ? $r->created_at->format('Y-m-d H:i:s') : '',
                    ];
                }

                return CsvExporter::download(
                    'projetoacademia_exercicios'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'atividade', 'minutos', 'kcal_gasto', 'observacoes', 'criado_em'],
                    $data
                );
            }

            if ($kind === 'weight') {
                $query = WeightEntry::where('user_id', $uid);
                if ($pf) $query->where('weighed_at', '>=', $pf);
                if ($pt) $query->where('weighed_at', '<=', $pt);
                $rows = $query->orderBy('weighed_at')->orderBy('id')->get();
                
                $data = [];
                foreach ($rows as $r) {
                    $data[] = [
                        $r->id, 
                        $r->weighed_at->format('Y-m-d'), 
                        $r->weight_kg, 
                        $r->created_at ? $r->created_at->format('Y-m-d H:i:s') : ''
                    ];
                }

                return CsvExporter::download(
                    'projetoacademia_peso'.$rangeSuffix.'_'.$stamp.'.csv',
                    ['id', 'data', 'peso_kg', 'criado_em'],
                    $data
                );
            }

            // Registrar log geral se cair aqui (tipo inválido ou outros)
            $monetizationService->logGeneration($user, 'Export Attempt', ['kind' => $kind]);

            return response('Tipo de exportação inválido.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return view('export', [
            'isPremium' => $isPremium,
            'formFrom' => (string) $request->query('f_from', ''),
            'formTo' => (string) $request->query('f_to', ''),
        ]);
    }
}
