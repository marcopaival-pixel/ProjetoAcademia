@extends('layouts.app', ['navCurrent' => 'profile'])

@section('title', 'Perfil')

@section('content')
        <h1>Perfil</h1>
        <p class="lead">Altura, idade e peso recente permitem estimar TMB e gasto (TDEE); você pode fixar a meta manualmente ou calcular ao salvar.</p>

        @if (!empty($notice))
            <div class="alert alert-success">{{ $notice }}</div>
        @endif
        @if (!empty($error))
            <div class="alert alert-error">{{ $error }}</div>
        @endif

        @if ($calPreview !== null)
            <div class="card" style="max-width: 32rem; margin-bottom: 1rem;">
                <h2 style="margin-top:0;">Prévia da estimativa</h2>
                <p class="muted" style="margin:0 0 0.75rem; font-size:0.9rem;">
                    Com base no peso de <strong>{{ number_format($calPreview['weight_kg'], 1, ',', '.') }} kg</strong>
                    ({{ \Carbon\Carbon::parse($calPreview['weighed_at'])->translatedFormat('d/m/Y') }}),
                    idade {{ $calPreview['age'] }} anos e dados abaixo:
                </p>
                <ul class="muted" style="margin:0; padding-left:1.25rem; font-size:0.9rem;">
                    <li>TMB (Mifflin–St Jeor): ≈ {{ (int) round($calPreview['bmr']) }} kcal/dia</li>
                    <li>Gasto estimado (TDEE): ≈ {{ (int) round($calPreview['tdee']) }} kcal/dia</li>
                    <li>Meta sugerida para o objetivo atual: <strong>{{ $calPreview['target'] }} kcal/dia</strong></li>
                </ul>
                <p class="muted" style="margin:0.75rem 0 0; font-size:0.8rem;">Referência geral; não substitui orientação de nutricionista ou médico.</p>
            </div>
        @endif

        <div class="card" style="max-width: 32rem;">
            <form method="post" action="{{ route('profile') }}" novalidate>
                @csrf
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input id="name" name="name" type="text" required maxlength="120" value="{{ old('name', $u->name) }}">
                </div>
                <p class="muted" style="margin: 0 0 1rem; font-size: 0.875rem;">Email: <strong>{{ $u->email }}</strong> (alteração em breve)</p>
                <div class="form-group">
                    <label for="birth_date">Nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $u->birth_date) }}">
                </div>
                <div class="form-group">
                    <label for="sex">Sexo (opcional)</label>
                    <select id="sex" name="sex">
                        <option value="" @selected(old('sex', $u->sex) === '')>—</option>
                        <option value="M" @selected(old('sex', $u->sex) === 'M')>Masculino</option>
                        <option value="F" @selected(old('sex', $u->sex) === 'F')>Feminino</option>
                        <option value="O" @selected(old('sex', $u->sex) === 'O')>Outro / prefiro não informar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="height_cm">Altura (cm)</label>
                    <input id="height_cm" name="height_cm" type="number" min="50" max="260" placeholder="ex.: 170" value="{{ old('height_cm', $u->height_cm) }}">
                </div>
                <div class="form-group">
                    <label for="activity_level">Nível de atividade</label>
                    <select id="activity_level" name="activity_level">
                        @foreach ([
                            'sedentary' => 'Sedentário', 'light' => 'Leve', 'moderate' => 'Moderado',
                            'active' => 'Ativo', 'very_active' => 'Muito ativo',
                        ] as $val => $lab)
                            <option value="{{ $val }}" @selected(old('activity_level', $u->activity_level ?? 'moderate') === $val)>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="goal">Objetivo</label>
                    <select id="goal" name="goal">
                        @foreach (['lose' => 'Perder peso', 'gain' => 'Ganhar peso', 'maintain' => 'Manter peso'] as $val => $lab)
                            <option value="{{ $val }}" @selected(old('goal', $u->goal ?? 'maintain') === $val)>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="daily_calorie_target">Meta calórica diária (kcal)</label>
                    <input id="daily_calorie_target" name="daily_calorie_target" type="number" min="500" max="20000" placeholder="ex.: 2000" value="{{ old('daily_calorie_target', $u->daily_calorie_target) }}">
                </div>
                @if ($isPremium)
                <p class="muted" style="margin: -0.5rem 0 0.75rem; font-size:0.875rem;">Metas de macros (opcional) — usadas no painel <strong>Hoje</strong> e nos totais do diário.</p>
                <div class="form-group">
                    <label for="protein_target_g">Meta proteína (g/dia)</label>
                    <input id="protein_target_g" name="protein_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 120" value="{{ old('protein_target_g', $u->protein_target_g) }}">
                </div>
                <div class="form-group">
                    <label for="carbs_target_g">Meta carboidrato (g/dia)</label>
                    <input id="carbs_target_g" name="carbs_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 200" value="{{ old('carbs_target_g', $u->carbs_target_g) }}">
                </div>
                <div class="form-group">
                    <label for="fat_target_g">Meta gordura (g/dia)</label>
                    <input id="fat_target_g" name="fat_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 60" value="{{ old('fat_target_g', $u->fat_target_g) }}">
                </div>
                @else
                <div class="premium-gate" style="margin-top: 1rem; min-height: 13.5rem;">
                    <div class="premium-gate__inner">
                        <p class="muted" style="margin:0 0 0.75rem; font-size:0.875rem;">Metas de macros no plano grátis seguem uma repartição padrão (25% proteína / 45% carboidrato / 30% gordura das suas kcal). <strong>Assine Premium</strong> para definir gramas manualmente (ideal para musculação e acompanhamento fino).</p>
                        @if ($freeMacroPrev !== null)
                            <div class="form-group" style="margin-bottom:0.5rem;"><span class="muted">Proteína (calculada)</span><br><strong class="tabular-nums">{{ $freeMacroPrev['p'] }} g/dia</strong></div>
                            <div class="form-group" style="margin-bottom:0.5rem;"><span class="muted">Carboidrato (calculado)</span><br><strong class="tabular-nums">{{ $freeMacroPrev['c'] }} g/dia</strong></div>
                            <div class="form-group" style="margin-bottom:0;"><span class="muted">Gordura (calculada)</span><br><strong class="tabular-nums">{{ $freeMacroPrev['f'] }} g/dia</strong></div>
                        @else
                            <p class="muted" style="margin:0;">Defina uma meta calórica acima para ver as metas de macro automáticas.</p>
                        @endif
                    </div>
                    <div class="premium-gate__overlay">
                        <span class="premium-gate__crown" aria-hidden="true">👑</span>
                        <p class="premium-gate__head">Recurso Premium</p>
                        <p class="premium-gate__sub">Edite proteína, carbo e gorda como quiser.</p>
                        <a class="btn btn-primary" href="{{ route('plano') }}">Assinar</a>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <label for="water_target_ml">Meta de Água (ml/dia)</label>
                    <input id="water_target_ml" name="water_target_ml" type="number" min="500" max="10000" step="100" placeholder="ex.: 2000" value="{{ old('water_target_ml', $u->water_target_ml) }}">
                </div>
                <div class="form-group">
                    <label style="display:flex; align-items:flex-start; gap:0.5rem; cursor:pointer; max-width:100%;">
                        <input type="checkbox" name="auto_calorie" value="1" style="width:auto; margin-top:0.2rem;" @checked(old('auto_calorie'))>
                        <span>Ao salvar, <strong>calcular meta automaticamente</strong> (TMB + TDEE + objetivo), usando o peso mais recente.</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>

        <div class="card" style="max-width: 32rem; margin-top: 1.25rem;">
            <h2 style="margin-top:0;">Alterar senha</h2>
            <form method="post" action="{{ route('profile') }}" novalidate autocomplete="off">
                @csrf
                <input type="hidden" name="profile_action" value="password">
                <div class="form-group">
                    <label for="current_password">Senha atual</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nova senha (mín. 8 caracteres)</label>
                    <input id="new_password" name="new_password" type="password" autocomplete="new-password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Confirmar nova senha</label>
                    <input id="new_password_confirm" name="new_password_confirm" type="password" autocomplete="new-password" required minlength="8">
                </div>
                @if ($errors->has('current_password') || $errors->has('new_password'))
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="btn btn-primary">Atualizar senha</button>
            </form>
        </div>
@endsection
