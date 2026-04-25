@extends('layouts.app')

@section('title', 'Solicitar Cupom — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[800px] mx-auto px-6">
    <!-- Header -->
    <div class="space-y-3 pb-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <a href="{{ route('professional.coupons.index') }}" class="text-zinc-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Novo Benefício</span>
        </div>
        <h1 class="text-4xl font-black tracking-tight text-white leading-tight">
            Solicitar <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Cupom</span>
        </h1>
        <p class="text-zinc-500 font-medium">Preencha os dados abaixo. Após a solicitação, o administrador revisará os dados para gerar o código exclusivo.</p>
    </div>

    <!-- Form Glass Card -->
    <div class="bg-zinc-900/60 backdrop-blur-md border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>

        <form action="{{ route('professional.coupons.store') }}" method="POST" class="space-y-8 relative z-10">
            @csrf

            <!-- Nome do Cupom -->
            <div class="space-y-2">
                <label for="name" class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Nome de Referência</label>
                <input type="text" name="name" id="name" required placeholder="Ex: Campanha Verão 2026" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold placeholder:text-zinc-700 focus:outline-none focus:border-blue-500/50 transition-all shadow-inner" value="{{ old('name') }}">
                @error('name') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Seleção de Paciente -->
            <div class="space-y-2">
                <label for="patient_id" class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Paciente Destinatário</label>
                <div class="relative">
                    <select name="patient_id" id="patient_id" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold appearance-none focus:outline-none focus:border-blue-500/50 transition-all shadow-inner">
                        <option value="" disabled selected>Selecione um paciente...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('patient_id') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Tipo de Desconto -->
                <div class="space-y-2">
                    <label for="discount_type" class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Tipo de Desconto</label>
                    <div class="relative">
                        <select name="discount_type" id="discount_type" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold appearance-none focus:outline-none focus:border-blue-500/50 transition-all shadow-inner">
                            <option value="percent">Percentual (%)</option>
                            <option value="fixed">Valor Fixo (R$)</option>
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Valor -->
                <div class="space-y-2">
                    <label for="discount_value" class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Valor do Desconto</label>
                    <input type="number" step="0.01" name="discount_value" id="discount_value" required placeholder="0.00" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold placeholder:text-zinc-700 focus:outline-none focus:border-blue-500/50 transition-all shadow-inner">
                </div>
            </div>

            <!-- Validade -->
            <div class="space-y-2">
                <label for="expiration_date" class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Data de Expiração</label>
                <input type="date" name="expiration_date" id="expiration_date" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold focus:outline-none focus:border-blue-500/50 transition-all shadow-inner" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                <p class="text-[10px] text-zinc-600 font-bold uppercase mt-2 ml-1">RECOMENDADO: MÍNIMO 7 DIAS DE VALIDADE</p>
                @error('expiration_date') <p class="text-red-500 text-[10px] font-black mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl shadow-[0_20px_40px_-10px_rgba(37,99,235,0.4)] hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    ENVIAR SOLICITAÇÃO 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
                <p class="text-center text-[10px] text-zinc-600 font-black uppercase tracking-widest mt-6 italic">A liberação pode levar até 24h úteis</p>
            </div>
        </form>
    </div>
</div>
@endsection
