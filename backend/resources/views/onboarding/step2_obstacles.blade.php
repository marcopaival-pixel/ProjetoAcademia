@extends('layouts.app')

@section('title', 'Quais foram os desafios? — NexShape')

@section('content')
    <div class="min-h-screen bg-zinc-50 flex flex-col items-center justify-center p-6 relative overflow-hidden">
        <!-- Header fixo no topo -->
        <div class="absolute top-12 left-0 w-full px-12">
            <h1 class="text-xl font-black text-blue-600 tracking-tighter italic">nexshape</h1>
        </div>

        <!-- Card Central -->
        <div
            class="w-full max-w-lg bg-white shadow-xl rounded-3xl p-10 md:p-16 space-y-12 animate-slide-in relative z-10 border border-zinc-100">
            <header class="text-center space-y-4">
                <h2 class="text-xl md:text-2xl font-bold text-zinc-900 leading-tight">Anteriormente, quais obstáculos
                    impediram você de perder peso?</h2>
                <p class="text-zinc-500 text-sm">Selecione todas as opções que descrevem sua situação.</p>
            </header>

            <form action="{{ route('onboarding.step2.obstacles.save') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-3">
                    @php
                        $options = [
                            'Falta de tempo',
                            'Era muito difícil seguir o plano de emagrecimento',
                            'Não gostava da comida',
                            'Foi difícil fazer escolhas alimentares',
                            'Comer socialmente e eventos',
                            'Desejo de comer certos alimentos',
                            'Falta de progresso'
                        ];
                    @endphp

                    @foreach ($options as $option)
                        <label
                            class="relative flex items-center p-4 cursor-pointer rounded-2xl border-2 border-zinc-100 hover:border-blue-100 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 group">
                            <input type="checkbox" name="obstacles[]" value="{{ $option }}" class="peer hidden">
                            <span
                                class="text-sm font-medium text-zinc-700 peer-checked:text-blue-700 w-full text-center">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-8">
                    <a href="{{ route('onboarding.step2.feedback') }}"
                        class="px-8 py-3 text-blue-600 font-bold hover:bg-blue-50 rounded-xl transition-all border border-blue-100 uppercase text-xs tracking-widest">
                        VOLTAR
                    </a>
                    <button type="submit"
                        class="px-12 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 uppercase text-xs tracking-widest">
                        PRÓXIMO
                    </button>
                </div>
            </form>
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

        label:has(input:checked) {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
        }

        label:has(input:checked) span {
            color: #1d4ed8 !important;
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