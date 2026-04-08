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
