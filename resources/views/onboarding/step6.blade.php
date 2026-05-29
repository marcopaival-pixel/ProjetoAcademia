@extends('layouts.onboarding-premium')

@section('title', 'Configurações')
@section('step_title', 'Identidade e Preferências')
@section('step_description', 'Personalize a aparência do seu sistema e como seus documentos serão emitidos.')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 6) }}" method="POST" enctype="multipart/form-data" class="space-y-12" x-data="{
    primaryColor: '#3b82f6',
    wmText: 'NEXSHAPE',
    wmOpacity: 0.1,
    wmRotate: -32,
    wmScale: 1.0,
    wmPosition: 'center'
}">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="space-y-8">
            <!-- Logo Upload -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Logotipo da Empresa</label>
                <div class="relative group cursor-pointer h-32 w-full glass rounded-[24px] border-dashed border-2 border-white/10 hover:border-blue-500/50 transition-all flex items-center justify-center">
                    <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt text-2xl text-zinc-600 mb-2 group-hover:text-blue-500 transition-colors"></i>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Arraste ou clique para enviar</p>
                    </div>
                </div>
            </div>

            <!-- Cor Principal -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Cor do Sistema</label>
                <div class="flex items-center gap-4">
                    <input type="color" name="primary_color" x-model="primaryColor" class="w-16 h-16 rounded-2xl bg-white/5 border-0 cursor-pointer">
                    <div class="flex-grow">
                        <input type="text" x-model="primaryColor" class="w-full input-premium font-mono uppercase">
                    </div>
                </div>
            </div>

            <!-- Regionalização -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Idioma</label>
                    <select name="language" class="w-full input-premium bg-zinc-900">
                        <option value="pt-BR">Português (BR)</option>
                        <option value="en">English (US)</option>
                        <option value="es">Español</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Moeda</label>
                    <select name="currency" class="w-full input-premium bg-zinc-900">
                        <option value="BRL">Real (R$)</option>
                        <option value="USD">Dólar ($)</option>
                        <option value="EUR">Euro (€)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Marca d'água Preview -->
        <div class="space-y-6">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1 text-center">Preview Marca d'água PDF</label>
            
            <div class="relative h-[300px] w-full bg-white rounded-[32px] shadow-inner overflow-hidden flex items-center justify-center p-8">
                <!-- Simulação de Documento -->
                <div class="absolute inset-0 p-8 space-y-4 opacity-[0.05]">
                    <div class="h-4 w-2/3 bg-black rounded"></div>
                    <div class="h-4 w-full bg-black rounded"></div>
                    <div class="h-4 w-full bg-black rounded"></div>
                    <div class="h-4 w-3/4 bg-black rounded"></div>
                    <div class="h-4 w-full bg-black rounded"></div>
                </div>

                <!-- Marca d'água Real -->
                <div class="pointer-events-none select-none font-black text-black break-words max-w-full text-center"
                     :style="`opacity: ${wmOpacity}; transform: rotate(${wmRotate}deg) scale(${wmScale});`"
                     x-text="wmText">
                </div>
            </div>

            <div class="glass p-6 rounded-[24px] space-y-4">
                <div class="space-y-2">
                    <label class="flex justify-between text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                        Texto <span>Opacidade: <span x-text="Math.round(wmOpacity * 100) + '%'"></span></span>
                    </label>
                    <input type="text" name="pdf_settings[watermark][text]" x-model="wmText" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white text-xs">
                    <input type="range" name="pdf_settings[watermark][opacity]" x-model="wmOpacity" min="0.02" max="0.5" step="0.01" class="w-full h-1 bg-white/10 rounded-full appearance-none accent-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Rotação</label>
                        <input type="range" name="pdf_settings[watermark][rotate]" x-model="wmRotate" min="-90" max="90" step="1" class="w-full h-1 bg-white/10 rounded-full appearance-none accent-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Escala</label>
                        <input type="range" name="pdf_settings[watermark][scale]" x-model="wmScale" min="0.5" max="3" step="0.1" class="w-full h-1 bg-white/10 rounded-full appearance-none accent-blue-500">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 5) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3">
            Continuar para Plano <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
