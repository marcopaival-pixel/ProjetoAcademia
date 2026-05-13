@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Criar Novo Plano
            </h1>
            <p class="text-gray-400 mt-2 uppercase text-xs font-black tracking-widest">Configure os detalhes e acessos do plano</p>
        </div>
        <a href="{{ route('admin.plans.index') }}" class="group flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition-all duration-300 shadow-xl">
            <i class="fas fa-arrow-left text-blue-400 group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-gray-300 font-bold text-sm leading-none">Voltar</span>
        </a>
    </div>

    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <!-- Basic Info -->
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 space-y-8">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 text-sm border border-blue-500/20">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Configuração Geral
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Nome do Plano</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700"
                            placeholder="ex: Plano Black">
                        @error('name') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Descrição</label>
                        <textarea name="description" rows="3"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700"
                            placeholder="Descreva os benefícios do plano...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Tipo de Plano</label>
                        <select name="type" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
                            <option value="student"      {{ old('type') == 'student'      ? 'selected' : '' }}>Aluno (B2C)</option>
                            <option value="personal"     {{ old('type') == 'personal'     ? 'selected' : '' }}>Personal Trainer</option>
                            <option value="nutritionist" {{ old('type') == 'nutritionist' ? 'selected' : '' }}>Nutricionista</option>
                            <option value="professional" {{ old('type') == 'professional' ? 'selected' : '' }}>Profissional (Legado)</option>
                            <option value="clinic"       {{ old('type') == 'clinic'       ? 'selected' : '' }}>Academia / Estúdio (B2B)</option>
                            <option value="full"         {{ old('type') == 'full'         ? 'selected' : '' }}>Completo (Todos os perfis)</option>
                        </select>
                        @error('type') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Preço Mensal (R$)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', '0.00') }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700">
                        @error('price') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Créditos de IA Mensais</label>
                        <input type="number" name="ai_credits" value="{{ old('ai_credits', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
                        @error('ai_credits') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Limits -->
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 space-y-8">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 text-sm border border-emerald-500/20">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Limites de Uso
                </h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[9px] font-black text-gray-500 mb-2 uppercase tracking-widest ml-1">Max Treinos</label>
                        <input type="number" name="max_workouts" value="{{ old('max_workouts', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-500 mb-2 uppercase tracking-widest ml-1">Max Dietas</label>
                        <input type="number" name="max_diets" value="{{ old('max_diets', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-500 mb-2 uppercase tracking-widest ml-1">Max Avaliações</label>
                        <input type="number" name="max_assessments" value="{{ old('max_assessments', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-500 mb-2 uppercase tracking-widest ml-1">Max Pacientes</label>
                        <input type="number" name="max_patients" value="{{ old('max_patients', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-gray-500 mb-2 uppercase tracking-widest ml-1">Max Profissionais (Clínica)</label>
                        <input type="number" name="max_professionals" value="{{ old('max_professionals', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all">
                    </div>
                </div>
            </div>

            <!-- Advanced Settings (Corporate & Trial) -->
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 space-y-8 md:col-span-2">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400 text-sm border border-amber-500/20">
                        <i class="fas fa-cog"></i>
                    </div>
                    Configurações Avançadas
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Período de Teste (Dias)</label>
                        <input type="number" name="trial_days" value="{{ old('trial_days', 0) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition-all">
                        <p class="text-[9px] text-gray-600 mt-2 ml-2 italic">0 = Sem trial</p>
                    </div>

                    <div class="flex flex-col justify-center">
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl border border-white/[0.03] hover:bg-white/[0.03] transition-all">
                            <input type="checkbox" name="is_corporate" id="is_corporate" value="1" class="hidden peer" {{ old('is_corporate') ? 'checked' : '' }} onchange="toggleCorporateFields(this.checked)">
                            <div class="w-6 h-6 rounded-lg border-2 border-white/10 flex items-center justify-center peer-checked:bg-amber-500 peer-checked:border-amber-500 transition-all">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <div>
                                <span class="block text-gray-300 font-bold text-sm">Plano Corporativo</span>
                                <span class="block text-gray-500 text-[10px]">Cobrança por profissional adicional</span>
                            </div>
                        </label>
                    </div>

                    <div id="corporate-fields" class="{{ old('is_corporate') ? '' : 'hidden' }} space-y-6 md:col-span-1 border-l border-white/5 pl-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest">Preço p/ Profissional Extra</label>
                            <input type="number" step="0.01" name="price_per_professional" value="{{ old('price_per_professional', '0.00') }}"
                                class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest">Mínimo de Profissionais</label>
                            <input type="number" name="min_professionals" value="{{ old('min_professionals', 1) }}"
                                class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles / Perfis Vinculados -->
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 md:col-span-2">
                <h3 class="text-xl font-bold text-white flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 bg-violet-500/20 rounded-xl flex items-center justify-center text-violet-400 text-sm border border-violet-500/20">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    Perfis que podem assinar este plano
                </h3>
                <p class="text-xs text-gray-600 mb-8 ml-1">Deixe em branco para tornar o plano visível a todos os perfis.</p>

                <div class="flex flex-wrap gap-4">
                    @foreach($roles as $role)
                    <label class="flex items-center gap-3 cursor-pointer group p-3 pr-5 rounded-2xl border border-white/[0.05] hover:bg-white/[0.04] hover:border-violet-500/30 transition-all">
                        <input type="checkbox"
                               name="role_ids[]"
                               value="{{ $role->id }}"
                               class="hidden peer"
                               {{ is_array(old('role_ids')) && in_array($role->id, old('role_ids')) ? 'checked' : '' }}>
                        <div class="w-5 h-5 rounded-md border-2 border-white/10 flex items-center justify-center peer-checked:bg-violet-600 peer-checked:border-violet-600 transition-all">
                            <i class="fas fa-check text-white text-[9px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-gray-300 font-bold text-sm group-hover:text-white transition-colors">{{ $role->label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('role_ids') <p class="text-red-400 text-xs mt-4 ml-2 italic">{{ $message }}</p> @enderror
            </div>

            <!-- Features -->
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 md:col-span-2">
                <h3 class="text-xl font-bold text-white flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 text-sm border border-indigo-500/20">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    Funcionalidades e Acessos
                </h3>

                <div class="max-h-[380px] overflow-y-auto pr-4 space-y-6 custom-scrollbar">
                    @php
                        $availableFeatures = [
                            'menu_student' => ['label' => 'Menu Aluno', 'desc' => 'Dashboard, Treinos, Diário, Peso'],
                            'menu_professional' => ['label' => 'Menu Profissional', 'desc' => 'Gestão de Pacientes, Prescrições IA, Branding'],
                            'menu_finance' => ['label' => 'Menu Financeiro', 'desc' => 'Histórico de pagamentos e faturas'],
                            'menu_reports' => ['label' => 'Menu Relatórios', 'desc' => 'Relatórios mensais detalhados em PDF'],
                            'menu_agenda' => ['label' => 'Menu Agenda', 'desc' => 'Agendamento de sessões e avaliações'],
                            'menu_assessments' => ['label' => 'Menu Avaliações', 'desc' => 'Avaliações físicas e bioimpedância'],
                        ];
                    @endphp

                    <div class="space-y-3">
                        @foreach($availableFeatures as $key => $feature)
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl border border-white/[0.03] hover:bg-white/[0.03] hover:border-white/10 transition-all">
                            <input type="checkbox" name="features[]" value="{{ $key }}" class="hidden peer" {{ is_array(old('features')) && in_array($key, old('features')) ? 'checked' : '' }}>
                            <div class="w-6 h-6 rounded-lg border-2 border-white/10 flex items-center justify-center peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all shadow-lg group-hover:scale-105">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <div>
                                <span class="block text-gray-300 font-bold text-sm group-hover:text-white transition-colors">{{ $feature['label'] }}</span>
                                <span class="block text-gray-500 text-[10px]">{{ $feature['desc'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @error('features') <p class="text-red-400 text-xs mt-4 ml-2 italic">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-white/5 gap-6">
            <button type="reset" class="px-8 py-4 text-gray-400 hover:text-white transition-all uppercase text-xs font-black tracking-widest">Limpar</button>
            <button type="submit" class="px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black uppercase text-xs tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/20 transform hover:-translate-y-1 transition-all duration-300">
                Salvar Novo Plano
            </button>
        </div>
    </form>
</div>

</div>

<script>
    function toggleCorporateFields(checked) {
        const fields = document.getElementById('corporate-fields');
        if (checked) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.4); }
</style>
@endsection
