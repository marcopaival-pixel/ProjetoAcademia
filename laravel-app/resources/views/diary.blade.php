@extends('layouts.app', ['navCurrent' => 'diary'])

@section('title', 'Alimentação')

@section('content')
        <h1>Diário alimentar</h1>
        <p class="lead">Registre refeições e veja o total do dia.</p>

        @if (!empty($notice))
            <div class="alert alert-success">{{ $notice }}</div>
        @endif
        @if (!empty($error))
            <div class="alert alert-error">{{ $error }}</div>
        @endif

        <form method="get" class="form-group" style="margin-bottom: 1.25rem;" action="{{ route('diary') }}">
            <label for="date">Dia</label>
            <input id="date" name="date" type="date" value="{{ $date }}" onchange="this.form.submit()">
        </form>

        <div class="grid grid-2">
            <div class="card">
                <h2>{{ $editRow ? 'Editar registro' : 'Adicionar' }}</h2>
                @if ($editRow)
                    <p class="muted" style="margin:0 0 1rem; font-size:0.875rem;">
                        <a href="{{ route('diary', ['date' => $date]) }}">Cancelar edição</a>
                    </p>
                @endif
                <form method="post" action="{{ route('diary') }}" novalidate>
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if ($editRow)
                        <input type="hidden" name="food_edit_id" value="{{ $editRow->id }}">
                    @endif
                    <div class="form-group">
                        <label for="meal_type">Refeição</label>
                        <select id="meal_type" name="meal_type">
                            @foreach ($mealLabels as $k => $lab)
                                <option value="{{ $k }}" @selected($formMeal === $k)>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="food_name">Alimento</label>
                        <input id="food_name" name="food_name" type="text" required maxlength="200" placeholder="Ex.: Arroz integral" value="{{ old('food_name', $editRow->food_name ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="calories">Calorias (kcal)</label>
                        <input id="calories" name="calories" type="number" min="0" max="20000" required value="{{ old('calories', $editRow ? $editRow->calories : '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="protein_g">Proteína (g)</label>
                        <input id="protein_g" name="protein_g" type="number" min="0" step="0.1" value="{{ old('protein_g', $editRow ? $editRow->protein_g : '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="carbs_g">Carboidrato (g)</label>
                        <input id="carbs_g" name="carbs_g" type="number" min="0" step="0.1" value="{{ old('carbs_g', $editRow ? $editRow->carbs_g : '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="fat_g">Gordura (g)</label>
                        <input id="fat_g" name="fat_g" type="number" min="0" step="0.1" value="{{ old('fat_g', $editRow ? $editRow->fat_g : '0') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $editRow ? 'Atualizar' : 'Salvar' }}</button>
                </form>
                @if (!$editRow)
                    <div class="copy-day-form">
                        <h3 class="muted" style="margin:0 0 0.5rem; font-size:0.9375rem; font-weight:600;">Copiar de outro dia</h3>
                        <form method="post" action="{{ route('diary') }}" class="form-inline">
                            @csrf
                            <input type="hidden" name="action" value="copy_day">
                            <input type="hidden" name="target_date" value="{{ $date }}">
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="source_date">Origem</label>
                                <input id="source_date" name="source_date" type="date" required>
                            </div>
                            <button type="submit" class="btn btn-ghost btn-sm">Copiar para {{ \Carbon\Carbon::parse($date)->format('d/m') }}</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="card">
                <h2>Totais do dia</h2>
                <p style="margin:0 0 0.5rem;"><strong>{{ $sumCal }} kcal</strong></p>
                <p class="muted" style="margin:0;">P {{ number_format($sumP, 1, ',', '.') }} g ·
                    C {{ number_format($sumC, 1, ',', '.') }} g ·
                    F {{ number_format($sumF, 1, ',', '.') }} g</p>
                @if ($hasMacroTargets)
                    <div class="macro-grid" style="margin-top:1rem; gap:0.75rem;">
                        @foreach ([['P','Proteína',$sumP,$macroTargets['p'] ?? null,'#3d9cf5'],['C','Carbo',$sumC,$macroTargets['c'] ?? null,'#34c759'],['G','Gordura',$sumF,$macroTargets['f'] ?? null,'#ff9f0a']] as $row)
                            @php
                                [$ab, $lb, $cur, $tgt, $col] = $row;
                                $pc = \App\Support\Macro::barPercent($cur, $tgt);
                            @endphp
                            @if ($pc !== null)
                                <div class="macro-item">
                                    <div class="macro-item-head">
                                        <span class="macro-abbr" style="color:{{ $col }}">{{ $ab }}</span>
                                        <span class="macro-label">{{ $lb }}</span>
                                    </div>
                                    <div class="macro-bar" style="--macro-fill: {{ $col }}"><span style="width: {{ $pc }}%;"></span></div>
                                    <p class="macro-stat muted" style="margin:0.25rem 0 0;">{{ number_format($cur, 1, ',', '.') }} / {{ number_format((float)$tgt, 1, ',', '.') }} g</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                @if ($rows->isEmpty())
                    <p class="empty-state">Nada registrado neste dia.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Refeição</th>
                                    <th>Item</th>
                                    <th>kcal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $r)
                                    <tr>
                                        <td>{{ $mealLabels[$r->meal_type] ?? $r->meal_type }}</td>
                                        <td>{{ $r->food_name }}</td>
                                        <td>{{ $r->calories }}</td>
                                        <td class="td-fit">
                                            <div class="row-actions">
                                                <a class="btn btn-ghost btn-sm" href="{{ route('diary', ['date' => $date, 'edit' => $r->id]) }}">Editar</a>
                                                <form method="post" action="{{ route('diary') }}" class="form-row-delete" onsubmit="return confirm('Remover este item?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="delete_food">
                                                    <input type="hidden" name="entry_date" value="{{ $date }}">
                                                    <input type="hidden" name="food_id" value="{{ $r->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
