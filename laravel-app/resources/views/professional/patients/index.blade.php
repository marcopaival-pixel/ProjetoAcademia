@extends('layouts.app')

@section('title', 'Base de Clientes — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <x-plan-over-limit-banner resource="patients" />
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Base de Inteligência</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ count($patients) }} {{ $patientsLabel }} Conectados</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Gestão de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">{{ $patientsLabel }}</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Acompanhamento granular da evolução biométrica e adesão às prescrições do ecossistema NexShape.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <a href="{{ route('admin.export.users') }}" class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold rounded-xl hover:bg-zinc-700 transition-all border border-white/5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    Exportar
                </a>
                <a href="{{ route('professional.patients.create') }}" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Novo {{ $patientLabel }}
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas e Controles de Visualização -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-2xl px-5 py-3 flex items-center gap-3">
                <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Total</span>
                <span class="text-white font-black text-xl">{{ $patients->total() }}</span>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mr-2">Itens por página: 10</span>
            </div>
        </div>

        <form action="{{ route('professional.patients.index') }}" method="GET" class="flex flex-1 max-w-2xl gap-4">
            <div class="relative flex-1">
                <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-zinc-900/50 border border-white/5 rounded-2xl py-3 pl-12 pr-6 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all placeholder:text-zinc-600" placeholder="Buscar por nome ou e-mail...">
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-zinc-900/50 border border-white/5 rounded-2xl px-5 py-3 text-zinc-400 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none min-w-[140px]">
                <option value="">Status</option>
                <option value="Ativo" {{ request('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Inativo" {{ request('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            <select name="goal" onchange="this.form.submit()" class="bg-zinc-900/50 border border-white/5 rounded-2xl px-5 py-3 text-zinc-400 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none min-w-[140px]">
                <option value="">Objetivo</option>
                <option value="gain" {{ request('goal') == 'gain' ? 'selected' : '' }}>Hipertrofia</option>
                <option value="lose" {{ request('goal') == 'lose' ? 'selected' : '' }}>Emagrecimento</option>
                <option value="performance" {{ request('goal') == 'performance' ? 'selected' : '' }}>Performance</option>
                <option value="maintain" {{ request('goal') == 'maintain' ? 'selected' : '' }}>Saúde e Bem-Estar</option>
            </select>
        </form>
    </div>

    <!-- Tabela de {{ $patientsLabel }} Premium -->
    <div class="relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-8 py-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-blue-400 transition-colors">
                                Nome do {{ $patientLabel }}
                                @if(request('sort', 'name') == 'name')
                                    <svg class="w-3 h-3 {{ request('direction') == 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Status</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Objetivo</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Adesão</th>
                        <th class="px-6 py-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_activity', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-blue-400 transition-colors">
                                Última Atualização
                                @if(request('sort') == 'last_activity')
                                    <svg class="w-3 h-3 {{ request('direction') == 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Responsável</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Perfil</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black uppercase tracking-widest text-zinc-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($patients as $patient)
                    <tr class="group hover:bg-white/[0.02] transition-all">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4 relative">
                                @if($patient['is_locked'])
                                    <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-1 h-8 bg-rose-500 rounded-full shadow-[0_0_10px_rgba(244,63,94,0.5)]"></div>
                                @endif
                                <div class="w-10 h-10 rounded-xl bg-{{ $patient['color'] }}-500/10 text-{{ $patient['color'] }}-500 flex items-center justify-center font-black text-sm border border-{{ $patient['color'] }}-500/20 shadow-lg shadow-{{ $patient['color'] }}-500/5 {{ $patient['is_locked'] ? 'opacity-50 grayscale' : '' }}">
                                    {{ $patient['initials'] }}
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="text-white font-bold text-sm group-hover:text-blue-400 transition-colors {{ $patient['is_locked'] ? 'text-zinc-500' : '' }}">{{ $patient['name'] }}</span>
                                        @if($patient['is_locked'])
                                            <span class="px-1.5 py-0.5 rounded-md bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase tracking-tighter border border-rose-500/20">Bloqueado</span>
                                        @endif
                                    </div>
                                    <span class="text-zinc-600 text-[10px] font-medium tracking-tight">ID: #{{ str_pad($patient['id'], 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                {{ $patient['status'] == 'Ativo' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 
                                   ($patient['status'] == 'Pendente' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 
                                   'bg-zinc-500/10 text-zinc-400 border-zinc-500/20') }}">
                                {{ $patient['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-zinc-400 text-xs font-bold">{{ $patient['goal'] }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1.5 min-w-[100px]">
                                <div class="flex justify-between items-center text-[10px] font-black">
                                    <span class="text-{{ $patient['color'] }}-400">{{ $patient['engage_val'] }}%</span>
                                </div>
                                <div class="w-full bg-zinc-950 rounded-full h-1.5 overflow-hidden border border-white/5 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-1000 bg-gradient-to-r 
                                        {{ $patient['engage_val'] > 80 ? 'from-emerald-600 to-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : 
                                           ($patient['engage_val'] > 50 ? 'from-amber-600 to-amber-400' : 'from-rose-600 to-rose-400') }}" 
                                        style="width: {{ $patient['engage_val'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-zinc-300 text-xs font-bold">{{ $patient['last_activity'] }}</span>
                                <span class="text-zinc-600 text-[10px]">{{ $patient['last_activity_date'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-zinc-400 text-xs font-medium">{{ $patient['professional_name'] }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-2 py-1 rounded-lg bg-zinc-800/50 text-zinc-500 text-[9px] font-black border border-white/5 uppercase tracking-wider">
                                {{ $patient['profile_type'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($patient['user_status'] === 'pending' || $patient['status'] === 'Aprovação Pendente')
                                    <button onclick="approvePatient({{ $patient['id'] }}, this)" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center transition-all shadow-xl text-emerald-500 bg-emerald-500/10 border-emerald-500/20 hover:bg-emerald-600 hover:text-white" title="Aprovar e Liberar Acesso">
                                        <i class="fas fa-user-check text-xs"></i>
                                    </button>
                                    @if($patient['user_status'] === 'pending')
                                    <button onclick="resendActivation({{ $patient['id'] }})" class="p-2 bg-amber-500/10 text-amber-500 rounded-lg border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all shadow-lg hover:shadow-amber-500/20" title="Reenviar E-mail de Ativação">
                                        <svg class="w-4 h-4 text-amber-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                    @endif
                                @else
                                    <button onclick="generateLink({{ $patient['id'] }})" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg border border-white/5 hover:bg-emerald-600 hover:text-white transition-all" title="Link Acesso">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </button>
                                @endif
                                <a href="{{ route('professional.patients.show', $patient['id']) }}" class="p-2 bg-blue-600/10 text-blue-500 rounded-lg border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all shadow-lg hover:shadow-blue-600/20" title="Ver Prontuário">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                                <a href="{{ route('professional.patients.edit', $patient['id']) }}" class="p-2 bg-zinc-800 text-zinc-500 rounded-lg border border-white/5 hover:bg-zinc-700 hover:text-white transition-all" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="{{ route('professional.patients.export-report', $patient['id']) }}" class="p-2 bg-zinc-800 text-zinc-500 rounded-lg border border-white/5 hover:bg-emerald-600 hover:text-white transition-all" title="Gerar Laudo PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </a>
                                <button type="button" onclick="showDeactivateModal({{ $patient['id'] }}, '{{ addslashes($patient['name']) }}')" class="p-2 bg-zinc-800 text-rose-500/50 rounded-lg border border-white/5 hover:bg-rose-600 hover:text-white transition-all" title="Desvincular">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-3xl bg-zinc-950/50 flex items-center justify-center border border-white/5 shadow-inner">
                                    <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <p class="text-zinc-500 font-bold text-sm">Nenhum {{ mb_strtolower($patientLabel) }} encontrado para os critérios selecionados.</p>
                                <a href="{{ route('professional.patients.index') }}" class="text-blue-500 text-xs font-black uppercase tracking-widest hover:text-blue-400 transition-all">Limpar Filtros</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação Customizada -->
        @if($patients->hasPages())
        <div class="px-8 py-6 bg-white/[0.02] border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-zinc-500 text-xs font-bold">
                Mostrando <span class="text-white">{{ $patients->firstItem() }}–{{ $patients->lastItem() }}</span> de <span class="text-white">{{ $patients->total() }}</span> {{ mb_strtolower($patientsLabel) }}
            </p>
            
            <div class="flex items-center gap-2">
                @if($patients->onFirstPage())
                    <span class="p-2.5 rounded-xl bg-zinc-950/50 text-zinc-700 border border-white/5 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                @else
                    <a href="{{ $patients->previousPageUrl() }}" class="p-2.5 rounded-xl bg-zinc-900 border border-white/10 text-zinc-400 hover:text-white hover:border-blue-500/50 transition-all shadow-lg hover:shadow-blue-500/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                <div class="flex items-center gap-1.5 mx-2">
                    @foreach ($patients->getUrlRange(max(1, $patients->currentPage() - 2), min($patients->lastPage(), $patients->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all border 
                            {{ $page == $patients->currentPage() ? 'bg-blue-600 text-white border-blue-500 shadow-lg shadow-blue-500/20 scale-110 z-10' : 'bg-zinc-900/50 text-zinc-500 border-white/5 hover:border-white/10 hover:text-white' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                </div>

                @if($patients->hasMorePages())
                    <a href="{{ $patients->nextPageUrl() }}" class="p-2.5 rounded-xl bg-zinc-900 border border-white/10 text-zinc-400 hover:text-white hover:border-blue-500/50 transition-all shadow-lg hover:shadow-blue-500/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <span class="p-2.5 rounded-xl bg-zinc-950/50 text-zinc-700 border border-white/5 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- NexShape Premium Access Modal -->
<div id="tokenModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-zinc-950/90 backdrop-blur-xl transition-all duration-500 opacity-0" id="modalOverlay"></div>
    
    <div class="relative bg-zinc-900 border border-white/10 rounded-[3.5rem] p-8 md:p-12 max-w-xl w-full shadow-[0_0_100px_rgba(59,130,246,0.15)] scale-95 opacity-0 transition-all duration-500 overflow-hidden" id="modalContent">
        <!-- Background Glow Decorations -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-600/10 rounded-full blur-[100px]"></div>

        <div class="relative flex flex-col items-center text-center space-y-8">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-blue-500/40 border-4 border-zinc-900 transition-transform hover:scale-110 duration-500">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
            </div>

            <div class="space-y-3">
                <h3 class="text-4xl font-black text-white tracking-tighter" id="modalDisplayTitle">
                    Vínculo <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Pronto!</span>
                </h3>
                <p class="text-zinc-500 font-medium text-sm leading-relaxed max-w-sm mx-auto">
                    Conexão restabelecida. Este link concede acesso seguro ao ecossistema NexShape para o {{ mb_strtolower($patientLabel) }}.
                </p>
            </div>

            <div class="w-full space-y-6">
                <!-- Link Container with dynamic glow -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <div class="relative flex items-center">
                        <input type="text" id="tokenLink" readonly 
                            class="w-full bg-zinc-950/80 border border-white/10 rounded-2xl py-6 px-8 text-blue-400 font-mono text-sm outline-none focus:border-blue-500/50 transition-all cursor-default pr-32 select-all" 
                            value="">
                        <button onclick="copyToken()" 
                            class="absolute right-3 py-3 px-6 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95"
                            id="copyBtn">
                            Copiar
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <button onclick="closeModal()" class="px-12 py-5 bg-zinc-800/50 text-zinc-400 font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl hover:bg-zinc-800 hover:text-white transition-all border border-white/5">
                        Fechar Painel
                    </button>
                </div>
            </div>
            
            <div class="pt-4 flex items-center justify-center gap-3">
                <span class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Link Seguro
                </span>
                <span class="text-zinc-800 text-[8px]">•</span>
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-600">Válido por 7 dias</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal Desvincular -->
<div id="deactivateModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-zinc-950/90 backdrop-blur-xl" onclick="closeDeactivateModal()"></div>
    <div class="relative bg-zinc-900 border border-white/10 rounded-[2.5rem] p-8 max-w-lg w-full shadow-2xl scale-100 transition-all">
        <h3 class="text-2xl font-black text-white text-center mb-2">Desvincular Aluno</h3>
        <p class="text-zinc-400 text-center text-sm mb-6">
            O aluno <strong id="deactivateModalName" class="text-white"></strong> será marcado como inativo e perderá o vínculo com seu painel. O histórico completo de dados será preservado.
        </p>
        
        <form id="deactivateForm" method="POST" action="">
            @csrf
            <div class="mb-6">
                <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Motivo da desvinculação (Opcional)</label>
                <textarea name="motivo_desvinculacao" rows="3" class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition-all resize-none" placeholder="Informe o motivo..."></textarea>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="button" onclick="closeDeactivateModal()" class="flex-1 py-4 bg-zinc-800 text-white font-bold rounded-xl hover:bg-zinc-700 transition-all text-sm border border-white/5">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-500 transition-all shadow-lg shadow-rose-600/20 text-sm">
                    Confirmar Desvinculação
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API_BASE = "{{ url('/professional/patients') }}";

@if(session('activation_link'))
    document.addEventListener('DOMContentLoaded', () => showModal("{{ session('activation_link') }}", "Vínculo"));
@endif

function setBtnLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        btn.innerHTML = btn.dataset.original;
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

async function handleAction(patientId, endpoint, title, btn) {
    setBtnLoading(btn, true);
    try {
        const url = `${API_BASE}/${patientId}/${endpoint}`;
        const response = await fetch(url);
        const contentType = response.headers.get("content-type");
        
        if (!response.ok) {
            let errorMsg = `Erro HTTP ${response.status}`;
            if (contentType && contentType.includes("application/json")) {
                const data = await response.json();
                errorMsg = data.message || errorMsg;
            }
            throw new Error(errorMsg);
        }

        if (contentType && contentType.includes("application/json")) {
            const data = await response.json();
            if (data.success) {
                showModal(data.link, title);
            } else {
                throw new Error(data.message || 'Falha na operação.');
            }
        } else {
            throw new Error('O servidor não retornou um formato válido (JSON). Verifique os logs.');
        }
    } catch (err) {
        console.error('Erro NexShape:', err);
        window.dispatchEvent(new CustomEvent('error-modal', {
            detail: {
                message: err.message || 'Não foi possível completar a operação.',
                title: 'Erro de Vínculo'
            }
        }));
    } finally {
        setBtnLoading(btn, false);
    }
}

function resendActivation(patientId) {
    handleAction(patientId, 'activation-link', 'Vínculo', event.currentTarget);
}

async function approvePatient(patientId, btnElement) {
    setBtnLoading(btnElement, true);
    try {
        const response = await fetch(`/professional/patients/${patientId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        
        if (data.success) {
            showModal(data.temp_password, "Acesso Liberado");
            
            // Exibir a senha temporária no modal ou alerta
            document.getElementById('modalDisplayTitle').innerHTML = `Acesso <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">Liberado!</span>`;
            document.querySelector('#modalContent p').innerHTML = `{{ $patientLabel }} ativado com sucesso. Copie a <strong>senha temporária</strong> abaixo e envie para o {{ mb_strtolower($patientLabel) }}.<br><span class="text-xs text-amber-500 block mt-2">O {{ mb_strtolower($patientLabel) }} será forçado a criar uma nova senha ao entrar.</span>`;
            
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: data.message,
                    title: '{{ $patientLabel }} Aprovado'
                }
            }));
            
            // Recarregar a página após fechar o modal ou após 5 segundos
            setTimeout(() => window.location.reload(), 5000);
        } else {
            throw new Error(data.message || 'Erro ao aprovar {{ mb_strtolower($patientLabel) }}');
        }
    } catch (err) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: {
                message: err.message,
                title: 'Erro de Aprovação'
            }
        }));
    } finally {
        setBtnLoading(btnElement, false);
    }
}

function generateLink(patientId) {
    handleAction(patientId, 'generate-link', 'Acesso', event.currentTarget);
}

function showModal(link, title = "Link de Acesso") {
    const modal = document.getElementById('tokenModal');
    const overlay = document.getElementById('modalOverlay');
    const content = document.getElementById('modalContent');
    const input = document.getElementById('tokenLink');
    const openLink = document.getElementById('openLink');
    const titleSpan = document.getElementById('modalDisplayTitle');

    if (titleSpan) {
        titleSpan.innerHTML = `${title} <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Pronto!</span>`;
    }

    input.value = link;
    if (openLink) {
        openLink.href = link;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        overlay.classList.replace('opacity-0', 'opacity-100');
        content.classList.replace('scale-95', 'scale-100');
        content.classList.replace('opacity-0', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('tokenModal');
    const overlay = document.getElementById('modalOverlay');
    const content = document.getElementById('modalContent');

    overlay.classList.replace('opacity-100', 'opacity-0');
    content.classList.replace('scale-100', 'scale-95');
    content.classList.replace('opacity-100', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 500);
}

function copyToken() {
    const input = document.getElementById('tokenLink');
    const btn = document.getElementById('copyBtn');
    const originalText = btn.innerText;

    if (!input.value) return;

    navigator.clipboard.writeText(input.value).then(() => {
        btn.innerText = 'COPIADO!';
        btn.classList.replace('bg-blue-600', 'bg-emerald-600');
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.replace('bg-emerald-600', 'bg-blue-600');
        }, 2000);
    });
}

function showDeactivateModal(patientId, patientName) {
    const form = document.getElementById('deactivateForm');
    form.action = `${API_BASE}/${patientId}/deactivate`;
    document.getElementById('deactivateModalName').innerText = patientName;
    document.getElementById('deactivateModal').classList.remove('hidden');
    document.getElementById('deactivateModal').classList.add('flex');
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
    document.getElementById('deactivateModal').classList.remove('flex');
}
</script>

@endsection




