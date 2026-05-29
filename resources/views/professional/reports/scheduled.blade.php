@extends('layouts.professional')

@section('title', 'Agendamento de Relatórios — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-blue-500 hover:border-blue-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
                 <i class="fas fa-clock text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Relatórios <span class="text-blue-500">Agendados</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Automação • Receba BI diretamente no seu E-mail
                </p>
            </div>
        </div>
    </div>

    <!-- Configuration Form -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[4rem] p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-blue-500/5 blur-[120px] rounded-full"></div>
        
        <form action="#" method="POST" class="relative z-10 space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Select Report -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-2">Tipo de Relatório</label>
                    <select name="report_type" class="w-full h-16 bg-zinc-950 border border-zinc-800 rounded-3xl px-8 text-white font-black text-sm appearance-none focus:border-blue-500/50 transition-all">
                        <option value="complete_analytics">Performance de Alunos (Completo)</option>
                        <option value="detailed_finance">Financeiro Detalhado</option>
                        <option value="management_reports">Gestão & Churn</option>
                    </select>
                </div>

                <!-- Frequency -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-2">Frequência de Envio</label>
                    <div class="flex gap-4">
                        @foreach(['Diário', 'Semanal', 'Mensal'] as $freq)
                        <label class="flex-grow">
                            <input type="radio" name="frequency" value="{{ strtolower($freq) }}" class="hidden peer" {{ $freq == 'Semanal' ? 'checked' : '' }}>
                            <div class="h-16 flex items-center justify-center rounded-3xl border border-zinc-800 bg-zinc-950 text-zinc-600 font-black text-[10px] uppercase tracking-widest peer-checked:border-blue-500 peer-checked:text-white peer-checked:bg-blue-500/10 cursor-pointer transition-all">
                                {{ $freq }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Format -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-2">Formato do Arquivo</label>
                    <div class="flex gap-4">
                        @foreach(['PDF', 'CSV'] as $fmt)
                        <label class="flex-grow">
                            <input type="radio" name="format" value="{{ strtolower($fmt) }}" class="hidden peer" {{ $fmt == 'PDF' ? 'checked' : '' }}>
                            <div class="h-16 flex items-center justify-center rounded-3xl border border-zinc-800 bg-zinc-950 text-zinc-600 font-black text-[10px] uppercase tracking-widest peer-checked:border-blue-500 peer-checked:text-white peer-checked:bg-blue-500/10 cursor-pointer transition-all">
                                {{ $fmt }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-2">Destinatário</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full h-16 bg-zinc-950 border border-zinc-800 rounded-3xl px-8 text-white font-black text-sm focus:border-blue-500/50 transition-all">
                </div>
            </div>

            <div class="pt-8 border-t border-zinc-800 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <i class="fas fa-magic text-sm"></i>
                    </div>
                    <p class="text-xs text-zinc-500 font-medium max-w-sm">
                        Nossa IA irá consolidar os dados e enviar o relatório automaticamente na frequência escolhida.
                    </p>
                </div>
                <button type="button" onclick="alert('Funcionalidade em fase de ativação no servidor de produção.')" class="px-12 py-5 bg-blue-600 text-white text-[11px] font-black rounded-3xl uppercase tracking-[0.2em] hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/30">
                    Salvar Agendamento
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
