@extends('layouts.app', ['navCurrent' => 'diary'])

@section('title', 'Alimentação')

@section('content')
        @php
            $mealIcons = [
                'breakfast' => '☕',
                'lunch'     => '🥗',
                'dinner'    => '🍲',
                'snack'     => '🍎',
                'other'     => '🥄'
            ];
            $rows = collect($rows);
        @endphp
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
            <div>
                <h1 style="margin:0;">Diário Alimentar</h1>
                <p class="muted" style="margin:0;">Gerencie sua nutrição para o dia {{ \Carbon\Carbon::parse($date)->format('d/m') }}</p>
            </div>
            <form method="get" action="{{ route('diary') }}" id="date-form">
                <input id="date" name="date" type="date" value="{{ $date }}" onchange="this.form.submit()" style="max-width: 150px; background: var(--surface); border-color: var(--border);">
            </form>
        </div>

        @if (!empty($notice) || !empty($error))
            <div style="margin-bottom: 1.5rem;">
                @if (!empty($notice)) <div class="alert alert-success">{{ $notice }}</div> @endif
                @if (!empty($error)) <div class="alert alert-error">{{ $error }}</div> @endif
            </div>
        @endif

        <div class="grid" style="grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: start;">
            <!-- LADO ESQUERDO: FORMULÁRIO -->
            <div class="card glass" style="position: sticky; top: 1.5rem;">
                <h2 style="margin-top:0; display:flex; align-items:center; gap:0.5rem;">
                    @if($editRow) ✏️ Editar @else ✨ Adicionar @endif
                </h2>
                
                @if ($editRow)
                    <a href="{{ route('diary', ['date' => $date]) }}" class="btn btn-ghost btn-sm" style="margin-bottom:1rem; display:inline-block;">Cancelar edição</a>
                @endif

                <form method="post" action="{{ route('diary') }}" novalidate>
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if ($editRow) <input type="hidden" name="food_edit_id" value="{{ $editRow->id }}"> @endif

                    <div class="form-group">
                        <label for="meal_type">Momento do dia</label>
                        <select id="meal_type" name="meal_type">
                            @foreach ($mealLabels as $k => $lab)
                                <option value="{{ $k }}" @selected($formMeal === $k)>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group autocomplete-wrapper">
                        <label for="food_name">Nome do Alimento</label>
                        <input id="food_name" name="food_name" type="text" required maxlength="200" autocomplete="off" placeholder="Ex.: Whey Protein" value="{{ old('food_name', $editRow->food_name ?? '') }}">
                        <div id="autocomplete-results" class="autocomplete-suggestions"></div>
                    </div>

                    <div class="form-group" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <div style="flex: 2; min-width: 8rem;">
                            <label for="amount">Quantidade consumida</label>
                            <input id="amount" name="amount" type="number" step="0.1" min="0" placeholder="Ex: 150" value="{{ old('amount', $editRow ? (float)$editRow->amount : '100') }}">
                        </div>
                        <div style="flex: 1; min-width: 5rem;">
                            <label for="unit">Unidade</label>
                            <select id="unit" name="unit">
                                <option value="g" @selected(($editRow->unit ?? 'g') == 'g')>g</option>
                                <option value="ml" @selected(($editRow->unit ?? 'g') == 'ml')>ml</option>
                                <option value="unid" @selected(($editRow->unit ?? 'g') == 'unid')>unid</option>
                            </select>
                        </div>
                    </div>

                    {{-- Valores base para o cálculo automático (sempre por 100g ou 1 unidade) --}}
                    <input type="hidden" id="base_calories" value="{{ $editRow ? ($editRow->calories / max(1, (float)$editRow->amount/100)) : '0' }}">
                    <input type="hidden" id="base_protein" value="{{ $editRow ? ($editRow->protein_g / max(1, (float)$editRow->amount/100)) : '0' }}">
                    <input type="hidden" id="base_carbs" value="{{ $editRow ? ($editRow->carbs_g / max(1, (float)$editRow->amount/100)) : '0' }}">
                    <input type="hidden" id="base_fat" value="{{ $editRow ? ($editRow->fat_g / max(1, (float)$editRow->amount/100)) : '0' }}">

                    <!-- BUSCA OFF -->
                    <details class="off-lookup" style="margin-bottom: 1.5rem; border: 1px solid var(--border); border-radius: 12px; background: rgba(255,255,255,0.02);">
                        <summary style="padding: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; color: var(--accent);">🔍 Buscar na Base OFF</summary>
                        <div style="padding: 1rem; border-top: 1px solid var(--border);">
                            <div class="form-group">
                                <input type="text" id="off-barcode" inputmode="numeric" placeholder="Código de barras..." style="margin-bottom: 0.5rem;">
                                <button type="button" class="btn btn-ghost btn-sm" id="off-barcode-btn" style="width:100%;">Buscar EAN</button>
                            </div>
                            <div class="form-group" style="margin-bottom:0.5rem;">
                                <input type="search" id="off-q" placeholder="Nome do produto..." style="margin-bottom: 0.5rem;">
                                <button type="button" class="btn btn-ghost btn-sm" id="off-search-btn" style="width:100%;">Pesquisar Nome</button>
                            </div>
                            <div id="off-results" style="margin-top:0.75rem; font-size: 0.8125rem;"></div>
                        </div>
                    </details>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div class="form-group" style="margin:0;">
                            <label for="calories">Calorias (kcal)</label>
                            <input id="calories" name="calories" type="number" min="0" required value="{{ old('calories', $editRow ? $editRow->calories : '0') }}">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label for="protein_g">Proteína (g)</label>
                            <input id="protein_g" name="protein_g" type="number" min="0" step="0.1" value="{{ old('protein_g', $editRow ? $editRow->protein_g : '0') }}">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label for="carbs_g">Carbo (g)</label>
                            <input id="carbs_g" name="carbs_g" type="number" min="0" step="0.1" value="{{ old('carbs_g', $editRow ? $editRow->carbs_g : '0') }}">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label for="fat_g">Gordura (g)</label>
                            <input id="fat_g" name="fat_g" type="number" min="0" step="0.1" value="{{ old('fat_g', $editRow ? $editRow->fat_g : '0') }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.75rem;">{{ $editRow ? 'Atualizar Registro' : 'Lançar no Diário' }}</button>
                </form>

                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border);">

                @if (!$editRow)
                    <div class="copy-section">
                        <p class="muted" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.75rem;">Ações Rápidas</p>
                        <form method="post" action="{{ route('diary') }}" style="display: flex; gap: 0.5rem; align-items: flex-end;">
                            @csrf
                            <input type="hidden" name="action" value="copy_day">
                            <input type="hidden" name="target_date" value="{{ $date }}">
                            <div style="flex:1;">
                                <label style="font-size: 0.75rem; color: var(--muted);">Copiar de:</label>
                                <input id="source_date" name="source_date" type="date" required style="padding: 0.35rem 0.65rem; font-size: 0.875rem;">
                            </div>
                            <button type="submit" class="btn btn-ghost btn-sm" style="height: 38px;">Copiar</button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- LADO DIREITO: RESUMO E LISTA -->
            <div>
                <!-- CARD DE RESUMO CALÓRICO -->
                <div class="summary-card" style="border-radius: 20px;">
                    <div class="calorie-balance">
                        <span class="calorie-balance__label">Restante Hoje</span>
                        <span class="calorie-balance__value tabular-nums">
                            @if($calorieTarget)
                                {{ number_format($calorieTarget - $sumCal, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>

                    <div class="calorie-split">
                        <div class="calorie-split__item">
                            <span class="calorie-split__val">{{ number_format($calorieTarget ?? 0, 0, ',', '.') }}</span>
                            <span class="calorie-split__lab">Meta</span>
                        </div>
                        <div class="calorie-split__item">
                            <span class="calorie-split__val" style="color: var(--accent);">{{ number_format($sumCal, 0, ',', '.') }}</span>
                            <span class="calorie-split__lab">Consumidas</span>
                        </div>
                    </div>

                    <div class="macro-ring-grid">
                        @foreach ([['P','Prot',$sumP,$macroTargets['p'] ?? null,'#3d9cf5'],['C','Carb',$sumC,$macroTargets['c'] ?? null,'#34c759'],['G','Gord',$sumF,$macroTargets['f'] ?? null,'#ff9f0a']] as $r)
                            @php [$ab, $lb, $cur, $tgt, $col] = $r; @endphp
                            <div class="macro-pill">
                                <span class="macro-pill__lab" style="color:{{$col}}">{{$lb}}</span>
                                <span class="macro-pill__val tabular-nums">{{ number_format($cur, 1, ',', '.') }}@if($tgt)<small class="muted" style="font-weight:400;">/{{ (int)$tgt }}</small>@endif</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- LISTA DE ALIMENTOS AGRUPADA -->
                @if (count($rows) === 0)
                    <div class="card" style="text-align: center; padding: 3rem; border-style: dashed; background: transparent;">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;">🍎</span>
                        <p class="muted">Nenhum alimento registrado para este dia.</p>
                        <p style="font-size: 0.875rem;">Comece adicionando algo no formulário ao lado.</p>
                    </div>
                @else
                    @foreach ($mealLabels as $mKey => $mLabel)
                        @php $mealRows = $rows->where('meal_type', $mKey); @endphp
                        @if ($mealRows->count() > 0)
                            <div class="meal-group">
                                <h3 class="meal-title">
                                    <span>@isset($mealIcons[$mKey]) {{ $mealIcons[$mKey] }} @else 🥄 @endisset</span>
                                    {{ $mLabel }}
                                    <span class="muted" style="font-size: 0.875rem; font-weight: 400; margin-left: auto;">
                                        {{ $mealRows->sum('calories') }} kcal
                                    </span>
                                </h3>

                                @foreach ($mealRows as $row)
                                    <div class="food-card">
                                        <div class="food-card__info">
                                            <span class="food-card__name">
                                                {{ $row->food_name }}
                                                @if(!empty($row->amount))
                                                    <small class="muted" style="font-weight:400; font-size:0.875rem;">({{ (float)$row->amount }}{{ $row->unit }})</small>
                                                @endif
                                            </span>
                                            <div class="food-card__macros">
                                                <div class="food-card__macro-item"><span>P:</span> <strong>{{ number_format($row->protein_g, 1, ',', '.') }}g</strong></div>
                                                <div class="food-card__macro-item"><span>C:</span> <strong>{{ number_format($row->carbs_g, 1, ',', '.') }}g</strong></div>
                                                <div class="food-card__macro-item"><span>G:</span> <strong>{{ number_format($row->fat_g, 1, ',', '.') }}g</strong></div>
                                            </div>
                                        </div>
                                        <div class="food-card__energy">
                                            <span class="food-card__kcal tabular-nums">{{ $row->calories }}</span>
                                            <span class="food-card__unit">kcal</span>
                                        </div>
                                        <div class="action-row">
                                            <a class="btn-icon" href="{{ route('diary', ['date' => $date, 'edit' => $row->id]) }}" title="Editar">✏️</a>
                                            <form method="post" action="{{ route('diary') }}" style="margin:0;" onsubmit="return confirm('Excluir este item?');">
                                                @csrf
                                                <input type="hidden" name="action" value="delete_food">
                                                <input type="hidden" name="entry_date" value="{{ $date }}">
                                                <input type="hidden" name="food_id" value="{{ $row->id }}">
                                                <button type="submit" class="btn-icon btn-icon--danger" title="Excluir">🗑️</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

<style>
.autocomplete-wrapper { position: relative; }
.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    max-height: 250px;
    overflow-y: auto;
    display: none;
}
.autocomplete-suggestion {
    padding: 0.65rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid var(--border);
}
.autocomplete-suggestion:last-child { border-bottom: none; }
.autocomplete-suggestion:hover { background: color-mix(in oklab, var(--accent) 10%, var(--surface)); }
</style>

<script>
(function () {
    const searchUrl = @json(route('food.search'));
    const productBase = @json(rtrim(url('/api/food/product'), '/'));
    const qEl = document.getElementById('off-q');
    const btn = document.getElementById('off-search-btn');
    const barcodeEl = document.getElementById('off-barcode');
    const barcodeBtn = document.getElementById('off-barcode-btn');
    const out = document.getElementById('off-results');
    const foodName = document.getElementById('food_name');
    const calEl = document.getElementById('calories');
    const pEl = document.getElementById('protein_g');
    const cEl = document.getElementById('carbs_g');
    const fEl = document.getElementById('fat_g');
    if (!btn || !barcodeBtn || !barcodeEl || !out || !foodName) return;

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    async function jsonOrEmpty(r) {
        try { return await r.json(); } catch (e) { return {}; }
    }

    const baseCal = document.getElementById('base_calories');
    const baseP = document.getElementById('base_protein');
    const baseC = document.getElementById('base_carbs');
    const baseF = document.getElementById('base_fat');
    const amountVal = document.getElementById('amount');
    const unitSelect = document.getElementById('unit');

    function applyProductToForm(x) {
        let label = x.name;
        if (x.brands) {
            label += ' — ' + x.brands;
        }
        if (label.length > 200) {
            label = label.slice(0, 197) + '…';
        }
        foodName.value = label;
        
        // Guardamos os valores BASE (sempre assumindo 100g para OFF)
        baseCal.value = x.calories;
        baseP.value = x.protein_g;
        baseC.value = x.carbs_g;
        baseF.value = x.fat_g;

        // Se for pesquisa OFF, a porção padrão é 100g
        amountVal.value = 100;
        unitSelect.value = 'g';
        
        recalculate();
    }

    function recalculate() {
        const amt = parseFloat(amountVal.value) || 0;
        // Fórmula: (Qtd / 100) * Valor Base do Produto
        const factor = amt / 100;
        
        if (baseCal.value > 0 || baseP.value > 0 || baseC.value > 0 || baseF.value > 0) {
            calEl.value = Math.round(parseFloat(baseCal.value) * factor);
            pEl.value = (parseFloat(baseP.value) * factor).toFixed(1);
            cEl.value = (parseFloat(baseC.value) * factor).toFixed(1);
            fEl.value = (parseFloat(baseF.value) * factor).toFixed(1);
        }
    }

    // Escuta mudanças no campo de quantidade
    amountVal.addEventListener('input', recalculate);
    unitSelect.addEventListener('change', recalculate);

    async function fetchProductAndApply(codeDigits) {
        out.innerHTML = '<p class="muted" style="margin:0;">A carregar produto…</p>';
        try {
            const pr = await fetch(productBase + '/' + encodeURIComponent(codeDigits), { headers: { 'Accept': 'application/json' } });
            const pd = await jsonOrEmpty(pr);
            if (pr.status === 429) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">Muitos pedidos de consulta. Aguarde cerca de um minuto.</p>';
                return;
            }
            if (!pr.ok) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">' + esc(pd.message || pd.error || ('Erro ' + pr.status)) + '</p>';
                return;
            }
            if (!pd.ok) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">' + esc(pd.error || 'Erro.') + '</p>';
                return;
            }
            applyProductToForm(pd.product);
            out.innerHTML = '<p class="alert alert-success" style="margin:0;">Campos preenchidos (' + esc(pd.product.basis || '100 g') + '). Revise antes de guardar.</p>';
        } catch (e) {
            out.innerHTML = '<p class="alert alert-error" style="margin:0;">Falha de rede ao carregar o produto.</p>';
        }
    }

    barcodeBtn.addEventListener('click', async function (e) {
        if (e) e.preventDefault();
        const raw = barcodeEl && barcodeEl.value ? barcodeEl.value : '';
        const digits = String(raw).replace(/\D/g, '');
        if (digits.length < 8) {
            out.innerHTML = '<p class="alert alert-error" style="margin:0;">Código inválido. Indique pelo menos 8 dígitos (EAN).</p>';
            return;
        }
        await fetchProductAndApply(digits);
    });

    barcodeEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            barcodeBtn.click();
        }
    });


    btn.addEventListener('click', async function (e) {
        if (e) e.preventDefault();
        const q = (qEl && qEl.value) ? qEl.value.trim() : '';
        if (q.length < 2) {
            out.innerHTML = '<p class="alert alert-error" style="margin:0;">Indique pelo menos 2 caracteres.</p>';
            return;
        }
        out.innerHTML = '<p class="muted" style="margin:0;">A pesquisar…</p>';
        try {
            const r = await fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
            const d = await jsonOrEmpty(r);
            if (r.status === 429) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">Muitos pedidos de consulta. Aguarde cerca de um minuto e tente novamente.</p>';
                return;
            }
            if (!r.ok) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">' + esc(d.message || d.error || ('Erro ' + r.status)) + '</p>';
                return;
            }
            if (!d.ok) {
                out.innerHTML = '<p class="alert alert-error" style="margin:0;">' + esc(d.error || 'Erro na pesquisa.') + '</p>';
                return;
            }
            const list = d.products || [];
            if (list.length === 0) {
                out.innerHTML = '<p class="muted" style="margin:0;">Nenhum resultado. Experimente outra palavra.</p>';
                return;
            }
            let html = '<ul style="list-style:none; margin:0; padding:0;">';
            list.forEach(function (p) {
                html += '<li style="margin-bottom:0.5rem; display:flex; flex-wrap:wrap; gap:0.35rem; align-items:center;">';
                html += '<span style="flex:1; min-width:10rem;">' + esc(p.name) + (p.brands ? ' <span class="muted">(' + esc(p.brands) + ')</span>' : '') + '</span>';
                html += '<button type="button" class="btn btn-ghost btn-sm off-pick" data-code="' + esc(p.code) + '">Usar no formulário</button>';
                html += '</li>';
            });
            html += '</ul>';
            out.innerHTML = html;
            out.querySelectorAll('.off-pick').forEach(function (b) {
                b.addEventListener('click', async function (ee) {
                    if (ee) ee.preventDefault();
                    const code = (b.getAttribute('data-code') || '').replace(/\D/g, '');
                    if (code.length < 8) {
                        out.innerHTML = '<p class="alert alert-error" style="margin:0;">Código de produto inválido.</p>';
                        return;
                    }
                    await fetchProductAndApply(code);
                });
            });
        } catch (e) {
            out.innerHTML = '<p class="alert alert-error" style="margin:0;">Falha de rede.</p>';
        }
    });

    const acBox = document.getElementById('autocomplete-results');
    let acTimer = null;

    if (foodName && acBox) {
        foodName.addEventListener('input', function() {
            clearTimeout(acTimer);
            const val = foodName.value.trim();
            if (val.length < 3) {
                acBox.innerHTML = '';
                acBox.style.display = 'none';
                return;
            }

            acTimer = setTimeout(async () => {
                try {
                    const rs = await fetch(searchUrl + '?q=' + encodeURIComponent(val), { headers: { 'Accept': 'application/json' } });
                    if (!rs.ok) return;
                    const ds = await rs.json();
                    if (!ds.ok || !ds.products || ds.products.length === 0) {
                        acBox.innerHTML = '';
                        acBox.style.display = 'none';
                        return;
                    }

                    let acHtml = '';
                    ds.products.slice(0, 8).forEach(p => {
                        const lbl = p.name + (p.brands ? ' (' + p.brands + ')' : '');
                        acHtml += `<div class="autocomplete-suggestion" data-code="${esc(p.code)}" data-label="${esc(lbl)}">${esc(lbl)}</div>`;
                    });
                    acBox.innerHTML = acHtml;
                    acBox.style.display = 'block';

                    acBox.querySelectorAll('.autocomplete-suggestion').forEach(div => {
                        div.addEventListener('click', () => {
                            const code = div.getAttribute('data-code');
                            acBox.innerHTML = '';
                            acBox.style.display = 'none';
                            if (code) {
                                fetchProductAndApply(code);
                            }
                        });
                    });
                } catch (e) {
                    console.error('Autocomplete error:', e);
                }
            }, 400);
        });

        document.addEventListener('click', (e) => {
            if (e.target !== foodName && e.target !== acBox) {
                acBox.innerHTML = '';
                acBox.style.display = 'none';
            }
        });
    }

    qEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btn.click();
        }
    });
})();
</script>
@endsection
