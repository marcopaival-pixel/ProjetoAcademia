@extends('layouts.app')

@section('title', 'Branding Studio — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">White-Label Engine</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Deep Branding v2.5</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Branding <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-blue-400">Studio</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Sua marca, sua plataforma. Personalize a experiência visual dos seus pacientes em todos os touchpoints.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <a href="{{ route('professional.dashboard') }}" class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold rounded-xl hover:bg-zinc-700 transition-all border border-white/5 flex items-center gap-2">
                    Voltar ao Painel
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Configuração (Lado Esquerdo) - Glass Bento Card -->
        <div class="lg:col-span-12 xl:col-span-4 space-y-8">
            <form action="{{ route('professional.branding.update') }}" method="POST" enctype="multipart/form-data" class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-emerald-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                @csrf
                
                <div class="space-y-10 relative z-10">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Identidade Nominal</label>
                        <input type="text" name="clinic_name" value="{{ $branding['clinic_name'] }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-[2rem] p-6 text-white text-sm font-bold focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all placeholder:text-zinc-700 shadow-inner" placeholder="Ex: Clínica BioFit">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Cor Primária</label>
                            <div class="flex items-center gap-4 bg-zinc-950/50 border border-white/5 rounded-[2rem] p-3 pr-6 transition-all focus-within:ring-2 focus-within:ring-emerald-500/50 shadow-inner">
                                <input type="color" id="primary_color" name="primary_color" value="{{ $branding['primary_color'] }}" class="w-12 h-12 bg-transparent border-none rounded-2xl cursor-pointer" oninput="updatePreview()">
                                <span id="primary_hex" class="text-zinc-500 text-[10px] font-black uppercase tracking-widest font-mono">{{ $branding['primary_color'] }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Cor de Destaque</label>
                            <div class="flex items-center gap-4 bg-zinc-950/50 border border-white/5 rounded-[2rem] p-3 pr-6 transition-all focus-within:ring-2 focus-within:ring-emerald-500/50 shadow-inner">
                                <input type="color" id="accent_color" name="accent_color" value="{{ $branding['accent_color'] }}" class="w-12 h-12 bg-transparent border-none rounded-2xl cursor-pointer" oninput="updatePreview()">
                                <span id="accent_hex" class="text-zinc-500 text-[10px] font-black uppercase tracking-widest font-mono">{{ $branding['accent_color'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Símbolo Visual (SVG/PNG)</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-white/10 border-dashed rounded-[2rem] cursor-pointer bg-zinc-950/30 hover:bg-emerald-500/5 hover:border-emerald-500/30 transition-all group/upload">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-4 text-zinc-600 group-hover/upload:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Fazer Upload do Logo</p>
                                    <p class="text-[9px] text-zinc-600 font-black mt-2">TRANSPARENTE (MÁX. 2MB)</p>
                                </div>
                                <input type="file" name="logo" class="hidden" />
                            </label>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-emerald-400 hover:text-white transition-all shadow-2xl active:scale-[0.98] uppercase text-xs tracking-[0.2em]">
                            Consolidar Marca
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Previews (Lado Direito) -->
        <div class="lg:col-span-12 xl:col-span-8 flex flex-col gap-10">
            <div class="flex items-center justify-between px-6">
                <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">Real-Time Preview Simulation</h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Preview Cartão do App do Paciente -->
                <div class="space-y-6">
                    <p class="text-center text-[10px] text-zinc-600 font-black uppercase tracking-widest">Interface Mobile do Paciente</p>
                    <div class="bg-black rounded-[4rem] border-[12px] border-zinc-900 p-8 aspect-[9/16] relative overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.8)] mx-auto w-full max-w-[320px]">
                        <div class="space-y-8">
                            <div class="flex items-center justify-between">
                                <div id="preview-logo-placeholder" class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-[8px] font-black text-zinc-600 tracking-tighter shadow-inner">LOGO</div>
                                <div class="w-6 h-6 rounded-full bg-zinc-800 border border-white/5 shadow-xl"></div>
                            </div>
                            <div class="h-40 rounded-[2.5rem] p-6 flex flex-col justify-end shadow-2xl group/app transition-all" id="preview-hero" style="background: {{ $branding['primary_color'] }}">
                                <p class="text-white text-[9px] font-black uppercase tracking-widest opacity-60 mb-2 truncate" id="preview-clinic-name">{{ $branding['clinic_name'] }}</p>
                                <h4 class="text-white text-xl font-black leading-tight tracking-tighter">Seu treino<br>está pronto!</h4>
                            </div>
                            <div class="space-y-4">
                                <div class="h-16 bg-zinc-900/50 rounded-[1.5rem] flex items-center px-5 justify-between border border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full shadow-lg" id="preview-dot" style="background: {{ $branding['accent_color'] }}; box-shadow: 0 0 10px {{ $branding['accent_color'] }}80"></div>
                                        <div class="w-24 h-2.5 bg-zinc-800 rounded-full"></div>
                                    </div>
                                    <div class="w-10 h-10 rounded-2xl bg-zinc-800 border border-white/5"></div>
                                </div>
                                <div class="h-16 bg-zinc-900/50 rounded-[1.5rem] flex items-center px-5 justify-between border border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full shadow-lg" id="preview-dot2" style="background: {{ $branding['accent_color'] }}; box-shadow: 0 0 10px {{ $branding['accent_color'] }}80"></div>
                                        <div class="w-24 h-2.5 bg-zinc-800 rounded-full"></div>
                                    </div>
                                    <div class="w-10 h-10 rounded-2xl bg-zinc-800 border border-white/5"></div>
                                </div>
                            </div>
                            <!-- Botão Flutuante -->
                            <div class="absolute bottom-12 left-1/2 -translate-x-1/2 w-16 h-16 rounded-full flex items-center justify-center text-white shadow-2xl" id="preview-fab" style="background: {{ $branding['primary_color'] }}">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview PDF Export -->
                <div class="space-y-6">
                    <p class="text-center text-[10px] text-zinc-600 font-black uppercase tracking-widest">Documento Técnico (PDF High-Res)</p>
                    <div class="bg-white rounded-[2rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] p-10 aspect-[1/1.41] mx-auto w-full max-w-[320px] text-zinc-800 flex flex-col group/pdf">
                        <div class="border-b-[3px] pb-6 mb-8 flex items-center justify-between transition-all" id="preview-pdf-border" style="border-color: {{ $branding['primary_color'] }}">
                            <div class="flex flex-col">
                                <h4 class="text-sm font-black uppercase tracking-tighter" id="preview-pdf-clinic" style="color: {{ $branding['primary_color'] }}">{{ $branding['clinic_name'] }}</h4>
                                <p class="text-[8px] text-zinc-400 font-bold uppercase tracking-widest">Professional Prescription Protocol</p>
                            </div>
                            <div class="w-8 h-8 bg-zinc-100 rounded-xl shadow-inner"></div>
                        </div>
                        <div class="flex-1 space-y-5">
                            <div class="h-3 bg-zinc-200 w-1/2 rounded-full"></div>
                            <div class="h-2 bg-zinc-100 w-full rounded-full"></div>
                            <div class="h-2 bg-zinc-100 w-full rounded-full"></div>
                            <div class="h-2 bg-zinc-100 w-3/4 rounded-full"></div>
                            <div class="pt-8">
                                <div class="h-1.5 w-12 rounded-full mb-3" style="background: {{ $branding['accent_color'] }}"></div>
                                <div class="h-20 bg-zinc-50 rounded-[1.5rem] italic flex items-start p-4 text-[10px] text-zinc-400 font-medium border border-zinc-100 uppercase tracking-tighter leading-relaxed">
                                    Observações dinâmicas e orientações específicas do profissional serão renderizadas neste container...
                                </div>
                            </div>
                        </div>
                        <div class="mt-auto pt-6 border-t border-zinc-50 flex justify-center">
                            <div class="w-24 h-6 rounded-full bg-zinc-100 opacity-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePreview() {
        const primary = document.getElementById('primary_color').value;
        const accent = document.getElementById('accent_color').value;
        const clinicNameInput = document.querySelector('input[name="clinic_name"]');
        const clinicName = clinicNameInput ? clinicNameInput.value : '';

        const primaryHex = document.getElementById('primary_hex');
        const accentHex = document.getElementById('accent_hex');
        if(primaryHex) primaryHex.textContent = primary;
        if(accentHex) accentHex.textContent = accent;

        const hero = document.getElementById('preview-hero');
        const fab = document.getElementById('preview-fab');
        const dot = document.getElementById('preview-dot');
        const dot2 = document.getElementById('preview-dot2');
        const clinicLabel = document.getElementById('preview-clinic-name');

        if(hero) hero.style.background = primary;
        if(fab) fab.style.background = primary;
        if(dot) {
            dot.style.background = accent;
            dot.style.boxShadow = `0 0 10px ${accent}80`;
        }
        if(dot2) {
            dot2.style.background = accent;
            dot2.style.boxShadow = `0 0 10px ${accent}80`;
        }
        if(clinicLabel) clinicLabel.textContent = clinicName;

        const pdfBorder = document.getElementById('preview-pdf-border');
        const pdfClinic = document.getElementById('preview-pdf-clinic');
        
        if(pdfBorder) pdfBorder.style.borderColor = primary;
        if(pdfClinic) {
            pdfClinic.style.color = primary;
            pdfClinic.textContent = clinicName;
        }
    }

    const nameInput = document.querySelector('input[name="clinic_name"]');
    if(nameInput) {
        nameInput.addEventListener('input', updatePreview);
    }
</script>

<style>
    input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    input[type="color"]::-webkit-color-swatch { border: none; border-radius: 12px; }
</style>
@endsection
