@extends('layouts.admin')

@section('title', 'Gestão de Especialidades')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        console.log('Registering specialtyComponent...');
        Alpine.data('specialtyComponent', () => ({
            showModal: @js(isset($editingEspecialidade)),
            showImportModal: false,
            isImporting: false,
            editMode: @js(isset($editingEspecialidade)),
            especialidade: @js($editingEspecialidade ?? [
                'id' => '',
                'nome' => '',
                'codigo' => '',
                'categoria' => '',
                'icone' => '',
                'status' => 'Ativo',
                'profession_id' => ''
            ]),
            openCreate() {
                console.log('Opening create modal...');
                this.editMode = false;
                this.especialidade = { id: '', nome: '', codigo: '', categoria: '', icone: '', status: 'Ativo', profession_id: '' };
                this.showModal = true;
            },
            openEdit(item) {
                console.log('Opening edit modal for:', item);
                this.editMode = true;
                this.especialidade = { ...item };
                this.showModal = true;
            },
            startImport() {
                this.isImporting = true;
            }
        }));
    });
</script>

<div class="space-y-10 animate-fade-in" x-data="specialtyComponent" x-cloak>
    <!-- Header & Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Especialidades Profissionais</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Configuração global de expertises do sistema</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.especialidades.export') }}" class="px-6 py-4 bg-zinc-900 border border-white/5 text-zinc-400 text-xs font-black uppercase tracking-widest rounded-2xl hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-file-export"></i> Exportar Lista
            </a>
            <button @click="showImportModal = true" class="px-6 py-4 bg-zinc-900 border border-white/5 text-zinc-400 text-xs font-black uppercase tracking-widest rounded-2xl hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-file-import"></i> Importar Lista
            </button>
            <button @click="openCreate()" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-3 shadow-xl shadow-blue-600/20 group">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i> Nova Especialidade
            </button>
        </div>
    </div>

    <!-- Error Messages (Import Validation) -->
    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 p-6 rounded-[2rem] animate-fade-in">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <h4 class="text-white font-black text-xs uppercase tracking-widest">Erros na Importação</h4>
            </div>
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-[10px] text-red-400 font-medium list-disc ml-5">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.especialidades.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou código da especialidade..." 
                    class="w-full bg-zinc-950 border border-white/5 p-4 pl-14 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>
            <button type="submit" class="px-10 bg-zinc-800 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-700 transition-all">Filtrar</button>
            @if(request('search'))
                <a href="{{ route('admin.especialidades.index') }}" class="px-6 bg-red-500/10 text-red-500 flex items-center justify-center rounded-2xl hover:bg-red-500/20 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[9px] font-black uppercase tracking-[0.25em] border-b border-white/5 bg-zinc-950/20">
                        <th class="px-8 py-6">Código</th>
                        <th class="px-8 py-6">Especialidade</th>
                        <th class="px-8 py-6">Profissão</th>
                        <th class="px-8 py-6">Categoria</th>
                        <th class="px-8 py-6">Ícone</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6">Cadastro</th>
                        <th class="px-8 py-6 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($especialidades as $item)
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs text-blue-400 font-bold bg-blue-400/5 px-2 py-1 rounded border border-blue-400/10">
                                    {{ $item->codigo }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-white leading-none">{{ $item->nome }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] text-blue-500 font-black uppercase tracking-widest bg-blue-500/10 px-2 py-1 rounded">
                                    {{ $item->profession?->name ?? 'Não Vinculada' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">{{ $item->categoria }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-400 group-hover:text-blue-400 transition-colors">
                                    <i class="{{ $item->icone }} text-lg"></i>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($item->status === 'Ativo')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20">
                                        <i class="fas fa-check-circle text-[8px]"></i> Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-500/10 text-red-400 text-[9px] font-black uppercase rounded-lg border border-red-500/20">
                                        <i class="fas fa-ban text-[8px]"></i> Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-zinc-500 text-[10px] font-bold">{{ $item->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit(@js($item))" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <form action="{{ route('admin.especialidades.toggle-status', $item) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all" title="{{ $item->status === 'Ativo' ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-power-off text-xs"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.especialidades.destroy', $item) }}" method="POST" class="inline"
                                          data-confirm-delete="true"
                                          data-confirm-title="Excluir Especialidade"
                                          data-confirm-message="Deseja remover '{{ $item->nome }}'? Esta ação afetará filtros e buscas de profissionais.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-20">
                                    <i class="fas fa-tags text-6xl"></i>
                                    <p class="text-sm font-black uppercase tracking-widest">Nenhuma especialidade encontrada</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($especialidades->hasPages())
            <div class="px-8 py-6 bg-zinc-950/40 flex items-center justify-between border-t border-white/5">
                <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest italic">Paginação de Especialidades</span>
                <div class="flex gap-2">
                    {{ $especialidades->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
        @endif
    </div> <!-- Close glass-card -->

    <!-- Modal Form -->
    <div class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80" 
         x-show="showModal" 
         x-cloak
         @keydown.escape.window="showModal = false">
        <div class="bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden" @click.away="showModal = false">
                <div class="p-8 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight" x-text="editMode ? 'Editar Especialidade' : 'Nova Especialidade'"></h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Preencha todos os campos obrigatórios</p>
                    </div>
                    <button @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="editMode ? '{{ url('admin/especialidades') }}/' + especialidade.id + '/update' : '{{ route('admin.especialidades.store') }}'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da Especialidade</label>
                            <input type="text" name="nome" x-model="especialidade.nome" required placeholder="Ex: Nutrição Esportiva"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Código Único</label>
                            <input type="text" name="codigo" x-model="especialidade.codigo" required placeholder="Ex: NUT-ESP"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Profissão Vinculada</label>
                            <select name="profession_id" x-model="especialidade.profession_id" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="">Nenhuma (Global)</option>
                                @foreach($professions as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Categoria de Exibição</label>
                            <select name="categoria" x-model="especialidade.categoria" required 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="">Selecione...</option>
                                <option value="Fitness">Fitness</option>
                                <option value="Saúde">Saúde</option>
                                <option value="Reabilitação">Reabilitação</option>
                                <option value="Nutrição">Nutrição</option>
                                <option value="Bem-estar">Bem-estar</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status</label>
                            <select name="status" x-model="especialidade.status" required 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Escolha um Ícone</label>
                        <div class="grid grid-cols-6 sm:grid-cols-8 gap-3 bg-zinc-950 p-4 rounded-2xl border border-white/5 max-h-40 overflow-y-auto custom-scrollbar">
                            @php
                                $icons = [
                                    'fas fa-user-md', 'fas fa-stethoscope', 'fas fa-heartbeat', 'fas fa-apple-alt', 
                                    'fas fa-dumbbell', 'fas fa-running', 'fas fa-spa', 'fas fa-medkit', 
                                    'fas fa-first-aid', 'fas fa-hospital', 'fas fa-pills', 'fas fa-vial',
                                    'fas fa-weight', 'fas fa-walking', 'fas fa-bicycle', 'fas fa-swimmer',
                                    'fas fa-yoga', 'fas fa-brain', 'fas fa-smile', 'fas fa-leaf'
                                ];
                            @endphp
                            @foreach($icons as $icon)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="icone" value="{{ $icon }}" x-model="especialidade.icone" class="hidden peer" required>
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl border border-white/5 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white text-zinc-600 hover:bg-white/5 transition-all">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showModal = false" class="px-8 py-4 text-zinc-500 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">Cancelar</button>
                        <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                            <span x-text="editMode ? 'Salvar Alterações' : 'Criar Especialidade'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Import Modal -->
    <div class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80" 
         x-show="showImportModal" 
         x-cloak
         @keydown.escape.window="!isImporting && (showImportModal = false)">
        <div class="bg-zinc-900 border border-white/10 w-full max-w-xl rounded-[2.5rem] shadow-2xl overflow-hidden" @click.away="!isImporting && (showImportModal = false)">
                <div class="p-8 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight">Importar Especialidades</h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Sincronize sua base de dados via CSV</p>
                    </div>
                    <button @click="showImportModal = false" x-show="!isImporting" class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Template Download -->
                    <div class="bg-blue-600/5 border border-blue-600/20 p-6 rounded-3xl flex items-center justify-between">
                        <div>
                            <h4 class="text-white text-xs font-black uppercase tracking-widest">Planilha Modelo</h4>
                            <p class="text-[10px] text-zinc-500 font-medium mt-1">Utilize nosso padrão para evitar erros</p>
                        </div>
                        <a href="{{ route('admin.especialidades.template') }}" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                            <i class="fas fa-download mr-2"></i> Baixar Modelo
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('admin.especialidades.import') }}" method="POST" enctype="multipart/form-data" @submit="startImport()">
                        @csrf
                        <div class="space-y-4" x-show="!isImporting">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Selecione o arquivo (.csv)</label>
                            <div class="relative group">
                                <input type="file" name="file" required accept=".csv"
                                    class="w-full bg-zinc-950 border border-white/5 p-8 rounded-[2rem] text-zinc-500 text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all file:hidden cursor-pointer group-hover:border-blue-600/50">
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-blue-600 mb-2"></i>
                                        <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Clique ou arraste seu arquivo aqui</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress / Loading -->
                        <div class="space-y-6 py-4" x-show="isImporting">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-blue-400 font-black uppercase tracking-[0.2em] animate-pulse">Importando dados...</span>
                                <span class="text-[10px] text-zinc-500 font-black">Processando</span>
                            </div>
                            <div class="w-full h-3 bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full bg-blue-600 animate-progress"></div>
                            </div>
                            <p class="text-[10px] text-zinc-500 text-center leading-relaxed font-medium">Por favor, não feche esta janela. Estamos validando e cadastrando suas especialidades na base de dados.</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-6" x-show="!isImporting">
                            <button type="button" @click="showImportModal = false" class="px-8 py-4 text-zinc-500 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">Cancelar</button>
                            <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                                Iniciar Importação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .animate-progress {
        width: 0%;
        animation: progress 2s ease-in-out infinite;
    }
    [x-cloak] { display: none !important; }

    @keyframes progress {
        0% { width: 0%; margin-left: 0%; }
        50% { width: 50%; margin-left: 25%; }
        100% { width: 0%; margin-left: 100%; }
    }
</style>

@push('scripts')
<script>
    console.log('Specialties scripts pushed');
</script>
@endpush
@endsection
