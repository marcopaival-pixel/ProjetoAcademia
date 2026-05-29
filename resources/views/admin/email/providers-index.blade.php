@extends('layouts.admin')

@section('title', 'Provedores de E-mail')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Header Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight italic uppercase">Configuração <span class="text-emerald-500">Multitenant</span></h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie credenciais SMTP individuais por unidade de negócio</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-zinc-950 border border-white/5 p-4 rounded-2xl flex items-center gap-3">
                <i data-lucide="info" class="w-4 h-4 text-emerald-500"></i>
                <p class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">
                    Fallback ativo: <span class="text-white">{{ \App\Models\AdminSetting::get('mail_host', 'Configurações Globais') }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 flex items-center gap-5 border-emerald-500/10">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Total Unidades</p>
                <h4 class="text-2xl font-black text-white italic tracking-tighter">{{ $companies->count() }}</h4>
            </div>
        </div>
        <div class="glass-card p-6 flex items-center gap-5 border-emerald-500/10">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <i data-lucide="mail-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Configuradas</p>
                <h4 class="text-2xl font-black text-white italic tracking-tighter">{{ $companies->filter(fn($c) => $c->configuracaoEmail)->count() }}</h4>
            </div>
        </div>
        <div class="glass-card p-6 flex items-center gap-5 border-rose-500/10">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                <i data-lucide="mail-warning" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Pendentes</p>
                <h4 class="text-2xl font-black text-white italic tracking-tighter">{{ $companies->filter(fn($c) => !$c->configuracaoEmail)->count() }}</h4>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="glass-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-950/80 border-b border-white/5">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Unidade / Academia</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Provedor SMTP</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($companies as $c)
                        @php($cfg = $c->configuracaoEmail)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-600 group-hover:text-white group-hover:border-emerald-500/30 transition-all">
                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-white uppercase tracking-tight">{{ $c->name }}</p>
                                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-0.5">CNPJ: {{ $c->tax_id ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $cfg?->nome_provedor ?? 'Utilizando Fallback Global' }}</span>
                                    @if($cfg)
                                        <span class="text-[9px] text-zinc-600 font-bold tracking-widest uppercase mt-1">{{ $cfg->smtp_host }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if(!$cfg)
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-zinc-900/50 border border-white/5 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                                        <span class="text-[9px] font-black uppercase text-zinc-600 tracking-widest">Global Fallback</span>
                                    </div>
                                @elseif($cfg->ativo)
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-[9px] font-black uppercase text-emerald-500 tracking-widest">Ativo</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-500/10 border border-rose-500/20 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span class="text-[9px] font-black uppercase text-rose-500 tracking-widest">Pausado</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('admin.settings.email.providers.edit', $c) }}" class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-zinc-900 border border-white/5 text-zinc-400 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 hover:border-emerald-500 transition-all active:scale-95 shadow-xl">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    Configurar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-20">
                                    <i data-lucide="inbox" class="w-12 h-12"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">Nenhuma empresa mapeada para o sistema</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Context -->
    <div class="p-6 bg-zinc-950/50 border border-white/5 rounded-3xl flex items-start gap-4">
        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 shrink-0">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <div>
            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Nota de Governança</h4>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                As configurações de e-mail por empresa têm prioridade sobre o fallback global. 
                Isso permite que cada unidade (Academy) tenha sua própria identidade de remetente e reputação de IP junto aos provedores de e-mail.
            </p>
        </div>
    </div>
</div>
@endsection
