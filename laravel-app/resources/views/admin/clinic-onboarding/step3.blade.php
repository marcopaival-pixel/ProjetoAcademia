@extends('layouts.clinic-onboarding')

@section('title', 'Configuração Inicial')

@section('content')
<form action="{{ route('admin.clinic-onboarding.step.save', [$company, 3]) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="space-y-8">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-palette mr-3 text-blue-500"></i> Identidade Visual
            </h3>
            
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-zinc-400">Cor Primária</label>
                <div class="flex items-center space-x-4">
                    <input type="color" name="primary_color" value="{{ old('primary_color', $company->primary_color ?? '#3b82f6') }}"
                        class="h-14 w-24 bg-transparent border-none cursor-pointer">
                    <input type="text" value="{{ old('primary_color', $company->primary_color ?? '#3b82f6') }}" readonly
                        class="flex-grow bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-zinc-500 text-sm">
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-semibold text-zinc-400">Cor de Destaque (Accent)</label>
                <div class="flex items-center space-x-4">
                    <input type="color" name="accent_color" value="{{ old('accent_color', $company->accent_color ?? '#10b981') }}"
                        class="h-14 w-24 bg-transparent border-none cursor-pointer">
                    <input type="text" value="{{ old('accent_color', $company->accent_color ?? '#10b981') }}" readonly
                        class="flex-grow bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-zinc-500 text-sm">
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-image mr-3 text-emerald-500"></i> Logotipo
            </h3>

            <div class="relative group">
                <div class="w-full h-48 bg-white/5 border-2 border-dashed border-white/10 rounded-3xl flex flex-col items-center justify-center transition-all group-hover:border-blue-500/50">
                    @if($company->logo_path)
                        <img src="{{ Storage::url($company->logo_path) }}" class="h-24 object-contain mb-4">
                        <span class="text-xs text-zinc-500">Clique para alterar</span>
                    @else
                        <i class="fas fa-cloud-upload-alt text-4xl text-zinc-600 mb-4 group-hover:text-blue-500 transition-colors"></i>
                        <span class="text-sm font-medium text-zinc-500">Upload do Logo (PNG/JPG)</span>
                    @endif
                    <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
            </div>
            <p class="text-[10px] text-zinc-500 uppercase tracking-widest text-center">Recomendado: 512x512px com fundo transparente</p>
        </div>
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-between">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 2]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Salvar e Continuar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>
</form>
@endsection
