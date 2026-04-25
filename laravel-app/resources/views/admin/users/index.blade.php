@extends('layouts.admin')

@section('title', 'Gestão de ' . ($currentRole ? ucfirst($currentRole) . 's' : 'Usuários'))

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Quick Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['l' => 'Contas Totais', 'v' => $overview['total_users'], 'i' => 'fas fa-users', 'c' => 'blue'],
                ['l' => 'Novos (7d)', 'v' => $overview['new_users_7d'], 'i' => 'fas fa-user-plus', 'c' => 'emerald'],
                ['l' => 'Ativos (7d)', 'v' => $overview['distinct_food_loggers_7d'], 'i' => 'fas fa-fire', 'c' => 'amber'],
                ['l' => 'Premium Ativo', 'v' => $overview['premium_subscriptions_active'], 'i' => 'fas fa-crown', 'c' => 'purple'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-{{ $s['c'] }}-500/10 flex items-center justify-center text-{{ $s['c'] }}-500">
                    <i class="{{ $s['i'] }} text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $s['l'] }}</span>
                    <p class="text-2xl font-black text-white leading-none mt-1">{{ $s['v'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Enhanced Filter Bar -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl" 
         x-data="{ 
            selectedProfile: '{{ request('profile_id') }}',
            selectedProfession: '{{ request('profession_id') }}',
            selectedSpecialty: '{{ request('specialty') }}',
            specialties: @json($specialties)
         }">
        <form action="{{ route('admin.users') }}" method="GET" class="space-y-6">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-4 space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Pesquisar Identidade</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, E-mail ou ID..." 
                            class="w-full bg-zinc-950 border border-white/5 p-4 pl-12 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>
                </div>

                <div class="md:col-span-3 space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Perfil / Cargo</label>
                    <select name="profile_id" x-model="selectedProfile" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                        <option value="">Todos os Perfis</option>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Comercial</label>
                    <select name="premium" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                        <option value="">Todos</option>
                        <option value="yes" {{ request('premium') === 'yes' ? 'selected' : '' }}>Premium</option>
                        <option value="no" {{ request('premium') === 'no' ? 'selected' : '' }}>Grátis</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">Filtrar</button>
                    <a href="{{ route('admin.users', ['role' => request('role')]) }}" class="w-14 bg-zinc-950 border border-white/5 flex items-center justify-center rounded-2xl text-zinc-500 hover:text-white transition-colors">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Professional Sub-filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-white/5 animate-fade-in" x-show="selectedProfile == '4'" x-cloak>
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Profissão Específica</label>
                    <select name="profession_id" x-model="selectedProfession" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                        <option value="">Todas as Profissões</option>
                        @foreach($professions as $prof)
                            <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Especialidade</label>
                    <select name="specialty" x-model="selectedSpecialty" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                        <option value="">Todas as Especialidades</option>
                        <template x-for="s in specialties.filter(item => !selectedProfession || item.profession_id == selectedProfession)" :key="s.id">
                            <option :value="s.nome" x-text="s.nome" :selected="selectedSpecialty == s.nome"></option>
                        </template>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Results Table -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-zinc-950/20">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight">
                    @if($currentRole == 'aluno') Base de Dados de Alunos
                    @elseif($currentRole == 'paciente') Lista de Pacientes Clínicos
                    @elseif($currentRole == 'professional') Corpo Docente / Profissionais
                    @elseif($currentRole == 'funcionario' || $currentRole == 'receptionist') Quadro de Funcionários
                    @else Base de Dados de Usuários
                    @endif
                </h2>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Exibindo registros conforme filtros ativos</p>
            </div>
            <div class="flex items-center gap-4" x-data="{ 
                showImport: false, 
                module: '', 
                title: '',
                isImporting: false,
                openImport(m, t) { this.module = m; this.title = t; this.showImport = true; }
            }">
                <div class="relative group/dropdown">
                    <button class="px-6 py-2.5 bg-zinc-900 border border-white/10 rounded-xl text-zinc-400 text-[10px] font-black uppercase tracking-widest hover:text-white transition-all flex items-center gap-2">
                        <i class="fas fa-file-import"></i> Importar Base <i class="fas fa-chevron-down text-[8px] ml-1"></i>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-56 bg-zinc-950 border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover/dropdown:opacity-100 group-hover/dropdown:visible transition-all duration-200 z-[110] p-2">
                        <button @click="openImport('profissionais', 'Importar Profissionais')" class="w-full text-left px-4 py-3 text-[10px] font-black text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-xl transition-colors flex items-center gap-2 uppercase tracking-widest">
                            <i class="fas fa-user-md w-4"></i> Profissionais
                        </button>
                        <button @click="openImport('alunos', 'Importar Alunos')" class="w-full text-left px-4 py-3 text-[10px] font-black text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-xl transition-colors flex items-center gap-2 uppercase tracking-widest mt-1">
                            <i class="fas fa-user-graduate w-4"></i> Alunos
                        </button>
                        <button @click="openImport('pacientes', 'Importar Pacientes')" class="w-full text-left px-4 py-3 text-[10px] font-black text-zinc-400 hover:text-white hover:bg-zinc-900 rounded-xl transition-colors flex items-center gap-2 uppercase tracking-widest mt-1">
                            <i class="fas fa-user-injured w-4"></i> Pacientes
                        </button>
                    </div>
                </div>

                <a href="{{ route('admin.users.create') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
                    <i class="fas fa-plus"></i> Novo Usuário
                </a>
                <a href="{{ route('admin.export.users') }}" class="px-6 py-2.5 bg-zinc-950 border border-white/10 rounded-xl text-zinc-400 text-[10px] font-black uppercase tracking-widest hover:text-white transition-all flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>

                <!-- Import Modal -->
                <template x-if="showImport">
                    <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
                        <div class="bg-zinc-900 border border-white/10 w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden" @click.away="!isImporting && (showImport = false)">
                            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-black text-white tracking-tight" x-text="title"></h3>
                                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Sincronização em massa via CSV</p>
                                </div>
                                <button @click="showImport = false" x-show="!isImporting" class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="p-8 space-y-8">
                                <div class="bg-blue-600/5 border border-blue-600/20 p-6 rounded-3xl flex items-center justify-between">
                                    <div>
                                        <h4 class="text-white text-xs font-black uppercase tracking-widest">Planilha Modelo</h4>
                                        <p class="text-[10px] text-zinc-500 font-medium mt-1">Baixe o padrão para evitar erros</p>
                                    </div>
                                    <a :href="'{{ url('admin/import/template') }}/' + module" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                                        <i class="fas fa-download mr-2"></i> Baixar Modelo
                                    </a>
                                </div>

                                <form :action="'{{ url('admin/import') }}/' + module" method="POST" enctype="multipart/form-data" @submit="isImporting = true">
                                    @csrf
                                    <div class="space-y-4" x-show="!isImporting">
                                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Selecione o arquivo (.csv)</label>
                                        <div class="relative group">
                                            <input type="file" name="file" required accept=".csv"
                                                class="w-full bg-zinc-950 border border-white/5 p-8 rounded-[2rem] text-zinc-500 text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all file:hidden cursor-pointer group-hover:border-blue-600/50">
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <div class="text-center">
                                                    <i class="fas fa-cloud-upload-alt text-2xl text-blue-600 mb-2"></i>
                                                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Clique para selecionar</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-6 py-4" x-show="isImporting">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] text-blue-400 font-black uppercase tracking-[0.2em] animate-pulse">Processando...</span>
                                            <div class="w-full h-2 bg-zinc-950 rounded-full overflow-hidden border border-white/5 ml-4">
                                                <div class="h-full bg-blue-600 animate-progress-fast"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 pt-6" x-show="!isImporting">
                                        <button type="button" @click="showImport = false" class="px-8 py-4 text-zinc-500 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">Cancelar</button>
                                        <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                                            Importar Agora
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[9px] font-black uppercase tracking-[0.25em] border-b border-white/5">
                        <th class="px-8 py-6">ID & Perfil</th>
                        <th class="px-8 py-6">Destaque / Info Extra</th>
                        <th class="px-8 py-6">Status Comercial</th>
                        <th class="px-8 py-6">Data de Ingresso</th>
                        <th class="px-8 py-6 text-right">Ações de Gestão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($users as $u)
                        @php
                            $isPaciente = $u->roles->contains('name', 'paciente');
                            $isProfissional = $u->roles->contains('name', 'professional');
                            $isAluno = $u->roles->contains('name', 'aluno');
                            $primaryRole = $u->roles->first();
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-8 py-6">
                                    <div class="flex items-center gap-4 {{ $isPaciente ? 'ml-8' : '' }}">
                                        @if($isPaciente)
                                            <div class="w-6 h-6 border-l-2 border-b-2 border-white/10 rounded-bl-xl mr-1 -mt-4"></div>
                                        @endif
                                        <div class="relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=18181b&color={{ $isPaciente ? 'emerald-500' : '3b82f6' }}" class="w-11 h-11 rounded-full border border-white/10 p-0.5">
                                            @if($u->is_admin)
                                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-amber-500 rounded-full border-4 border-zinc-900 flex items-center justify-center text-[8px] text-zinc-900" title="Admin">
                                                    <i class="fas fa-shield-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-black text-white leading-none">{{ $u->name }}</p>
                                                @foreach($u->roles as $role)
                                                    @php
                                                        $badgeClass = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                                                        if ($role->name === 'paciente') $badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                                        if ($role->name === 'professional') $badgeClass = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                                    @endphp
                                                    <span class="px-2 py-0.5 {{ $badgeClass }} text-[8px] font-black uppercase rounded border">
                                                        {{ $role->label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <div class="flex flex-col gap-0.5 mt-1">
                                                <p class="text-[10px] text-zinc-500 font-medium">{{ $u->email }} <span class="text-zinc-700 ml-2">#{{ $u->id }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    @if($isProfissional && $u->professionalProfile)
                                        <span class="text-[10px] text-white font-bold">{{ $u->professionalProfile->profession->name ?? 'Profissional' }}</span>
                                        <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $u->professionalProfile->specialty ?? 'Geral' }}</span>
                                        <span class="text-[9px] text-indigo-400 font-black uppercase tracking-tighter mt-1">
                                            <i class="fas fa-users mr-1"></i> {{ $u->patients->count() }} Pacientes
                                        </span>
                                    @elseif(($isAluno || $isPaciente) && $u->profile)
                                        <span class="text-[10px] text-white font-bold">{{ $u->profile->goal ?? 'Objetivo não definido' }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $latestWeight = $u->weightEntries()->latest('weighed_at')->first();
                                            @endphp
                                            @if($latestWeight)
                                                <span class="text-[9px] text-emerald-500 font-black uppercase tracking-tighter">
                                                    <i class="fas fa-weight mr-1"></i> {{ $latestWeight->weight_kg }}kg
                                                </span>
                                            @endif
                                            @if($u->professionals->count() > 0)
                                                <span class="text-[9px] text-blue-400 font-black uppercase tracking-tighter">
                                                    <i class="fas fa-user-md mr-1"></i> {{ $u->professionals->first()->name }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($u->department || $u->roles->contains('name', 'receptionist'))
                                        <span class="text-[10px] text-white font-bold">{{ $u->department ?? ($u->roles->first()?->label ?? 'Funcionário') }}</span>
                                        <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest italic">Departamento Administrativo</span>
                                    @else
                                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest italic">Sem informações adicionais</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-2 items-start">
                                    <div class="flex items-center gap-2">
                                        @if($u->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20">
                                                <i class="fas fa-check-circle text-[8px]"></i> Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-500/10 text-red-400 text-[9px] font-black uppercase rounded-lg border border-red-500/20">
                                                <i class="fas fa-ban text-[8px]"></i> Bloqueado
                                            </span>
                                        @endif
                                    </div>
                                    @if($u->is_premium)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-500/10 text-yellow-400 text-[9px] font-black uppercase rounded-lg border border-yellow-500/20">
                                            <i class="fas fa-crown text-[8px]"></i> Premium
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-zinc-300 text-xs font-bold">{{ $u->created_at->format('d/m/Y') }}</p>
                                <p class="text-[9px] text-zinc-600 font-black uppercase mt-0.5">{{ $u->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-xl">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.security.index') }}?user_id={{ $u->id }}" title="Segurança / Reset de Senha" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all shadow-xl">
                                        <i class="fas fa-key text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.lgpd.index') }}" title="Painel LGPD / Privacidade" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-xl">
                                        <i class="fas fa-fingerprint text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.lgpd.export-user', $u->id) }}" title="Exportar Dados (LGPD/JSON)" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-zinc-800 hover:text-white transition-all shadow-xl">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                    @php
                                        $userProfileName = $u->userProfile?->name;
                                        $isDeletable = in_array($userProfileName, ['aluno', 'paciente', 'professional']);
                                    @endphp
                                    
                                    @if((auth()->user()->isAdministrator() || auth()->user()->hasPermission('users.delete')) && $isDeletable && $u->id !== auth()->id() && ! $u->is_admin)
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-flex"
                                            data-confirm-delete
                                            data-confirm-title="Remover {{ $u->userProfile->label }}"
                                            data-confirm-message="Remover este {{ strtolower($u->userProfile->label) }} da base de dados? Esta ação é irreversível e removerá todos os vínculos associados.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir {{ $u->userProfile->label }}" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-xl">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
        <div class="px-8 py-6 bg-zinc-950/40 flex items-center justify-between border-t border-white/5">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest italic">Páginas de Resultados NexShape</span>
            <div class="flex gap-2">
                @if ($users->onFirstPage())
                    <span class="px-4 py-2 bg-zinc-900 text-zinc-700 text-[10px] font-black rounded-lg cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-zinc-900 text-zinc-300 text-[10px] font-black rounded-lg hover:bg-blue-600 hover:text-white transition-all">Anterior</a>
                @endif

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-zinc-900 text-zinc-300 text-[10px] font-black rounded-lg hover:bg-blue-600 hover:text-white transition-all">Próximo</a>
                @else
                    <span class="px-4 py-2 bg-zinc-900 text-zinc-700 text-[10px] font-black rounded-lg cursor-not-allowed">Próximo</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }

    .animate-progress-fast {
        width: 0%;
        animation: progress-fast 1.5s ease-in-out infinite;
    }
    @keyframes progress-fast {
        0% { width: 0%; margin-left: 0%; }
        50% { width: 50%; margin-left: 25%; }
        100% { width: 0%; margin-left: 100%; }
    }
</style>
@endsection
