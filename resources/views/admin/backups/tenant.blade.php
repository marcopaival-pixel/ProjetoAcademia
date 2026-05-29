@extends('layouts.admin')

@section('title', 'Backup da Clínica: ' . $company->name)

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-zinc-500 mb-1">
                <a href="{{ route('admin.backups.index') }}" class="hover:text-emerald-600 transition-colors">Segurança</a>
                <i class="lucide-chevron-right w-4 h-4"></i>
                <span>Backup por Clínica</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $company->name }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Gerencie backups isolados para esta unidade de negócio.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.backups.tenant.create', $company->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all shadow-sm shadow-emerald-500/20">
                    <i class="lucide-download-cloud w-4 h-4"></i>
                    <span>Exportar Dados da Clínica</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 rounded-xl flex gap-3">
        <i class="lucide-info w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5"></i>
        <div class="text-sm text-blue-700 dark:text-blue-300">
            <strong>Backup Isolado:</strong> Este processo exporta apenas os dados vinculados a esta empresa (Unidades, Usuários, Planos de Treino, Histórico, etc.). Ideal para migrações ou recuperação de erros específicos desta clínica sem afetar o restante do SaaS.
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800">
            <h3 class="font-semibold text-zinc-900 dark:text-white">Backups Disponíveis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-950 text-zinc-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-medium">Data da Exportação</th>
                        <th class="px-6 py-4 font-medium">Ficheiro</th>
                        <th class="px-6 py-4 font-medium">Tamanho</th>
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
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.backups.tenant.download', [$company->id, $backup['file_name']]) }}" 
                                   class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                   title="Download">
                                    <i class="lucide-download w-4 h-4"></i>
                                </a>
                                <button onclick="confirmRestore('{{ $backup['file_name'] }}')"
                                        class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                        title="Restaurar Dados">
                                    <i class="lucide-rotate-ccw w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.backups.tenant.delete', [$company->id, $backup['file_name']]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Excluir este backup permanentemente?')"
                                            class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                            title="Excluir">
                                        <i class="lucide-trash-2 w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-zinc-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="lucide-archive w-12 h-12 text-zinc-300"></i>
                                <p>Nenhum backup isolado encontrado para esta clínica.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Restauração Crítica -->
<div id="restoreModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-rose-200 dark:border-rose-900/30">
            <div class="p-6">
                <div class="flex items-center gap-4 text-rose-600 mb-4">
                    <div class="p-3 bg-rose-100 dark:bg-rose-900/30 rounded-full">
                        <i class="lucide-alert-octagon w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold">Restauração Crítica</h3>
                </div>
                
                <div class="space-y-4 text-zinc-600 dark:text-zinc-400 text-sm">
                    <p>Você está prestes a restaurar os dados da clínica <strong>{{ $company->name }}</strong>.</p>
                    
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-lg border border-zinc-200 dark:border-zinc-800">
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="lucide-check-circle-2 w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span>Apenas dados desta clínica serão afetados.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="lucide-x-circle w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                                <span>Dados atuais da clínica serão <strong>apagados</strong> antes da importação.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="lucide-shield-check w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                                <span>As outras clínicas do sistema permanecerão intactas.</span>
                            </li>
                        </ul>
                    </div>

                    <p class="font-medium text-zinc-900 dark:text-white">Confirma a operação para o ficheiro <span id="displayFileName" class="text-rose-600"></span>?</p>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-950 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                <form id="restoreForm" action="{{ route('admin.backups.tenant.restore', $company->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="file_name" id="restoreFileName">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500">
                        Sim, Restaurar Clínica
                    </button>
                </form>
                <button type="button" onclick="closeRestoreModal()" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmRestore(fileName) {
        document.getElementById('restoreFileName').value = fileName;
        document.getElementById('displayFileName').textContent = fileName;
        document.getElementById('restoreModal').classList.remove('hidden');
    }

    function closeRestoreModal() {
        document.getElementById('restoreModal').classList.add('hidden');
    }
</script>
@endsection
