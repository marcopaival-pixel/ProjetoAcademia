@extends('layouts.admin')

@section('title', 'Configurações Globais')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Header Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight italic uppercase">Centro de Controle <span class="text-emerald-500">NexShape</span></h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie as diretrizes, segurança e comunicações da plataforma</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Sistema Operacional</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Sidebar de Navegação Rápida (Anchor Links) -->
        <div class="lg:col-span-3 space-y-2">
            <nav class="sticky top-24 space-y-1">
                <a href="#security" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-zinc-900/50 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800/50 transition-all group active">
                    <i data-lucide="shield-check" class="w-4 h-4 group-hover:text-emerald-500 transition-colors"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Segurança</span>
                </a>
                <a href="#system" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-zinc-900/50 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800/50 transition-all group">
                    <i data-lucide="cpu" class="w-4 h-4 group-hover:text-emerald-500 transition-colors"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Sistema</span>
                </a>
                <a href="#branding" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-zinc-900/50 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800/50 transition-all group">
                    <i data-lucide="palette" class="w-4 h-4 group-hover:text-emerald-500 transition-colors"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Identidade</span>
                </a>
                <a href="#email-settings" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/20 text-emerald-500 shadow-lg shadow-emerald-500/5 group">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest font-extrabold">E-mail (SMTP)</span>
                </a>
                <a href="#strategy" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-zinc-900/50 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800/50 transition-all group">
                    <i data-lucide="trending-up" class="w-4 h-4 group-hover:text-emerald-500 transition-colors"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">IA & Estratégia</span>
                </a>
                <a href="#finance" class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-zinc-900/50 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800/50 transition-all group">
                    <i data-lucide="wallet" class="w-4 h-4 group-hover:text-emerald-500 transition-colors"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Financeiro</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-9 space-y-12">
            
            <!-- SEGURANÇA -->
            <section id="security" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10 border-emerald-500/10 shadow-emerald-500/5">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shadow-inner">
                            <i data-lucide="shield" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">Segurança & Acesso</h3>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Políticas de validação e autenticação</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-zinc-950/50 rounded-3xl border border-white/5 hover:border-emerald-500/20 transition-all flex items-center justify-between group">
                                <div class="max-w-[70%]">
                                    <h4 class="text-xs font-black text-white uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Verificação de E-mail</h4>
                                    <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold tracking-widest leading-relaxed">Obrigatório para novos cadastros</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="verificacao_email_ativa" value="false">
                                    <input type="checkbox" name="verificacao_email_ativa" value="true" class="sr-only peer" {{ \App\Models\SystemSetting::isTrue('verificacao_email_ativa', true) ? 'checked' : '' }}>
                                    <div class="w-12 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 peer-checked:after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>

                            <div class="p-6 bg-zinc-950/50 rounded-3xl border border-white/5 opacity-40 cursor-not-allowed">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-black text-white uppercase tracking-widest">Autenticação 2FA</h4>
                                        <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold tracking-widest">Segunda camada de proteção</p>
                                    </div>
                                    <span class="px-3 py-1 bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-widest rounded-lg">Em breve</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-8 py-4 bg-emerald-500 text-zinc-950 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                                Salvar Políticas
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- SISTEMA -->
            <section id="system" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-400 border border-white/5">
                            <i data-lucide="settings-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">Parâmetros do Sistema</h3>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Configurações globais da plataforma</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da Plataforma</label>
                            <input type="text" name="site_name" value="{{ \App\Models\AdminSetting::get('site_name', 'NexShape') }}" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Modo de Operação</label>
                            <div class="relative">
                                <select name="maintenance_mode" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                                    <option value="0" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '0' ? 'selected' : '' }}>Operação Normal (Público)</option>
                                    <option value="1" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '1' ? 'selected' : '' }}>Manutenção (Apenas Admins)</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Limite de Upload (MB)</label>
                            <div class="flex items-center bg-zinc-950 border border-white/5 rounded-2xl px-4">
                                <input type="number" name="max_upload_size" value="{{ \App\Models\AdminSetting::get('max_upload_size', '10') }}" 
                                    class="flex-1 bg-transparent py-4 text-white text-sm outline-none">
                                <span class="text-[10px] font-black text-zinc-600 uppercase">Megabytes</span>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex justify-end pt-4 border-t border-white/5">
                            <button type="submit" class="px-10 py-4 bg-white text-zinc-950 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-200 transition-all active:scale-95">
                                Atualizar Sistema
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- IDENTIDADE -->
            <section id="branding" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white border border-white/10">
                            <i data-lucide="swatchbook" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">Identidade & Branding</h3>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Personalização visual e marca</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @csrf
                        <div class="space-y-4">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Esquema de Cores (Accent)</label>
                            <div class="flex items-center gap-4 bg-zinc-950 p-6 rounded-3xl border border-white/5">
                                <div class="relative w-16 h-16 rounded-2xl overflow-hidden border border-white/10 group cursor-pointer shadow-2xl">
                                    <input type="color" name="accent_color" value="{{ \App\Models\AdminSetting::get('accent_color', '#10b981') }}" 
                                        class="absolute inset-[-10px] w-[200%] h-[200%] cursor-pointer border-none bg-transparent" id="accent-input">
                                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i data-lucide="pipette" class="w-4 h-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-lg font-black text-white tracking-tighter uppercase leading-none" id="accent-code">{{ \App\Models\AdminSetting::get('accent_color', '#10b981') }}</span>
                                    <span class="text-[8px] text-zinc-600 font-black uppercase tracking-widest mt-1">Hexadecimal</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">URL da Logomarca</label>
                            <input type="text" name="logo_url" value="{{ \App\Models\AdminSetting::get('logo_url', '') }}" placeholder="https://seu-cdn.com/logo.png" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-2 px-1">Se vazio, utiliza a logo padrão do sistema.</p>
                        </div>

                        <div class="md:col-span-2 flex justify-end pt-4 border-t border-white/5">
                            <button type="submit" class="px-10 py-4 bg-zinc-900 text-white border border-white/10 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-800 transition-all active:scale-95">
                                Aplicar Branding
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- E-MAIL (SMTP) - DESTAQUE -->
            <section id="email-settings" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10 border-emerald-500/20 ring-1 ring-emerald-500/10 shadow-2xl shadow-emerald-500/5 relative overflow-hidden">
                    <!-- Background Glow -->
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-3xl bg-emerald-500 flex items-center justify-center text-zinc-950 shadow-lg shadow-emerald-500/20 rotate-3 group-hover:rotate-0 transition-transform">
                                    <i data-lucide="send" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white tracking-tight uppercase italic">Infraestrutura de E-mail</h3>
                                    <p class="text-[10px] text-emerald-500/80 font-black uppercase tracking-widest mt-1">Configuração SMTP Global & Testes</p>
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.settings.test') }}" method="POST">
                                @csrf
                                <button type="submit" class="group flex items-center gap-3 px-6 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-xl active:scale-95">
                                    <i data-lucide="zap" class="w-4 h-4 animate-pulse group-hover:animate-none"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Testar Conexão</span>
                                </button>
                            </form>
                        </header>

                        <div class="bg-zinc-950/40 p-6 rounded-[2rem] border border-white/5 mb-10">
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                                <i data-lucide="info" class="w-3 h-3 inline-block mr-1 -mt-0.5 text-emerald-500"></i>
                                Estas configurações servem como <span class="text-white">fallback global</span>. 
                                Para configurar provedores específicos por clínica, utilize o módulo: 
                                <a href="{{ route('admin.settings.email.providers') }}" class="text-emerald-500 hover:underline inline-flex items-center gap-1 ml-1">
                                    <span>E-mail &rarr; Provedores</span>
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            </p>
                        </div>

                        <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-10">
                            @csrf
                            
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Host SMTP</label>
                                <input type="text" name="mail_host" value="{{ \App\Models\AdminSetting::get('mail_host') }}" placeholder="smtp.provider.com" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Porta</label>
                                <input type="text" name="mail_port" value="{{ \App\Models\AdminSetting::get('mail_port', '587') }}" placeholder="587" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Criptografia</label>
                                <div class="relative">
                                    <select name="mail_encryption" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                                        <option value="tls" {{ \App\Models\AdminSetting::get('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS (Seguro)</option>
                                        <option value="ssl" {{ \App\Models\AdminSetting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ \App\Models\AdminSetting::get('mail_encryption') == 'none' ? 'selected' : '' }}>Nenhuma</option>
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Utilizador</label>
                                <input type="text" name="mail_username" value="{{ \App\Models\AdminSetting::get('mail_username') }}" placeholder="autenticacao@dominio.com" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha de Aplicativo</label>
                                <div class="relative">
                                    <input type="password" name="mail_password" placeholder="••••••••••••" 
                                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all pr-12" id="mail-pass">
                                    <button type="button" onclick="togglePass('mail-pass')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-white transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail Remetente (From)</label>
                                <input type="email" name="mail_from_address" value="{{ \App\Models\AdminSetting::get('mail_from_address') }}" placeholder="no-reply@nexshape.com" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome de Exibição</label>
                                <input type="text" name="mail_from_name" value="{{ \App\Models\AdminSetting::get('mail_from_name', config('app.name')) }}" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                            </div>

                            <div class="md:col-span-3 flex justify-end pt-6 border-t border-white/5">
                                <button type="submit" class="px-12 py-5 bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-[0.2em] rounded-3xl hover:bg-emerald-400 transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                                    Salvar Configurações de E-mail
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- ESTRATÉGIA IA -->
            <section id="strategy" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10 border-blue-500/10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 shadow-lg shadow-blue-500/5">
                            <i data-lucide="brain-circuit" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">Estratégia de Inteligência Artificial</h3>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Gestão de custos e lucratividade operacional</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Custo Mensal Global (R$)</label>
                            <input type="number" step="0.01" name="ai_monthly_total_cost" value="{{ \App\Models\AdminSetting::get('ai_monthly_total_cost', '500.00') }}" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Volume de Requisições (Mês)</label>
                            <input type="number" name="ai_monthly_total_usage" value="{{ \App\Models\AdminSetting::get('ai_monthly_total_usage', '5000') }}" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Markup de Lucro (%)</label>
                            <input type="number" name="ai_profit_margin" value="{{ \App\Models\AdminSetting::get('ai_profit_margin', '300') }}" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        </div>

                        <div class="md:col-span-3 pt-8 mt-4 border-t border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/5 border border-blue-500/10 flex items-center justify-center text-blue-500/50">
                                    <i data-lucide="calculator" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    @php
                                        $cost = (float) \App\Models\AdminSetting::get('ai_monthly_total_cost', 500);
                                        $usage = (int) \App\Models\AdminSetting::get('ai_monthly_total_usage', 5000);
                                        $costPerUse = $usage > 0 ? $cost / $usage : 0;
                                    @endphp
                                    <p class="text-[11px] font-black text-white uppercase tracking-widest">Preço Base: R$ {{ number_format($costPerUse, 4, ',', '.') }}</p>
                                    <p class="text-[8px] text-zinc-600 font-black uppercase tracking-[0.2em] mt-1">Custo unitário estimado por crédito consumido</p>
                                </div>
                            </div>
                            <button type="submit" class="px-10 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-3xl hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 active:scale-95">
                                Atualizar Matriz Financeira
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- FINANCEIRO -->
            <section id="finance" class="scroll-mt-24">
                <div class="glass-card p-8 md:p-10 border-emerald-500/10 shadow-emerald-500/5">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 shadow-inner">
                            <i data-lucide="badge-dollar-sign" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase italic">Faturamento & Cobrança</h3>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Gestão global de monetização</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="p-8 bg-zinc-950/50 rounded-[2.5rem] border border-white/5 hover:border-emerald-500/20 transition-all flex items-center justify-between group">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 group-hover:text-emerald-500 transition-colors shadow-inner">
                                    <i data-lucide="power" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-white uppercase tracking-widest">Status da Cobrança Global</h4>
                                    <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold tracking-widest leading-relaxed">Se desativado, todos os planos terão ativação gratuita imediata</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="pagamento_ativo" value="false">
                                <input type="checkbox" name="pagamento_ativo" value="true" class="sr-only peer" {{ \App\Models\AdminSetting::get('pagamento_ativo', true) ? 'checked' : '' }}>
                                <div class="w-16 h-8 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-zinc-400 peer-checked:after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-10 py-4 bg-emerald-500 text-zinc-950 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                                Atualizar Faturamento
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- ZONA DE PERIGO -->
            <section id="danger" class="pt-10">
                <div class="p-8 md:p-10 bg-rose-500/5 border border-rose-500/10 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-3xl bg-rose-500/10 flex items-center justify-center text-rose-500 border border-rose-500/20">
                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-rose-500 tracking-tight uppercase italic leading-tight">Procedimentos de <br>Manutenção Crítica</h3>
                            <p class="text-[10px] text-rose-500/50 font-bold uppercase tracking-widest mt-2">Ações irreversíveis que afetam o núcleo</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <button class="px-8 py-4 bg-rose-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-400 transition-all shadow-xl shadow-rose-500/10 active:scale-95">
                            Limpar Caches do Sistema
                        </button>
                        <button class="px-8 py-4 bg-zinc-900 text-rose-500 border border-rose-500/20 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-500/10 transition-all">
                            Reiniciar Workers
                        </button>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

@push('scripts')
<script>
    const accentInput = document.getElementById('accent-input');
    const accentCode = document.getElementById('accent-code');
    
    if(accentInput) {
        accentInput.addEventListener('input', (e) => {
            accentCode.textContent = e.target.value.toUpperCase();
        });
    }

    function togglePass(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Scroll reveal highlight sidebar
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('nav a');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 120) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active', 'bg-emerald-500/5', 'text-emerald-500', 'border-emerald-500/20');
            link.classList.add('bg-zinc-900/50', 'text-zinc-400', 'border-white/5');
            
            if (link.getAttribute('href').includes(current)) {
                link.classList.add('active', 'bg-emerald-500/5', 'text-emerald-500', 'border-emerald-500/20');
                link.classList.remove('bg-zinc-900/50', 'text-zinc-400', 'border-white/5');
            }
        });
    });
</script>
@endpush

<style>
    .glass-card {
        background: rgba(10, 10, 10, 0.4);
        backdrop-filter: blur(40px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 3rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glass-card:hover {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(10, 10, 10, 0.5);
    }
    input, select {
        transition: all 0.2s ease;
    }
    input:focus, select:focus {
        background-color: #000 !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
    }
</style>
@endsection
