@extends('layouts.app', ['navCurrent' => 'dashboard'])

@section('title', 'Hoje')

@section('content')
        <h1>Hoje</h1>
        <p class="lead">{{ \Carbon\Carbon::parse($today)->translatedFormat('d/m/Y') }} — resumo rápido do dia.</p>

        <section class="summary-card glass animate-fade-up" aria-label="Resumo calórico">
            <div class="calorie-balance">
                <span class="calorie-balance__value">{{ $remaining !== null ? $remaining : '—' }}</span>
                <span class="calorie-balance__label">Kcal Restantes</span>
            </div>
            
            <div class="calorie-split">
                <div class="calorie-split__item">
                    <span class="calorie-split__val">{{ $calorieTarget !== null ? $calorieTarget : '—' }}</span>
                    <span class="calorie-split__lab">Meta</span>
                </div>
                <div class="calorie-split__item">
                    <span class="calorie-split__val">{{ $consumed }}</span>
                    <span class="calorie-split__lab">Consumidas</span>
                </div>
                <div class="calorie-split__item">
                    <span class="calorie-split__val">{{ $burned }}</span>
                    <span class="calorie-split__lab">Gastas</span>
                </div>
            </div>
        </section>

        @if ($calorieTarget === null)
            <div class="alert alert-success">
                Defina sua <strong>meta calórica diária</strong> em <a href="{{ route('profile') }}">Perfil</a> para ver o saldo.
            </div>
        @endif

        @if ($hasMacroTargets)
            <section class="card macro-section animate-fade-up delay-1" aria-label="Macronutrientes hoje">
                <h2 style="margin-top:0;">Macros hoje</h2>
                <div class="macro-grid">
                    @foreach ([['P','Proteína',$sumProt,$macroTargets['p'] ?? null,'#3d9cf5'],['C','Carboidrato',$sumCarb,$macroTargets['c'] ?? null,'#34c759'],['G','Gordura',$sumFat,$macroTargets['f'] ?? null,'#ff9f0a']] as $row)
                        @php
                            [$abbr, $label, $cur, $tgt, $color] = $row;
                            $pct = \App\Support\Macro::barPercent($cur, $tgt);
                        @endphp
                        <div class="macro-item">
                            <div class="macro-item-head">
                                <span class="macro-abbr" style="color:{{ $color }}">{{ $abbr }}</span>
                                <span class="macro-label">{{ $label }}</span>
                            </div>
                            @if ($pct !== null)
                                <div class="macro-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pct }}" style="--macro-fill: {{ $color }}">
                                    <span style="width: {{ $pct }}%;"></span>
                                </div>
                                <p class="macro-stat muted">{{ number_format($cur, 1, ',', '.') }} / {{ number_format((float)$tgt, 1, ',', '.') }} g</p>
                            @else
                                <p class="macro-stat muted">{{ number_format($cur, 1, ',', '.') }} g · <a href="{{ route('profile') }}">definir meta</a></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="card">
            <h2>Último peso registrado</h2>
            @if ($lastWeight)
                <p style="margin:0;"><strong>{{ number_format((float)$lastWeight->weight_kg, 1, ',', '.') }} kg</strong>
                    <span class="muted"> em {{ \Carbon\Carbon::parse($lastWeight->weighed_at)->translatedFormat('d/m/Y') }}</span></p>
            @else
                <p class="empty-state">Nenhum peso ainda. Registre em <a href="{{ route('weight') }}">Peso</a>.</p>
            @endif
        </div>

        <section class="card water-section" aria-label="Controle de Água">
            <h2 style="margin-top:0;">💦 Água hoje</h2>
            @php($waterPct = min(100, $waterTarget > 0 ? ($waterConsumed / $waterTarget) * 100 : 0))
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong>{{ number_format($waterConsumed / 1000, 2, ',', '.') }}L</strong> / {{ number_format($waterTarget / 1000, 2, ',', '.') }}L
            </div>
            <div class="water-bar-container" style="background: rgba(0,0,0,0.05); height: 16px; border-radius: 8px; overflow: hidden; margin-bottom: 1rem; border: 1px solid rgba(0,0,0,0.1);">
                <div class="water-bar-fill" style="width: {{ $waterPct }}%; background: #007aff; height: 100%; transition: width 0.3s ease;"></div>
            </div>
            <form method="post" action="{{ route('dashboard') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @csrf
                <div style="display: flex; gap: 0.35rem; margin-bottom: 0.25rem;">
                    <button type="submit" name="water_add" value="200" class="btn btn-ghost" style="flex: 1; padding: 0.4rem; font-size: 0.8125rem; text-align: center;">💧 200ml</button>
                    <button type="submit" name="water_add" value="300" class="btn btn-ghost" style="flex: 1; padding: 0.4rem; font-size: 0.8125rem; text-align: center;">💧 300ml</button>
                    <button type="submit" name="water_add" value="500" class="btn btn-ghost" style="flex: 1; padding: 0.4rem; font-size: 0.8125rem; text-align: center;">💧 500ml</button>
                    <button type="submit" name="water_add" value="1000" class="btn btn-ghost" style="flex: 1; padding: 0.4rem; font-size: 0.8125rem; text-align: center;">💧 1L</button>
                </div>
                <div style="display: flex; gap: 0.4rem; align-items: center; justify-content: flex-end;">
                    <input type="number" name="water_add_custom" min="10" max="1000" step="10" placeholder="ml" style="width: 5.5rem; border: 1px solid rgba(0,0,0,0.1); padding: 0.4rem 0.5rem; border-radius: 6px; font-size: 0.8125rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="height: 36px; padding: 0 0.75rem; font-weight: bold;">(+)</button>
                </div>
            </form>

            @if (count($waterToday) > 0)
                <div style="margin-top: 1rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 0.75rem;">
                    <h3 class="muted" style="margin: 0 0 0.5rem; font-size: 0.8125rem; font-weight: 600;">Registros de hoje:</h3>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 0.4rem;">
                        @foreach ($waterToday as $w)
                            <li style="background: rgba(0,0,0,0.03); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.4rem; border: 1px solid rgba(0,0,0,0.05);">
                                <strong>{{ $w->amount_ml }}ml</strong>
                                <form method="post" action="{{ route('dashboard') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" name="water_delete" value="{{ $w->id }}" title="Excluir" style="color: #ff3b30; border: none; background: none; cursor: pointer; padding: 0; font-size: 1.1rem; line-height: 1; vertical-align: middle;">&times;</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>

        <div class="actions-inline" style="margin-top: 1.25rem;">
            <a class="btn btn-primary" href="{{ route('diary') }}">Registrar refeição</a>
            <a class="btn btn-ghost" href="{{ route('exercise') }}">Registrar exercício</a>
        </div>
@endsection
