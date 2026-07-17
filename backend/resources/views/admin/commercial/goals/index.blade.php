@extends('layouts.admin')

@section('title', 'Metas e Performance')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Metas Estratégicas</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Planejamento e acompanhamento de crescimento</p>
        </div>
        <button onclick="document.getElementById('modal-goal').classList.remove('hidden')" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-600/20 flex items-center gap-3">
            <i class="fas fa-bullseye text-sm"></i> Definir Nova Meta
        </button>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($goals as $goal)
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 space-y-6 relative overflow-hidden group shadow-2xl">
                <div class="flex items-center justify-between relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-indigo-400 border border-white/5">
                        @switch($goal->type)
                            @case('revenue') <i class="fas fa-dollar-sign text-xl"></i> @break
                            @case('new_users') <i class="fas fa-user-plus text-xl"></i> @break
                            @case('active_users') <i class="fas fa-users text-xl"></i> @break
                            @case('tickets_resolved') <i class="fas fa-headset text-xl"></i> @break
                            @default <i class="fas fa-star text-xl"></i>
                        @endswitch
                    </div>
                    <form action="{{ route('admin.goals.destroy', $goal) }}" method="POST"
                    data-confirm-delete
                    data-confirm-title="Excluir meta"
                    data-confirm-message="Excluir esta meta? Esta ação não pode ser desfeita.">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center bg-transparent text-zinc-800 hover:bg-red-500/10 hover:text-red-500 transition-all">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </form>
                </div>

                <div class="relative z-10">
                    <h3 class="text-lg font-black text-white truncate">{{ $goal->title }}</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $goal->start_date->format('d/m') }} até {{ $goal->end_date->format('d/m/Y') }}</p>
                </div>

                <div class="space-y-3 relative z-10">
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">
                                @if($goal->type == 'revenue') R$ {{ number_format($goal->current_value, 2, ',', '.') }}
                                @else {{ number_format($goal->current_value, 0, ',', '.') }}
                                @endif
                            </span>
                            <span class="text-[10px] text-zinc-500 font-bold">/ 
                                @if($goal->type == 'revenue') R$ {{ number_format($goal->target_value, 2, ',', '.') }}
                                @else {{ number_format($goal->target_value, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <span class="text-sm font-black text-indigo-500">{{ $goal->progress }}%</span>
                    </div>
                    <div class="w-full h-3 bg-white/5 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-gradient-to-r from-indigo-600 to-emerald-500 transition-all duration-1000 shadow-[0_0_15px_rgba(79,70,229,0.5)]" style="width: {{ min($goal->progress, 100) }}%"></div>
                    </div>
                </div>

                <!-- Glow Background Effect -->
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-600/5 blur-[100px] rounded-full group-hover:bg-indigo-600/10 transition-all"></div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-zinc-950/30 border border-dashed border-white/5 rounded-[3rem] text-center">
                <i class="fas fa-chess text-zinc-800 text-4xl mb-6"></i>
                <p class="text-xs text-zinc-600 font-black uppercase tracking-widest">Nenhuma meta estratégica definida para o período</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Goal -->
<div id="modal-goal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] w-full max-w-xl p-10 animate-scale-up">
        <h3 class="text-xl font-black text-white mb-2">Definir Nova Meta</h3>
        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-8">Planejamento de Performance</p>
        
        <form action="{{ route('admin.goals.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Título / Identificação</label>
                <input type="text" name="title" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-indigo-500/50 transition-all outline-none" placeholder="Ex: Faturamento Mensal Abril">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Tipo de Métrica</label>
                    <select name="type" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-indigo-500/50 transition-all outline-none appearance-none">
                        <option value="revenue">Faturamento (R$)</option>
                        <option value="new_users">Novos Usuários</option>
                        <option value="active_users">Usuários Ativos (7d)</option>
                        <option value="tickets_resolved">Chamados Resolvidos</option>
                        <option value="custom">Valor Customizado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Valor Alvo</label>
                    <input type="number" name="target_value" step="0.01" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-indigo-500/50 transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Data Início</label>
                    <input type="date" name="start_date" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-indigo-500/50 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Data Fim</label>
                    <input type="date" name="end_date" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-indigo-500/50 transition-all outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-goal').classList.add('hidden')" class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Cancelar</button>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Salvar Planejamento</button>
            </div>
        </form>
    </div>
</div>
@endsection
