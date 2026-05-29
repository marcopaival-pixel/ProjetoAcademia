@extends('layouts.admin')

@section('title', 'Cadastros pendentes')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-12">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Novos cadastros</h2>
            <p class="text-zinc-500 text-xs mt-1 font-bold uppercase tracking-widest">Aceite ou recuse pedidos de acesso de alunos</p>
        </div>
        @php($pendingCount = \App\Models\User::where('registration_approval_status', 'pending')->where('is_admin', false)->count())
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 text-xs font-black uppercase tracking-widest">
            <i class="fas fa-hourglass-half"></i> {{ $pendingCount }} pendente(s)
        </span>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[2rem] overflow-hidden shadow-xl">
        @forelse ($pending as $u)
            <div class="p-6 sm:p-8 border-b border-white/5 last:border-0">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-800 flex items-center justify-center text-amber-500 shrink-0 border border-white/5">
                        <i class="fas fa-user-plus text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-white font-black text-lg truncate">{{ $u->name }}</p>
                        <p class="text-zinc-500 text-sm truncate">{{ $u->email }}</p>
                        <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mt-2">
                            Pedido em {{ $u->created_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <form action="{{ route('admin.registrations.approve', $u) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-check mr-2"></i> Aceitar
                        </button>
                    </form>
                    <button type="button"
                        id="open-reject-{{ $u->id }}"
                        class="px-6 py-3 rounded-xl bg-zinc-800 hover:bg-red-600/80 border border-white/10 text-zinc-300 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-times mr-2"></i> Não aceitar
                    </button>
                </div>
                </div>

                <div id="reject-modal-{{ $u->id }}" class="hidden fixed inset-0 z-[400] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm">
                    <div class="bg-zinc-900 border border-red-500/20 rounded-2xl p-6 max-w-md w-full shadow-2xl">
                        <h3 class="text-lg font-black text-white mb-2">Recusar {{ $u->name }}</h3>
                        <p class="text-zinc-500 text-xs mb-4">Opcional: mensagem para o utilizador (correio interno).</p>
                        <form action="{{ route('admin.registrations.reject', $u) }}" method="POST" class="space-y-4">
                            @csrf
                            <textarea name="note" rows="3" maxlength="2000" placeholder="Motivo da recusa (opcional)"
                                class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-zinc-600 outline-none focus:ring-2 focus:ring-red-500/40"></textarea>
                            <div class="flex gap-3 justify-end">
                                <button type="button" class="close-reject px-4 py-2 rounded-xl text-zinc-400 hover:text-white text-xs font-bold uppercase" data-close="{{ $u->id }}">Cancelar</button>
                                <button type="submit" class="px-8 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white text-[10px] font-black uppercase tracking-widest">Confirmar recusa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 text-center text-zinc-500">
                <i class="fas fa-check-circle text-4xl text-emerald-500/40 mb-4"></i>
                <p class="font-bold uppercase tracking-widest text-xs">Nenhum cadastro pendente</p>
            </div>
        @endforelse
    </div>

    @if ($pending->hasPages())
        <div class="flex justify-center">
            {{ $pending->links() }}
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('[id^="open-reject-"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('id').replace('open-reject-', '');
            var m = document.getElementById('reject-modal-' + id);
            if (m) m.classList.remove('hidden');
        });
    });
    document.querySelectorAll('.close-reject').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-close');
            var m = document.getElementById('reject-modal-' + id);
            if (m) m.classList.add('hidden');
        });
    });
    document.querySelectorAll('[id^="reject-modal-"]').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection
