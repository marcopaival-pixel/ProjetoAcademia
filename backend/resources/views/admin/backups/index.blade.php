@extends('layouts.admin')

@section('title', 'Gestão de Backups')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Segurança & Backups</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Gerencie as cópias de segurança do banco de dados e arquivos do sistema.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.backups.create') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all shadow-sm shadow-emerald-500/20">
                    <i class="lucide-database w-4 h-4"></i>
                    <span>Novo Backup Manual</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <i class="lucide-shield-check w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-sm text-zinc-500">Status do Sistema</span>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Protegido</h3>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <i class="lucide-cloud-upload w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-sm text-zinc-500">Destino Remoto</span>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Amazon S3</h3>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <i class="lucide-calendar-clock w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-sm text-zinc-500">Próximo Backup</span>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">02:00 AM (Daily)</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800">
            <h3 class="font-semibold text-zinc-900 dark:text-white">Histórico de Backups</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-950 text-zinc-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">Data & Hora</th>
                        <th class="px-6 py-4 font-medium">Nome do Ficheiro</th>
                        <th class="px-6 py-4 font-medium">Tamanho</th>
                        <th class="px-6 py-4 font-medium">Localização</th>
                        <th class="px-6 py-4 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $backup['last_modified'] }}
                        </td>
                        <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                            {{ $backup['file_name'] }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-md text-xs">
                                {{ $backup['file_size'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($backup['disk'] === 'backup')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i class="lucide-cloud w-3 h-3"></i> Cloud
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                                    <i class="lucide-hard-drive w-3 h-3"></i> Local
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.backups.download', [$backup['disk'], $backup['file_name']]) }}" 
                                   class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                   title="Download">
                                    <i class="lucide-download w-4 h-4"></i>
                                </a>
                                <button onclick="confirmRestore('{{ $backup['disk'] }}', '{{ $backup['file_name'] }}')"
                                        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                        title="Restaurar">
                                    <i class="lucide-rotate-ccw w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.backups.delete', [$backup['disk'], $backup['file_name']]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Tem certeza que deseja excluir este backup?')"
                                            class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                            title="Excluir">
                                        <i class="lucide-trash-2 w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-zinc-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="lucide-database-zap w-12 h-12 text-zinc-300"></i>
                                <p>Nenhum backup encontrado nos discos configurados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tenant Backup Section -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden mt-8">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-zinc-900 dark:text-white">Backups por Clínica (Multi-Tenant)</h3>
                <p class="text-xs text-zinc-500">Gestão isolada de dados por unidade de negócio.</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($companies as $company)
                <a href="{{ route('admin.backups.tenant.index', $company->id) }}" 
                   class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/30 group-hover:text-emerald-600 transition-colors">
                            <i class="lucide-building-2 w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-zinc-900 dark:text-white group-hover:text-emerald-600 transition-colors">{{ $company->name }}</span>
                            <span class="block text-xs text-zinc-500">{{ $company->slug }}</span>
                        </div>
                    </div>
                    <i class="lucide-arrow-right w-4 h-4 text-zinc-400 group-hover:text-emerald-600 transition-colors"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Restauração -->
<div id="restoreModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-zinc-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="lucide-alert-triangle w-6 h-6"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-zinc-900 dark:text-white">Atenção Crítica</h3>
                        <div class="mt-2 text-sm text-zinc-500">
                            <p>Você está prestes a restaurar o banco de dados. Isso irá **sobrescrever** todos os dados atuais do sistema pelo conteúdo do backup selecionado. Esta ação não pode ser desfeita.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-zinc-50 dark:bg-zinc-950 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <form id="restoreForm" action="{{ route('admin.backups.restore') }}" method="POST">
                    @csrf
                    <input type="hidden" name="disk" id="restoreDisk">
                    <input type="hidden" name="file_name" id="restoreFileName">
                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 sm:w-auto">
                        Sim, Restaurar Agora
                    </button>
                </form>
                <button type="button" onclick="closeRestoreModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 sm:mt-0 sm:w-auto">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmRestore(disk, fileName) {
        document.getElementById('restoreDisk').value = disk;
        document.getElementById('restoreFileName').value = fileName;
        document.getElementById('restoreModal').classList.remove('hidden');
    }

    function closeRestoreModal() {
        document.getElementById('restoreModal').classList.add('hidden');
    }
</script>
@endsection
