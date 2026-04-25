@extends('layouts.admin')

@section('title', 'Configurações Globais')

@section('content')
<div class="space-y-10 animate-fade-in max-w-5xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        
        <!-- General Options -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
            <header class="mb-10">
                <h3 class="text-xl font-black text-white tracking-tight italic">Opções do Sistema</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Parâmetros globais da aplicação</p>
            </header>

            <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da Plataforma</label>
                    <input type="text" name="site_name" value="{{ \App\Models\AdminSetting::get('site_name', 'NexShape') }}" 
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Modo de Operação</label>
                    <select name="maintenance_mode" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                        <option value="0" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '0' ? 'selected' : '' }}>Operação Normal (Público)</option>
                        <option value="1" {{ \App\Models\AdminSetting::get('maintenance_mode', '0') == '1' ? 'selected' : '' }}>Manutenção (Apenas Admins)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Limite de Upload (MB)</label>
                    <input type="number" name="max_upload_size" value="{{ \App\Models\AdminSetting::get('max_upload_size', '10') }}" 
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                    Salvar Alterações
                </button>
            </form>
        </div>

        <!-- Branding & Appearance -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
            <header class="mb-10">
                <h3 class="text-xl font-black text-white tracking-tight italic">Identidade Visual</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Personalização de marca e cores</p>
            </header>

            <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Cor de Destaque (Accent)</label>
                    <div class="flex items-center gap-4 bg-zinc-950 p-4 rounded-2xl border border-white/5">
                        <input type="color" name="accent_color" value="{{ \App\Models\AdminSetting::get('accent_color', '#3d9cf5') }}" 
                            class="w-12 h-10 rounded-lg border-2 border-white/10 bg-zinc-900 cursor-pointer p-0.5">
                        <span class="text-xs font-mono text-zinc-400">{{ \App\Models\AdminSetting::get('accent_color', '#3d9cf5') }}</span>
                    </div>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-2 px-1">Afeta botões, links e gráficos dinâmicos.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Logo URL (Custom)</label>
                    <input type="text" name="logo_url" value="{{ \App\Models\AdminSetting::get('logo_url', '') }}" placeholder="https://..." 
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-2 px-1">Deixe vazio para usar o assets original.</p>
                </div>

                <button type="submit" class="w-full py-5 bg-white text-zinc-900 font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-zinc-200 transition-all shadow-xl shadow-white/5">
                    Aplicar Identidade
                </button>
            </form>
        </div>
    </div>

    <!-- IA & Pricing Strategy -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <header class="mb-10">
            <h3 class="text-xl font-black text-white tracking-tight italic">IA & Estratégia de Precificação</h3>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Variáveis para cálculo dinâmico de custos e planos</p>
        </header>

        <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @csrf
            
            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Custo Mensal IA (R$)</label>
                <input type="number" step="0.01" name="ai_monthly_total_cost" value="{{ \App\Models\AdminSetting::get('ai_monthly_total_cost', '500.00') }}" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Volume de Usos/Mês (Est.)</label>
                <input type="number" name="ai_monthly_total_usage" value="{{ \App\Models\AdminSetting::get('ai_monthly_total_usage', '5000') }}" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Margem de Lucro (%)</label>
                <input type="number" name="ai_profit_margin" value="{{ \App\Models\AdminSetting::get('ai_profit_margin', '300') }}" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="md:col-span-3 pt-6 border-t border-white/5 flex items-center justify-between">
                <p class="text-[10px] text-zinc-500 font-medium italic">
                    @php
                        $cost = (float) \App\Models\AdminSetting::get('ai_monthly_total_cost', 500);
                        $usage = (int) \App\Models\AdminSetting::get('ai_monthly_total_usage', 5000);
                        $costPerUse = $usage > 0 ? $cost / $usage : 0;
                    @endphp
                    Custo estimado por crédito: R$ {{ number_format($costPerUse, 4, ',', '.') }}
                </p>
                <button type="submit" class="w-full md:w-auto px-12 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                    Atualizar Estratégia
                </button>
            </div>
        </form>
    </div>

    <!-- E-mail Configuration (SMTP) -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <header class="mb-10 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-black text-white tracking-tight italic">Configuração de E-mail (SMTP)</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Fallback global quando a empresa não tem provedor próprio. Por empresa: <a href="{{ route('admin.settings.email.providers') }}" class="text-blue-400 hover:underline">Config. E-mail → Provedor</a>.</p>
            </div>
            <form action="{{ route('admin.settings.test') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl border border-white/5 hover:bg-blue-600 transition-all flex items-center gap-2 group">
                    <span class="w-2 h-2 rounded-full bg-blue-500 group-hover:bg-white animate-pulse"></span>
                    Testar Conexão
                </button>
            </form>
        </header>

        <form action="{{ route('admin.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @csrf
            
            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Servidor SMTP (Host)</label>
                <input type="text" name="mail_host" value="{{ \App\Models\AdminSetting::get('mail_host') }}" placeholder="smtp.gmail.com" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Porta</label>
                <input type="text" name="mail_port" value="{{ \App\Models\AdminSetting::get('mail_port', '587') }}" placeholder="587" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Criptografia</label>
                <select name="mail_encryption" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                    <option value="tls" {{ \App\Models\AdminSetting::get('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS (Recomendado)</option>
                    <option value="ssl" {{ \App\Models\AdminSetting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="none" {{ \App\Models\AdminSetting::get('mail_encryption') == 'none' ? 'selected' : '' }}>Nenhuma</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Utilizador (Username)</label>
                <input type="text" name="mail_username" value="{{ \App\Models\AdminSetting::get('mail_username') }}" placeholder="email@dominio.com" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha (Password)</label>
                <input type="password" name="mail_password" placeholder="••••••••••••" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-1 px-1 italic">Deixe em branco para manter a atual.</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail de Envio (From)</label>
                <input type="email" name="mail_from_address" value="{{ \App\Models\AdminSetting::get('mail_from_address') }}" placeholder="noreply@nexshape.com" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome de Exibição</label>
                <input type="text" name="mail_from_name" value="{{ \App\Models\AdminSetting::get('mail_from_name', config('app.name')) }}" 
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
            </div>

            <div class="md:col-span-3 pt-6 border-t border-white/5">
                <button type="submit" class="w-full md:w-auto px-12 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                    Salvar Configurações de E-mail
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-zinc-950/40 border border-red-500/10 p-10 rounded-[3rem]">
        <h3 class="text-lg font-black text-red-500 tracking-tight italic mb-2">Zona de Perigo</h3>
        <p class="text-xs text-zinc-600 font-bold mb-8 uppercase tracking-widest">Ações irreversíveis que afetam o núcleo do sistema</p>
        
        <div class="flex flex-wrap gap-4">
            <button class="px-8 py-4 bg-red-600/10 text-red-500 border border-red-500/20 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-600 hover:text-white transition-all">
                Limpar Todos os Caches
            </button>
            <button class="px-8 py-4 bg-zinc-900 text-zinc-600 border border-white/5 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:text-white transition-all">
                Reiniciar Serviços
            </button>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
