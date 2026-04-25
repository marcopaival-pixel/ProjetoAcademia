@extends('layouts.app', ['navCurrent' => 'profile'])

@section('title', 'Meu Perfil')

@section('content')
<div class="profile-header animate-fade-up" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; gap: 1.5rem; flex-wrap: wrap;">
    <div class="header-left">
        <h1 style="margin: 0; font-size: 2.5rem; letter-spacing: -0.02em;">Meu Perfil</h1>
        <p class="muted" style="margin-top: 0.5rem; font-size: 1.125rem;">Gerencie seus dados físicos, metas e configurações de saúde em um só lugar.</p>
    </div>
    <div class="header-right" style="background: var(--surface-glass); padding: 1.5rem 2rem; border-radius: 24px; border: 1px solid var(--border); display: flex; gap: 2rem; align-items: center; box-shadow: var(--shadow-sm); backdrop-filter: blur(8px);">
        <div class="stat-item" style="text-align: center;">
            <span class="muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 0.25rem;">Meta Atual</span>
            <strong style="font-size: 1.5rem; color: var(--primary);">{!! $u->daily_calorie_target ? $u->daily_calorie_target . ' <small style="font-size: 0.75rem;">kcal</small>' : '—' !!}</strong>
        </div>
        <div style="width: 1px; height: 30px; background: var(--border);"></div>
        <div class="stat-item" style="text-align: center;">
            <span class="muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 0.25rem;">Sua Idade</span>
            <strong style="font-size: 1.5rem;">{!! $age ? $age . ' <small style="font-size: 0.75rem;">anos</small>' : '—' !!}</strong>
        </div>
    </div>
</div>

@if (!empty($notice))
    <div class="alert alert-success animate-fade-up" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem;">✅</span> {{ $notice }}
    </div>
@endif

@if (!empty($error))
    <div class="alert alert-error animate-fade-up" style="margin-bottom: 2rem; border-radius: 16px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem;">⚠️</span> {{ $error }}
    </div>
@endif

@if ($calPreview !== null)
    <div class="card animate-fade-up" style="margin-bottom: 2rem; border-radius: 24px; padding: 2rem; border-left: 4px solid var(--primary); background: color-mix(in oklab, var(--primary) 5%, var(--surface));">
        <h2 style="margin-top:0; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            📊 Prévia da Estimativa
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div class="preview-item">
                <span class="muted" style="font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Taxa Metabólica Basal (TMB)</span>
                <strong style="font-size: 1.125rem;">{{ (int) round($calPreview['bmr']) }} kcal/dia</strong>
            </div>
            <div class="preview-item">
                <span class="muted" style="font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Gasto Total Estimado (TDEE)</span>
                <strong style="font-size: 1.125rem;">{{ (int) round($calPreview['tdee']) }} kcal/dia</strong>
            </div>
            <div class="preview-item">
                <span class="muted" style="font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Meta Sugerida</span>
                <strong style="font-size: 1.125rem; color: var(--primary);">{{ $calPreview['target'] }} kcal/dia</strong>
            </div>
        </div>
        <p class="muted" style="margin-top: 1.25rem; font-size: 0.8rem; font-style: italic;">Valores baseados no seu peso de {{ number_format($latestWeight, 1, ',', '.') }} kg em {{ \Carbon\Carbon::parse($calPreview['weighed_at'])->translatedFormat('d/m/Y') }}.</p>
    </div>
@endif

<form method="post" action="{{ route('profile') }}" novalidate class="animate-fade-up">
    @csrf
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(28rem, 1fr)); gap: 2rem;">
        
        <!-- CARD: DADOS PESSOAIS -->
        <div class="card" style="padding: 2.5rem; border-radius: 24px;">
            <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">👤 Dados Pessoais</div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name" style="font-weight: 600; font-size: 0.9rem;">Nome Completo</label>
                <input id="name" name="name" type="text" required maxlength="120" value="{{ old('name', $u->name) }}" style="padding: 0.75rem 1rem; border-radius: 12px;">
                <span class="muted" style="font-size: 0.8rem; margin-top: 0.5rem; display: block;">Email: {{ $u->email }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="birth_date" style="font-weight: 600; font-size: 0.9rem;">Nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" required value="{{ old('birth_date', $u->birth_date) }}" style="padding: 0.75rem 1rem; border-radius: 12px;">
                </div>
                <div class="form-group">
                    <label for="sex" style="font-weight: 600; font-size: 0.9rem;">Sexo</label>
                    <select id="sex" name="sex" required style="padding: 0.75rem 1rem; border-radius: 12px;">
                        <option value="" @selected(old('sex', $u->sex) === '')>Selecione...</option>
                        <option value="M" @selected(old('sex', $u->sex) === 'M')>Masculino</option>
                        <option value="F" @selected(old('sex', $u->sex) === 'F')>Feminino</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 1.5rem;">
                <label for="height_cm" style="font-weight: 600; font-size: 0.9rem;">Altura (cm)</label>
                <input id="height_cm" name="height_cm" type="number" min="50" max="260" placeholder="ex.: 175" value="{{ old('height_cm', $u->height_cm) }}" style="padding: 0.75rem 1rem; border-radius: 12px;">
            </div>
        </div>

        <!-- CARD: COMPOSIÇÃO E ATIVIDADE -->
        <div class="card" style="padding: 2.5rem; border-radius: 24px;">
            <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">⚖️ Composição @if(!$isPurePatient) e Atividade @endif</div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="current_weight_kg" style="font-weight: 600; font-size: 0.9rem;">Peso Atual (kg)</label>
                    <input id="current_weight_kg" name="current_weight_kg" type="number" step="0.1" min="20" max="500" placeholder="0.0" value="{{ old('current_weight_kg', $latestWeight) }}" style="padding: 0.75rem 1rem; border-radius: 12px; font-weight: 700; font-size: 1.1rem; color: var(--primary);">
                </div>
                @if(!$isPurePatient)
                <div class="form-group">
                    <label for="target_weight_kg" style="font-weight: 600; font-size: 0.9rem;">Peso Objetivo (kg)</label>
                    <input id="target_weight_kg" name="target_weight_kg" type="number" step="0.1" min="20" max="500" placeholder="0.0" value="{{ old('target_weight_kg', $u->target_weight_kg) }}" style="padding: 0.75rem 1rem; border-radius: 12px; font-weight: 700; font-size: 1.1rem;">
                </div>
                @endif
            </div>

            @if(!$isPurePatient)
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="training_days_per_week" style="font-weight: 600; font-size: 0.9rem;">Frequência de Treino</label>
                <select id="training_days_per_week" name="training_days_per_week" style="padding: 0.75rem 1rem; border-radius: 12px;">
                    <option value="" @selected(old('training_days_per_week', $u->training_days_per_week) === '')>Quantos dias por semana?</option>
                    <option value="1-2" @selected(old('training_days_per_week', $u->training_days_per_week) === '1-2')>1-2 dias na semana</option>
                    <option value="3-4" @selected(old('training_days_per_week', $u->training_days_per_week) === '3-4')>3-4 dias na semana</option>
                    <option value="5-6" @selected(old('training_days_per_week', $u->training_days_per_week) === '5-6')>5-6 dias na semana</option>
                    <option value="all" @selected(old('training_days_per_week', $u->training_days_per_week) === 'all')>Treino todos os dias</option>
                </select>
            </div>

            <div class="form-group">
                <label for="activity_level" style="font-weight: 600; font-size: 0.9rem;">Nível de Atividade Diário</label>
                <select id="activity_level" name="activity_level" style="padding: 0.75rem 1rem; border-radius: 12px;">
                    @foreach ([
                        'sedentary' => 'Sedentário (Pouco movimento)',
                        'light' => 'Leve (Exercício ocasional)',
                        'moderate' => 'Moderado (Ativo regularmente)',
                        'active' => 'Ativo (Treino intenso diário)',
                        'very_active' => 'Muito Ativo (Atleta/Trabalho pesado)',
                    ] as $val => $lab)
                        <option value="{{ $val }}" @selected(old('activity_level', $u->activity_level ?? 'moderate') === $val)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <div style="background: var(--surface-glass); padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border); text-align: center; margin-top: 1rem;">
                    <p class="muted" style="font-size: 0.85rem;">Torne-se <strong>Aluno</strong> para desbloquear metas de treino e atividade.</p>
                </div>
            @endif
        </div>

        @if(!$isPurePatient)
        <!-- CARD: METAS E ESTILO DE VIDA -->
        <div class="card" style="padding: 2.5rem; border-radius: 24px;">
            <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">🎯 Metas de Saúde</div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="goal" style="font-weight: 600; font-size: 0.9rem;">Objetivo Principal</label>
                <select id="goal" name="goal" style="padding: 0.75rem 1rem; border-radius: 12px; font-weight: 600;">
                    <option value="lose" @selected(old('goal', $u->goal) === 'lose')>🔥 Perder Peso / Emagrecer</option>
                    <option value="maintain" @selected(old('goal', $u->goal) === 'maintain')>⚖️ Manter Peso Atual</option>
                    <option value="gain" @selected(old('goal', $u->goal) === 'gain')>💪 Ganhar Massa Muscular</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1.5rem; align-items: start;">
                <div class="form-group">
                    <label for="daily_calorie_target" style="font-weight: 600; font-size: 0.9rem;">Meta Calórica Diária (kcal)</label>
                    <input id="daily_calorie_target" name="daily_calorie_target" type="number" min="500" max="20000" placeholder="ex.: 2200" value="{{ old('daily_calorie_target', $u->daily_calorie_target) }}" style="padding: 0.75rem 1rem; border-radius: 12px; font-weight: 700; font-size: 1.25rem;">
                </div>
                <div class="form-group">
                    <label for="water_target_ml" style="font-weight: 600; font-size: 0.9rem;">Meta Água (ml)</label>
                    <input id="water_target_ml" name="water_target_ml" type="number" min="500" max="10000" step="100" placeholder="2500" value="{{ old('water_target_ml', $u->water_target_ml) }}" style="padding: 0.75rem 1rem; border-radius: 12px;">
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="climate" style="font-weight: 600; font-size: 0.9rem;">Clima Local</label>
                <select id="climate" name="climate" style="padding: 0.75rem 1rem; border-radius: 12px;">
                    <option value="cold" @selected(old('climate', $u->climate) === 'cold')>❄️ Clima Frio</option>
                    <option value="moderate" @selected(old('climate', $u->climate ?? 'moderate') === 'moderate')>☁️ Clima Agradável</option>
                    <option value="hot" @selected(old('climate', $u->climate) === 'hot')>☀️ Clima Quente</option>
                </select>
            </div>
        </div>
        @endif

        <!-- CARD: AUTOMAÇÃO @if($isPurePatient) BLOQUEADA @endif -->
        <div class="card" style="padding: 2.5rem; border-radius: 24px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">⚙️ Automação @if(!$isPurePatient) do Cálculo @endif</div>
                
                @if(!$isPurePatient)
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <label style="display:flex; align-items:flex-start; gap:1rem; cursor:pointer; padding: 1rem; background: var(--surface-glass); border-radius: 16px; border: 1px solid var(--border);">
                        <input type="checkbox" name="auto_calorie" value="1" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);" @checked(old('auto_calorie'))>
                        <span style="font-size: 0.9rem;"><strong>Calcular calorias automaticamente</strong><br><span class="muted" style="font-size: 0.8rem;">Usa peso atual, TMB e nível de atividade.</span></span>
                    </label>
                    
                    <label style="display:flex; align-items:flex-start; gap:1rem; cursor:pointer; padding: 1rem; background: var(--surface-glass); border-radius: 16px; border: 1px solid var(--border);">
                        <input type="checkbox" name="auto_water" value="1" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);" @checked(old('auto_water', $u->is_water_target_auto))>
                        <span style="font-size: 0.9rem;"><strong>Calcular água automaticamente</strong><br><span class="muted" style="font-size: 0.8rem;">Usa peso atual, idade e clima.</span></span>
                    </label>
                </div>
                @else
                <div style="text-align: center; padding: 2rem; background: var(--surface-glass); border-radius: 20px; border: 1px dashed var(--border);">
                    <i class="fas fa-lock" style="font-size: 2rem; color: var(--border); margin-bottom: 1rem; display: block;"></i>
                    <p class="muted" style="font-size: 0.85rem;">As automações de metas são exclusivas para alunos ou gerenciadas pelo seu profissional.</p>
                </div>
                @endif
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 16px; font-weight: 700; font-size: 1.1rem; box-shadow: var(--shadow-md);">Salvar Alterações</button>
            </div>
        </div>

    </div>
</form>

@if(!$isPurePatient)
<!-- CARD: CONQUISTAS E TROFÉUS (GAMIFICAÇÃO) -->
<div class="card animate-fade-up" style="padding: 2.5rem; border-radius: 24px; margin-top: 2rem; border: 2px solid gold; background: linear-gradient(145deg, var(--surface) 0%, rgba(255, 215, 0, 0.05) 100%);">
    <div class="section-label" style="color: gold; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">🏆 Galeria de Conquistas</div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1.5rem; text-align: center;">
        @php($myBadges = \App\Services\AchievementService::getList(auth()->id()))
        @foreach($myBadges as $badge)
            <div class="badge-item" style="padding: 1rem; border-radius: 20px; background: {{ $badge->unlocked ? 'var(--surface-glass)' : 'rgba(255,255,255,0.02)' }}; border: 1px solid {{ $badge->unlocked ? 'gold' : 'var(--border)' }}; opacity: {{ $badge->unlocked ? '1' : '0.4' }}; transition: all 0.3s ease; filter: {{ $badge->unlocked ? 'none' : 'grayscale(1)' }};" title="{{ $badge->desc }}">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">{{ $badge->icon }}</div>
                <div style="font-weight: 700; font-size: 0.85rem; color: {{ $badge->unlocked ? 'gold' : 'var(--text)' }};">{{ $badge->name }}</div>
                <small class="muted" style="font-size: 0.65rem; display: block; margin-top: 0.25rem;">{{ $badge->desc }}</small>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="profile-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(28rem, 1fr)); gap: 2rem; margin-top: 2rem; margin-bottom: 4rem;">
    
    @if(!$isPurePatient)
    <!-- CARD: MACROS (PREMIUM GATE) -->
    <div class="card animate-fade-up" style="padding: 2.5rem; border-radius: 24px; position: relative; overflow: hidden;">
        <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">🧪 Macros Personalizados</div>
        
        @if ($isPremium)
            <form method="post" action="{{ route('profile') }}" novalidate>
                @csrf
                <input type="hidden" name="profile_action" value="macros">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="protein_target_g" style="font-size: 0.8rem;">Proteína (g)</label>
                        <input id="protein_target_g" name="protein_target_g" type="number" step="0.1" value="{{ old('protein_target_g', $u->protein_target_g) }}" style="padding: 0.5rem; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="carbs_target_g" style="font-size: 0.8rem;">Carbo (g)</label>
                        <input id="carbs_target_g" name="carbs_target_g" type="number" step="0.1" value="{{ old('carbs_target_g', $u->carbs_target_g) }}" style="padding: 0.5rem; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="fat_target_g" style="font-size: 0.8rem;">Gordura (g)</label>
                        <input id="fat_target_g" name="fat_target_g" type="number" step="0.1" value="{{ old('fat_target_g', $u->fat_target_g) }}" style="padding: 0.5rem; border-radius: 8px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Salvar Macros</button>
            </form>
        @else
            <div style="opacity: 0.5; pointer-events: none;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div style="text-align:center;"><small class="muted">Prot</small><br><strong>{{ $freeMacroPrev['p'] ?? '—' }}g</strong></div>
                    <div style="text-align:center;"><small class="muted">Carb</small><br><strong>{{ $freeMacroPrev['c'] ?? '—' }}g</strong></div>
                    <div style="text-align:center;"><small class="muted">Gord</small><br><strong>{{ $freeMacroPrev['f'] ?? '—' }}g</strong></div>
                </div>
            </div>
            <div class="premium-badge" style="position: absolute; top: 1.5rem; right: 1.5rem; background: gold; color: black; font-size: 0.65rem; padding: 0.25rem 0.6rem; border-radius: 99px; font-weight: 800;">PREMIUM</div>
            <div style="margin-top: 1rem; padding: 1.25rem; background: var(--surface-glass); border-radius: 16px; border: 1px solid var(--border); text-align: center;">
                <p style="font-size: 0.85rem; margin-bottom: 1rem;">Personalize suas metas de macros por gramas.</p>
                <a href="{{ route('plano') }}" class="btn btn-sm btn-primary" style="padding: 0.5rem 1.5rem;">Ver Planos</a>
            </div>
        @endif
    </div>
    @endif

    <!-- CARD: SEGURANÇA -->
    <div class="card animate-fade-up" style="padding: 2.5rem; border-radius: 24px;">
        <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">🔒 Segurança e Senha</div>
        
        <form method="post" action="{{ route('profile') }}" novalidate autocomplete="off">
            @csrf
            <input type="hidden" name="profile_action" value="password">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="current_password" style="font-size: 0.85rem;">Senha Atual</label>
                <input id="current_password" name="current_password" type="password" required style="padding: 0.5rem 1rem; border-radius: 10px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="new_password" style="font-size: 0.85rem;">Nova Senha</label>
                    <input id="new_password" name="new_password" type="password" required minlength="8" style="padding: 0.5rem 1rem; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label for="new_password_confirm" style="font-size: 0.85rem;">Confirmar</label>
                    <input id="new_password_confirm" name="new_password_confirm" type="password" required minlength="8" style="padding: 0.5rem 1rem; border-radius: 10px;">
                </div>
            </div>
            <p class="muted" style="font-size: 0.75rem; margin-top: 1rem; font-style: italic;">
                A senha deve ter pelo menos 8 caracteres, uma letra maiúscula, um número e um símbolo.
            </p>
            <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 1.5rem;">Atualizar Senha</button>
        </form>
    </div>

    </div>

    <!-- CARD: PRIVACIDADE E DADOS (LGPD) -->
    <div class="card animate-fade-up" style="padding: 2.5rem; border-radius: 24px; margin-top: 2rem; margin-bottom: 4rem;">
        <div class="section-label" style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; margin-bottom: 1.5rem; display: block;">🛡️ Privacidade e Seus Dados (LGPD)</div>
        
        <p class="muted" style="margin-bottom: 1.5rem; font-size: 0.95rem;">Em conformidade com a Lei Geral de Proteção de Dados, você tem total controle sobre suas informações.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr)); gap: 1.5rem;">
            <!-- Portabilidade -->
            <div style="background: var(--surface-glass); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0 0 0.5rem; font-size: 1.125rem;">Portabilidade de Dados</h4>
                    <p class="muted" style="font-size: 0.85rem; margin-bottom: 1.5rem;">Baixe todos os seus registros (perfil, refeições e treinos) em formato digital (JSON).</p>
                </div>
                <a href="{{ route('privacy.download') }}" class="btn btn-outline-primary btn-sm" style="width: 100%;">
                    <i class="fas fa-file-export me-2"></i> Baixar Meus Dados
                </a>
            </div>

            <!-- Esquecimento -->
            <div style="background: rgba(248, 81, 73, 0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(248, 81, 73, 0.2); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0 0 0.5rem; font-size: 1.125rem; color: #f85149;">Direito ao Esquecimento</h4>
                    <p class="muted" style="font-size: 0.85rem; margin-bottom: 1.5rem;">Solicite a exclusão total e definitiva de seus dados de nossos servidores.</p>
                </div>
                <form action="{{ route('privacy.request-deletion') }}" method="POST"
                    data-confirm-delete
                    data-confirm-title="Solicitar exclusão de dados"
                    data-confirm-message="Tem certeza? Esta ação enviará um pedido formal de exclusão de dados e não pode ser desfeita após o processamento."
                    data-confirm-primary-label="Enviar pedido">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm" style="width: 100%;">
                        <i class="fas fa-trash-alt me-2"></i> Solicitar Exclusão de Dados
                    </button>
                </form>
            </div>

            <!-- Bloqueios -->
            <div style="background: var(--surface-glass); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0 0 0.5rem; font-size: 1.125rem;">Utilizadores Bloqueados</h4>
                    <p class="muted" style="font-size: 0.85rem; margin-bottom: 1.5rem;">Gerencie a lista de pessoas que você bloqueou para não receber mensagens.</p>
                </div>
                <a href="{{ route('profile.blocked') }}" class="btn btn-outline-primary btn-sm" style="width: 100%;">
                    <i class="fas fa-user-slash me-2"></i> Gerir Bloqueios
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
