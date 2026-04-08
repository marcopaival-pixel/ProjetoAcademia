@extends('layouts.admin')

@section('title', 'Avisos & Comunicações HUD')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Comunicações <span class="text-blue-500">Globais</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Gestão de alertas críticos e informativos para toda a base de usuários.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase rounded-xl border border-blue-500/20 tracking-widest">
                <i class="fas fa-broadcast-tower me-2"></i>Link Ativo
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10 items-start">
        <!-- New Announcement Form -->
        <div class="xl:col-span-5 bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 shadow-2xl">
            <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                <i class="fas fa-plus-circle text-blue-500"></i>Novo Alerta de Sistema
            </h3>
            
            <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="content" class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Conteúdo do Alerta</label>
                    <textarea id="content" name="content" rows="4" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-800" placeholder="Ex: Manutenção agendada para às 23h..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="type" class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Protocolo Visual</label>
                        <select id="type" name="type" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="info">Informação (Azul)</option>
                            <option value="success">Sucesso (Verde)</option>
                            <option value="warning">Alerta (Amarelo)</option>
                            <option value="danger">Crítico (Vermelho)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="ends_at" class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Expiração (Opcional)</label>
                        <input type="date" id="ends_at" name="ends_at" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all text-zinc-400">
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-widest text-xs">
                    Disparar Comunicação
                </button>
            </form>
        </div>

        <!-- Active Announcements -->
        <div class="xl:col-span-7 space-y-6">
            <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <i class="fas fa-history text-zinc-500"></i>Timeline de Transmissão
            </h3>
            
            @forelse($announcements as $an)
                @php
                    $colors = [
                        'info' => 'blue',
                        'success' => 'emerald',
                        'warning' => 'amber',
                        'danger' => 'red'
                    ];
                    $c = $colors[$an->type] ?? 'blue';
                @endphp
                <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl hover:border-{{ $c }}-500/30 transition-all overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-{{ $c }}-500"></div>
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-0.5 bg-{{ $c }}-500/10 text-{{ $c }}-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-{{ $c }}-500/20">
                                {{ strtoupper($an->type) }}
                            </span>
                            <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-tight">Criado em {{ $an->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-zinc-300 leading-relaxed font-medium mb-2">{{ $an->content }}</p>
                        @if($an->ends_at)
                            <div class="flex items-center gap-2 text-[10px] text-zinc-500 font-bold">
                                <i class="far fa-clock"></i> Expira em {{ \Carbon\Carbon::parse($an->ends_at)->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.announcements.delete', $an->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-700 hover:text-red-500 hover:border-red-500/30 transition-all shadow-inner">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-16 bg-zinc-900/10 border border-dashed border-white/5 rounded-[3.5rem] text-center">
                    <div class="w-16 h-16 bg-zinc-900 rounded-[2rem] flex items-center justify-center text-zinc-700 mx-auto mb-6">
                        <i class="fas fa-comment-slash text-2xl"></i>
                    </div>
                    <h4 class="text-white font-black uppercase tracking-widest text-xs">Sem Histórico de Avisos</h4>
                    <p class="text-zinc-600 text-sm mt-2">Inicie uma nova transmissão para informar os usuários.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
