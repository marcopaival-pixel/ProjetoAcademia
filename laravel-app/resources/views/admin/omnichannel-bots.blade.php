@extends('layouts.admin')

@section('title', 'Configuração de Chatbots')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/omnichannel.css') }}">
    <style>
        .bot-card { background: var(--omni-card); border: 1px solid var(--omni-border); border-radius: 20px; padding: 24px; margin-bottom: 24px; transition: all 0.3s; }
        .bot-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .step-item { border-left: 2px solid var(--accent); padding-left: 20px; margin-bottom: 20px; position: relative; }
        .step-item::before { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); }
        .badge-type { font-size: 10px; padding: 2px 8px; border-radius: 99px; background: rgba(var(--accent-rgb), 0.1); color: var(--accent); text-transform: uppercase; font-weight: 700; }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Gerenciador de Bots</h1>
            <p class="text-muted">Crie roteiros e automatize seu atendimento omnichannel.</p>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Novo Bot
        </button>
    </div>

    @php
        $bots = \App\Models\OmniBot::with(['steps.options'])->where('company_id', 1)->get();
    @endphp

    @if($bots->count() == 0)
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-12 text-center">
            <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9l-.707.707M12 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"></path></svg>
            </div>
            <h2 class="text-xl font-bold mb-2">Nenhum robô configurado</h2>
            <p class="text-muted mb-8">Você ainda não criou nenhum roteiro de atendimento para sua empresa.</p>
            <a href="/setup-bot-demo" class="text-indigo-400 hover:underline">Configurar Roteiro de Demonstração Automatizado</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6">
            @foreach($bots as $bot)
                <div class="bot-card">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold">{{ $bot->name }}</h3>
                                <span class="{{ $bot->is_active ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }} text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">
                                    {{ $bot->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <p class="text-sm text-muted mt-1">WhatsApp: {{ $bot->whatsapp_phone ?? 'Não configurado' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="p-2 hover:bg-white/5 rounded-lg text-muted" title="Editar Bot"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                            <button class="p-2 hover:bg-red-500/10 rounded-lg text-red-400" title="Excluir"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-zinc-800 pt-6">
                        <h4 class="text-xs uppercase font-bold text-zinc-500 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                            Roteiro de Atendimento (Passos)
                        </h4>
                        
                        <div class="space-y-4">
                            @foreach($bot->steps as $step)
                                <div class="step-item">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="badge-type">{{ $step->type }}</span>
                                        <span class="font-bold text-sm">{{ $step->label }}</span>
                                        @if($step->is_start) <span class="text-[9px] bg-indigo-500 text-white px-1.5 py-0.5 rounded">INÍCIO</span> @endif
                                    </div>
                                    <p class="text-sm text-zinc-400 line-clamp-1">{{ $step->content }}</p>
                                    @if($step->options->count() > 0)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($step->options as $opt)
                                                <span class="text-[10px] bg-white/5 border border-white/10 px-2 py-1 rounded">
                                                    {{ $opt->trigger_value }}: {{ $opt->label }} → #{{ $opt->destination_step_id }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
