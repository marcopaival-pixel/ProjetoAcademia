@extends('layouts.admin')

@section('title', isset($integration) ? 'Editar API: ' . $integration->name : 'Nova Integração API')

@section('content')
<div class="max-w-4xl animate-fade-in">
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">
        <header class="mb-10 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-white tracking-tight italic">{{ isset($integration) ? 'Atualizar Configurações' : 'Configurar Nova Fonte de Dados' }}</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Defina os parâmetros de conexão e autenticação</p>
            </div>
            @if(isset($integration))
                <form action="{{ route('admin.api-integrations.test', $integration) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600/10 text-blue-500 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2 group">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 group-hover:bg-white animate-pulse"></span>
                        Testar Conexão
                    </button>
                </form>
            @endif
        </header>

        <form action="{{ isset($integration) ? route('admin.api-integrations.update', $integration) : route('admin.api-integrations.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @csrf
            
            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da API / Provedor</label>
                <input type="text" name="name" value="{{ old('name', $integration->name ?? '') }}" placeholder="Ex: ExerciseDB, OpenFoodFacts..." required
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all font-bold">
                @error('name') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de Integração</label>
                <div class="relative">
                    <select name="type" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                        <option value="">Selecione um tipo...</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $integration->type ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                @error('type') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status</label>
                <div class="relative">
                    <select name="status" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                        <option value="active" {{ old('status', $integration->status ?? 'active') == 'active' ? 'selected' : '' }}>ATIVA</option>
                        <option value="inactive" {{ old('status', $integration->status ?? '') == 'inactive' ? 'selected' : '' }}>INATIVA</option>
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                @error('status') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">URL Base do Endpoint</label>
                <input type="url" name="base_url" value="{{ old('base_url', $integration->base_url ?? '') }}" placeholder="https://api.exemplo.com/v1" required
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all font-mono">
                @error('base_url') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">API Key / Token</label>
                <div class="relative">
                    <input type="password" name="api_key" value="{{ old('api_key', isset($integration) ? '********' : '') }}" placeholder="api_key_..."
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all pr-12">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 cursor-pointer hover:text-zinc-400" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
                @error('api_key') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Secret Key / ID</label>
                <div class="relative">
                    <input type="password" name="secret_key" value="{{ old('secret_key', isset($integration) ? '********' : '') }}" placeholder="secret_..."
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all pr-12">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 cursor-pointer hover:text-zinc-400" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
                @error('secret_key') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Timeout (Segundos)</label>
                <input type="number" name="timeout" value="{{ old('timeout', $integration->timeout ?? '30') }}" min="1" max="120" required
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                @error('timeout') <p class="text-red-500 text-[10px] uppercase font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 pt-6 border-t border-white/5 flex gap-4">
                <button type="submit" class="flex-1 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                    {{ isset($integration) ? 'Salvar Configurações' : 'Finalizar Cadastro' }}
                </button>
                <a href="{{ route('admin.api-integrations.index') }}" class="px-10 py-5 bg-zinc-800 text-zinc-400 font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-zinc-700 hover:text-white transition-all">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(el) {
        const input = el.parentElement.querySelector('input');
        const icon = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
