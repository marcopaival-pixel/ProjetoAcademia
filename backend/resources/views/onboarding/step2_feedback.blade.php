@extends('layouts.app')

@section('title', 'Excelente passo! — NexShape')

@section('content')
    <div class="min-h-screen bg-zinc-50 flex flex-col items-center justify-center p-6 relative overflow-hidden">
        <!-- Header fixo no topo -->
        <div class="absolute top-12 left-0 w-full px-12">
            <h1 class="text-xl font-black text-blue-600 tracking-tighter italic">nexshape</h1>
        </div>

        <!-- Card Central -->
        <div
            class="w-full max-w-lg bg-white shadow-xl rounded-3xl p-10 md:p-16 space-y-12 animate-slide-in relative z-10 border border-zinc-100">
            <header class="text-center space-y-6">
                <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 leading-tight">Excelente! Você acabou de dar um
                    enorme passo na sua jornada.</h2>
                <p class="text-zinc-500 text-sm md:text-base leading-relaxed">
                    Você sabia que registrar os alimentos consumidos é um método cientificamente comprovado para obter
                    sucesso? O nome disso é "automonitoramento". Quanto mais consistente você for, maior a probabilidade de
                    alcançar suas metas.
                </p>
                <p class="text-zinc-800 font-bold text-sm md:text-base">Agora, vamos falar sobre sua meta de
                    {{ $data['goal'] ?? 'peso' }}.</p>
            </header>

            <div class="flex items-center justify-between pt-6">
                <a href="{{ route('onboarding.step2') }}"
                    class="px-8 py-3 text-blue-600 font-bold hover:bg-blue-50 rounded-xl transition-all border border-blue-100 uppercase text-xs tracking-widest">
                    VOLTAR
                </a>
                <a href="{{ route('onboarding.step2.obstacles') }}"
                    class="px-12 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 uppercase text-xs tracking-widest">
                    PRÓXIMO
                </a>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f9fafb !important;
        }

        .site-header,
        .site-footer {
            display: none !important;
        }

        .animate-slide-in {
            animation: slideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
@endsection