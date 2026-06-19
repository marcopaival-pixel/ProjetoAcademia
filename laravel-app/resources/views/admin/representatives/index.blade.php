@extends('layouts.admin')

@section('title', 'Gestão de Representantes — NexShape Pro')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Gestão de <span class="text-emerald-500">Representantes</span></h1>
            <p class="text-sm text-zinc-500 font-medium italic">Aprove e gerencie seus parceiros comerciais.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-[2rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Total de Representantes</p>
            <p class="text-2xl font-bold text-white tracking-tight">{{ $representatives->total() }}</p>
        </div>
    </div>

    <div class="rounded-[2.5rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-800/50">
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Representante</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Cadastro em</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse($representatives as $rep)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center font-black text-zinc-500 italic">
                                    {{ substr($rep->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white tracking-tight">{{ $rep->name }}</p>
                                    <p class="text-[10px] text-zinc-500 font-medium">{{ $rep->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($rep->status === 'PENDENTE_APROVACAO' || $rep->status === 'pending' || $rep->status === 'PENDENTE')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-500 font-black uppercase tracking-widest italic">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pendente
                                </span>
                            @elseif($rep->isActive())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] text-emerald-500 font-black uppercase tracking-widest italic">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-[10px] text-red-400 font-black uppercase tracking-widest italic">
                                    Recusado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-[11px] text-zinc-500 font-medium">
                            {{ $rep->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-2">
                                @if($rep->status === 'PENDENTE_APROVACAO' || $rep->status === 'pending' || $rep->status === 'PENDENTE')
                                    <button type="button" id="open-approve-{{ $rep->id }}" class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-emerald-500/5" title="Aprovar Representante">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button" id="open-reject-{{ $rep->id }}" class="p-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/5" title="Recusar Representante">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Approve Modal -->
                                    <div id="approve-modal-{{ $rep->id }}" class="hidden fixed inset-0 z-[400] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm">
                                        <div class="bg-zinc-900 border border-emerald-500/20 rounded-2xl p-6 max-w-md w-full shadow-2xl">
                                            <h3 class="text-lg font-black text-white mb-2">Aprovar {{ $rep->name }}</h3>
                                            <p class="text-zinc-500 text-xs mb-4">Tem a certeza que deseja aprovar este parceiro comercial?</p>
                                            
                                            <form action="{{ route('admin.representatives.approve', $rep) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div class="flex gap-3 justify-end mt-4">
                                                    <button type="button" class="close-modal px-4 py-2 rounded-xl text-zinc-400 hover:text-white text-xs font-bold uppercase" data-close="approve-modal-{{ $rep->id }}">Cancelar</button>
                                                    <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">Aprovar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div id="reject-modal-{{ $rep->id }}" class="hidden fixed inset-0 z-[400] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm">
                                        <div class="bg-zinc-900 border border-red-500/20 rounded-2xl p-6 max-w-md w-full shadow-2xl">
                                            <h3 class="text-lg font-black text-white mb-2">Recusar {{ $rep->name }}</h3>
                                            <p class="text-zinc-500 text-xs mb-4">Deseja realmente recusar o cadastro deste parceiro comercial?</p>
                                            
                                            <form action="{{ route('admin.representatives.reject', $rep) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div class="flex gap-3 justify-end mt-4">
                                                    <button type="button" class="close-modal px-4 py-2 rounded-xl text-zinc-400 hover:text-white text-xs font-bold uppercase" data-close="reject-modal-{{ $rep->id }}">Cancelar</button>
                                                    <button type="submit" class="px-8 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-red-500/20">Recusar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('admin.users.edit', $rep->id) }}" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-xl" title="Editar Perfil">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    @if($rep->id !== auth()->id() && !$rep->is_admin)
                                        @php
                                            $isUserFullyActive = ($rep->status === 'active' || $rep->status === 'ATIVO' || $rep->status === 'APROVADO');
                                        @endphp
                                        <form action="{{ route('admin.users.toggle-status', $rep->id) }}" method="POST" class="inline-flex">
                                            @csrf
                                            <button type="submit" 
                                                title="{{ $isUserFullyActive ? 'Bloquear Acesso' : 'Desbloquear / Liberar Acesso Total' }}"
                                                class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center transition-all shadow-xl {{ $isUserFullyActive ? 'text-zinc-500 hover:bg-red-600 hover:text-white hover:border-red-600' : 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20 hover:bg-emerald-600 hover:text-white' }}">
                                                <i class="fas {{ $isUserFullyActive ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.security.index') }}?user_id={{ $rep->id }}" title="Segurança / Reset de Senha" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all shadow-xl">
                                        <i class="fas fa-key text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.lgpd.index') }}" title="Painel LGPD / Privacidade" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-xl">
                                        <i class="fas fa-fingerprint text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.lgpd.export-user', $rep->id) }}" title="Exportar Dados (LGPD/JSON)" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-zinc-800 hover:text-white transition-all shadow-xl">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                    
                                    @if(auth()->user()->isAdministrator() || auth()->user()->hasPermission('users.delete'))
                                        <form action="{{ route('admin.users.destroy', $rep) }}" method="POST" class="inline-flex"
                                            data-confirm-delete
                                            data-confirm-title="Remover Representante"
                                            data-confirm-message="Remover este representante da base de dados? Esta ação é irreversível e removerá todos os vínculos associados.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir Representante" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-xl">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i data-lucide="users-2" class="w-10 h-10 text-zinc-800"></i>
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">Nenhum representante encontrado</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($representatives->hasPages())
            <div class="px-6 py-4 border-t border-zinc-800/50">
                {{ $representatives->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    document.querySelectorAll('[id^="open-approve-"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('id').replace('open-approve-', '');
            var m = document.getElementById('approve-modal-' + id);
            if (m) m.classList.remove('hidden');
        });
    });
    
    document.querySelectorAll('[id^="open-reject-"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('id').replace('open-reject-', '');
            var m = document.getElementById('reject-modal-' + id);
            if (m) m.classList.remove('hidden');
        });
    });

    document.querySelectorAll('.close-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-close');
            var m = document.getElementById(id);
            if (m) m.classList.add('hidden');
        });
    });

    document.querySelectorAll('[id^="approve-modal-"], [id^="reject-modal-"]').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>
@endsection
