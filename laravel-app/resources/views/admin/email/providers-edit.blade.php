@extends('layouts.admin')

@section('title', 'E-mail — '.$company->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Navigation & Feedback -->
    <div class="flex items-center justify-between gap-4 mb-2">
        <a href="{{ route('admin.settings.email.providers') }}" class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-zinc-950 border border-white/5 text-zinc-500 text-[10px] font-black uppercase tracking-widest hover:text-white transition-all group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Voltar aos Provedores
        </a>
        <div class="flex items-center gap-3">
            @if($config->ativo)
                <span class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-xl flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Configuração Ativa
                </span>
            @else
                <span class="px-4 py-2 bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-xl">Inativo</span>
            @endif
        </div>
    </div>

    <!-- Main Config Card -->
    <div class="glass-card p-10 border-emerald-500/10 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>

        <header class="mb-12">
            <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic leading-none">Diretrizes de Conexão <span class="text-emerald-500">SMTP</span></h3>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.3em] mt-3 italic">Identidade de envio para: <span class="text-white">{{ $company->name }}</span></p>
        </header>

        <form action="{{ route('admin.settings.email.providers.update', $company) }}" method="POST" class="space-y-12">
            @csrf
            
            <!-- Grupo 1: Identidade do Provedor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Identificação Interna</label>
                    <input type="text" name="nome_provedor" value="{{ old('nome_provedor', $config->nome_provedor) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                        placeholder="Ex.: Servidor Principal">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Modalidade de Envio</label>
                    <div class="relative">
                        <select name="tipo_envio" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                            <option value="smtp" @selected(old('tipo_envio', $config->tipo_envio) === 'smtp')>SMTP (Standard Protocol)</option>
                            <option value="api" @selected(old('tipo_envio', $config->tipo_envio) === 'api')>API / Gateway (HTTP)</option>
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Preset de Configuração</label>
                    <div class="relative">
                        <select name="preset" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                            @foreach(['custom' => 'Configuração Personalizada', 'gmail' => 'Google Workspace / Gmail', 'outlook' => 'Microsoft 365 / Outlook', 'hostgator' => 'HostGator (CPANEL)', 'sendgrid' => 'SendGrid API', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('preset', $config->preset ?? 'custom') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status do Provedor</label>
                    <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5 flex items-center justify-between h-[60px]">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-600">Ativar Envios desta Unidade</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="ativo" value="1" class="sr-only peer" @checked(old('ativo', $config->ativo ?? true))>
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 peer-checked:after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Grupo 2: Credenciais Técnicas -->
            <div class="pt-10 border-t border-white/5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-3 md:col-span-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Servidor SMTP (Host)</label>
                        <input type="text" name="smtp_host" value="{{ old('smtp_host', $config->smtp_host) }}"
                            class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                            placeholder="smtp.provider.com">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Porta de Conexão</label>
                        <input type="number" name="smtp_porta" value="{{ old('smtp_porta', $config->smtp_porta ?: 587) }}"
                            class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Usuário de Autenticação</label>
                        <input type="text" name="smtp_usuario" value="{{ old('smtp_usuario', $config->smtp_usuario) }}"
                            class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha / API Key</label>
                        <div class="relative group">
                            <input type="password" id="smtp_senha" name="smtp_senha" placeholder="••••••••"
                                class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800 pr-12">
                            <button type="button" onclick="togglePass('smtp_senha')" class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-white transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de Criptografia</label>
                        <div class="relative">
                            <select name="criptografia" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                                <option value="tls" @selected(old('criptografia', $config->criptografia) === 'tls')>TLS / STARTTLS</option>
                                <option value="ssl" @selected(old('criptografia', $config->criptografia) === 'ssl')>SSL</option>
                                <option value="none" @selected(old('criptografia', $config->criptografia) === 'none')>Nenhuma</option>
                            </select>
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grupo 3: Identidade de Remetente -->
            <div class="pt-10 border-t border-white/5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail Remetente (Enviado Por)</label>
                        <input type="email" name="email_remetente" value="{{ old('email_remetente', $config->email_remetente) }}"
                            class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                            placeholder="noreply@academia.com">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome de Exibição (Display Name)</label>
                        <input type="text" name="nome_remetente" value="{{ old('nome_remetente', $config->nome_remetente) }}"
                            class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                            placeholder="NexShape - {{ $company->name }}">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8">
                <button type="submit" class="w-full md:w-auto px-16 py-6 bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-[0.2em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                    Guardar Configurações
                </button>
            </div>
        </form>
    </div>

    <!-- Seção de Teste de Conectividade -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12">
        <div class="lg:col-span-7">
            <div class="glass-card p-8 border-emerald-500/10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-emerald-500">
                        <i data-lucide="zap" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white uppercase tracking-widest leading-none">Validação de Infraestrutura</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-2">Dispare um e-mail real para confirmar os parâmetros</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.email.providers.test') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="empresa_id" value="{{ $company->id }}">
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Endereço de Destino</label>
                        <div class="flex gap-4">
                            <input type="email" name="email_destino" value="{{ auth()->user()->email }}" required
                                class="flex-1 bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            <button type="submit" class="px-8 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl border border-white/5 hover:bg-emerald-500 hover:text-zinc-950 hover:border-emerald-500 transition-all shadow-xl active:scale-95">
                                Testar Agora
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="glass-card p-8 border-rose-500/10 h-full flex flex-col justify-between">
                <div>
                    <h4 class="text-sm font-black text-rose-500 uppercase tracking-widest mb-2 italic leading-tight">Zona Crítica</h4>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                        Desativar o envio impedirá que notificações automáticas, tokens de verificação e e-mails de marketing sejam processados para esta empresa.
                    </p>
                </div>
                
                <div class="pt-8">
                    <form action="{{ route('admin.settings.email.providers.deactivate', $company) }}" method="POST"
                        data-confirm-delete
                        data-confirm-title="Desativar Provedor?"
                        data-confirm-message="Esta ação interromperá todo o fluxo de saída de e-mails para esta unidade. Tem certeza?">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-rose-500/5 border border-rose-500/20 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                            Interromper Envios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePass(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush
@endsection
