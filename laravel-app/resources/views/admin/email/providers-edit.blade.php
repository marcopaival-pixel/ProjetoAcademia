@extends('layouts.admin')

@section('title', 'E-mail — '.$company->name)

@section('content')
<div class="space-y-10 animate-fade-in max-w-5xl mx-auto">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-bold">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Provedor de e-mail</h2>
            <p class="text-zinc-500 text-sm mt-1">{{ $company->name }}</p>
        </div>
        <a href="{{ route('admin.settings.email.providers') }}" class="text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-white">← Voltar</a>
    </div>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <form action="{{ route('admin.settings.email.providers.update', $company) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @csrf
            <div class="md:col-span-2 space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do provedor</label>
                <input type="text" name="nome_provedor" value="{{ old('nome_provedor', $config->nome_provedor) }}" required
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all"
                    placeholder="Ex.: Gmail produção">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de envio</label>
                <select name="tipo_envio" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    <option value="smtp" @selected(old('tipo_envio', $config->tipo_envio) === 'smtp')>SMTP</option>
                    <option value="api" @selected(old('tipo_envio', $config->tipo_envio) === 'api')>API (via SMTP do fornecedor)</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Preset / provedor</label>
                <select name="preset" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    @foreach(['custom' => 'Personalizado', 'gmail' => 'Gmail', 'outlook' => 'Outlook', 'hostgator' => 'HostGator', 'sendgrid' => 'SendGrid', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('preset', $config->preset ?? 'custom') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight px-1">SendGrid (API): utilize utilizador <code class="text-zinc-400">apikey</code> e a chave API como senha.</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Servidor SMTP (host)</label>
                <input type="text" name="smtp_host" value="{{ old('smtp_host', $config->smtp_host) }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all"
                    placeholder="smtp.exemplo.com">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Porta</label>
                <input type="number" name="smtp_porta" value="{{ old('smtp_porta', $config->smtp_porta ?: 587) }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Utilizador</label>
                <input type="text" name="smtp_usuario" value="{{ old('smtp_usuario', $config->smtp_usuario) }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha</label>
                <input type="password" name="smtp_senha" placeholder="••••••••"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight px-1">Deixe em branco para manter a atual.</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Criptografia</label>
                <select name="criptografia" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    <option value="tls" @selected(old('criptografia', $config->criptografia) === 'tls')>TLS</option>
                    <option value="ssl" @selected(old('criptografia', $config->criptografia) === 'ssl')>SSL</option>
                    <option value="none" @selected(old('criptografia', $config->criptografia) === 'none')>Nenhuma</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tempo limite (s)</label>
                <input type="number" name="timeout" value="{{ old('timeout', $config->timeout ?: 30) }}" min="5" max="600"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Limite envio / hora</label>
                <input type="number" name="limite_envio_por_hora" value="{{ old('limite_envio_por_hora', $config->limite_envio_por_hora ?: 100) }}" min="1"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail remetente</label>
                <input type="email" name="email_remetente" value="{{ old('email_remetente', $config->email_remetente) }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do remetente</label>
                <input type="text" name="nome_remetente" value="{{ old('nome_remetente', $config->nome_remetente) }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="ativo" value="1" class="rounded border-white/10 bg-zinc-950" @checked(old('ativo', $config->ativo ?? true))>
                    <span class="text-sm text-white font-bold">Ativo</span>
                </label>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-4 pt-4 border-t border-white/5">
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all">Salvar</button>
            </div>
        </form>
    </div>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <h3 class="text-lg font-black text-white tracking-tight mb-6">Testar envio</h3>
        <form action="{{ route('admin.settings.email.providers.test') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <input type="hidden" name="empresa_id" value="{{ $company->id }}">
            <div class="flex-1 w-full space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail destino</label>
                <input type="email" name="email_destino" value="{{ auth()->user()->email }}" required
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>
            <button type="submit" class="px-8 py-4 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl border border-white/5 hover:bg-emerald-600 transition-all">Testar envio</button>
        </form>
    </div>

    <div class="flex justify-end">
        <form action="{{ route('admin.settings.email.providers.deactivate', $company) }}" method="POST"
            data-confirm-delete
            data-confirm-title="Desativar envio"
            data-confirm-message="Desativar o envio de e-mail para esta empresa?">
            @csrf
            <button type="submit" class="px-8 py-4 bg-red-600/10 text-red-400 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-red-500/20 hover:bg-red-600 hover:text-white transition-all">Desativar envio</button>
        </form>
    </div>
</div>
@endsection
