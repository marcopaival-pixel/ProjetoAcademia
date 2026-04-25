@extends('layouts.admin')

@section('title', 'Acessar Clínica: ' . $company->name)

@section('content')
<div class="max-w-2xl animate-fade-in">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-white">Acessar Clínica / <span class="text-purple-400">Modo Suporte</span></h1>
        <p class="text-zinc-500 text-sm mt-2">Você está prestes a entrar no ambiente da clínica <strong>{{ $company->name }}</strong>. Por razões de segurança e conformidade (LGPD), este acesso será registrado e auditado.</p>
    </div>

    <form action="{{ route('admin.impersonate-clinic.store', $company) }}" method="POST" class="bg-zinc-900/40 border border-white/5 rounded-2xl p-8 space-y-6">
        @csrf

        <div class="space-y-4">
            <label class="block text-[10px] font-black uppercase text-zinc-400 tracking-wider">Selecione o motivo do acesso</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach([
                    'Suporte técnico',
                    'Implantação',
                    'Treinamento',
                    'Correção de erro',
                    'Auditoria',
                    'Outro'
                ] as $motivo)
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-white/5 bg-zinc-800/50 cursor-pointer hover:border-purple-500/30 transition-all group">
                        <input type="radio" name="motivo_acesso" value="{{ $motivo }}" class="w-4 h-4 text-purple-600 bg-zinc-700 border-zinc-600 focus:ring-purple-500 focus:ring-offset-zinc-900" required>
                        <span class="text-sm text-zinc-300 group-hover:text-white">{{ $motivo }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="space-y-2">
            <label for="descricao" class="block text-[10px] font-black uppercase text-zinc-400 tracking-wider">Descrição detalhada</label>
            <textarea 
                id="descricao" 
                name="descricao" 
                rows="4" 
                class="w-full bg-zinc-800/50 border border-white/5 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-purple-500 transition-colors"
                placeholder="Descreva brevemente o que será realizado neste acesso..."
                required
            ></textarea>
            @error('descricao')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 flex items-center justify-between">
            <a href="{{ route('admin.pdf-companies.index') }}" class="text-[10px] font-black uppercase text-zinc-500 hover:text-white transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-[10px] font-black uppercase shadow-lg shadow-purple-600/20 transition-all transform hover:scale-[1.02]">
                Confirmar e Entrar
            </button>
        </div>
    </form>
</div>
@endsection
