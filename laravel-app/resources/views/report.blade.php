@extends('layouts.app', ['navCurrent' => 'report'])

@section('title', 'Relatório semanal')

@section('content')
        <h1>Relatório — últimos 7 dias</h1>
        <p class="lead">De {{ $start->translatedFormat('d/m/Y') }} a {{ $end->translatedFormat('d/m/Y') }}.</p>

        <div class="stats" aria-label="Resumo da semana" style="margin-bottom: 1.25rem;">
            <div class="stat">
                <p class="stat-label">Média kcal/dia (dias com registro)</p>
                <p class="stat-value">{{ $daysWithFood > 0 ? $avgKcal : '—' }}</p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;">{{ $daysWithFood }} dia(s) com alimentação</p>
            </div>
            <div class="stat">
                <p class="stat-label">Total exercício</p>
                <p class="stat-value tabular-nums">{{ $totalExMin }} min</p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;">Σ kcal informadas: {{ $totalExKcal }}</p>
            </div>
            <div class="stat">
                <p class="stat-label">Variação de peso (período)</p>
                <p class="stat-value tabular-nums">{{ $deltaWeight !== null ? sprintf('%+.2f', $deltaWeight).' kg' : '—' }}</p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;">Primeira vs última pesagem na semana (ou vs peso anterior)</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="margin-top:0;">Por dia</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>kcal</th>
                            <th>Ex. (min)</th>
                            <th>Ex. kcal</th>
                            <th>Peso (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($days as $info)
                            <tr>
                                <td>{{ $info['label'] }}</td>
                                <td class="tabular-nums">{{ $info['kcal_in'] > 0 ? $info['kcal_in'] : '—' }}</td>
                                <td class="tabular-nums">{{ $info['ex_min'] > 0 ? $info['ex_min'] : '—' }}</td>
                                <td class="tabular-nums">{{ $info['ex_kcal'] > 0 ? $info['ex_kcal'] : '—' }}</td>
                                <td class="tabular-nums">{{ $info['weight'] !== null ? number_format($info['weight'], 1, ',', '.') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="actions-inline">
            <a class="btn btn-primary" href="{{ route('export') }}">Exportar CSV</a>
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Voltar ao hoje</a>
        </div>
@endsection
