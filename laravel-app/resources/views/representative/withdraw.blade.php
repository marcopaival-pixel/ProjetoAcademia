@extends('layouts.app')

@section('title', 'Solicitar Resgate')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 animate-fade-in">
    <div>
        <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">
            Solicitar <span class="text-emerald-500">Resgate</span>
        </h1>
        <p class="text-zinc-500 font-medium mt-1">Converta suas comissões disponíveis em dinheiro na sua conta via PIX.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Card de Saldo --}}
        <div class="bg-emerald-500 p-10 rounded-[3rem] relative overflow-hidden shadow-2xl shadow-emerald-500/10">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            
            <p class="text-[11px] font-black text-zinc-950/50 uppercase tracking-[0.2em] mb-4">Saldo Líquido Disponível</p>
            <h2 class="text-5xl font-black text-zinc-950 tracking-tighter">R$ {{ number_format($netAvailable, 2, ',', '.') }}</h2>
            
            <div class="mt-8 space-y-2">
                <div class="flex items-center justify-between text-[10px] font-black text-zinc-950/40 uppercase tracking-widest">
                    <span>Total em Carteira:</span>
                    <span>R$ {{ number_format($availableBalance, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[10px] font-black text-zinc-950/40 uppercase tracking-widest">
                    <span>Solicitações Pendentes:</span>
                    <span>- R$ {{ number_format($pendingWithdrawals, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Formulário --}}
        <div class="bg-zinc-900/50 border border-zinc-800 p-10 rounded-[3rem]">
            <form action="{{ route('representative.withdraw.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest px-4">Valor do Resgate (Mín. R$ 20,00)</label>
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-zinc-600 font-black">R$</span>
                        <input type="number" step="0.01" name="amount" required max="{{ $netAvailable }}" min="20"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-3xl py-6 pl-14 pr-6 text-white font-black focus:border-emerald-500 outline-none transition-all"
                            placeholder="0,00">
                    </div>
                    @error('amount') <p class="text-rose-500 text-[10px] font-bold px-4 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest px-4">Chave PIX para Recebimento</label>
                    <input type="text" name="pix_key" required
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-3xl py-6 px-6 text-white font-black focus:border-emerald-500 outline-none transition-all"
                        placeholder="CPF, E-mail, Celular ou Aleatória">
                    @error('pix_key') <p class="text-rose-500 text-[10px] font-bold px-4 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" @if($netAvailable < 20) disabled @endif
                        class="w-full py-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 disabled:opacity-30 disabled:cursor-not-allowed transition-all shadow-xl shadow-emerald-500/10 uppercase tracking-[0.2em] text-xs">
                        CONFIRMAR SOLICITAÇÃO
                    </button>
                    @if($netAvailable < 20)
                        <p class="text-zinc-600 text-[9px] font-bold text-center mt-4 uppercase tracking-widest italic">Atingir saldo mínimo de R$ 20,00 para habilitar.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="bg-zinc-900/30 border border-zinc-800/50 p-8 rounded-[2rem] flex items-start gap-6">
        <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="info" class="w-6 h-6"></i>
        </div>
        <div class="space-y-2">
            <h4 class="text-sm font-black text-white uppercase tracking-widest">Informações Importantes</h4>
            <ul class="text-[11px] text-zinc-500 font-medium space-y-1 list-disc pl-4">
                <li>O prazo para processamento do resgate é de até **48 horas úteis**.</li>
                <li>Certifique-se de que a chave PIX informada está correta; não nos responsabilizamos por transferências para chaves erradas.</li>
                <li>Saques realizados fora do horário comercial serão processados no próximo dia útil.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
