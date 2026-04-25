@extends('layouts.clinic-onboarding')

@section('title', 'Seleção do Plano')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white/5 border border-white/10 rounded-3xl p-8 flex flex-col hover:border-blue-500/50 transition-all group relative overflow-hidden">
                @if($plan->name == 'Premium')
                    <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-tighter">Recomendado</div>
                @endif
                
                <h3 class="text-white font-bold text-xl mb-2">{{ $plan->name }}</h3>
                <div class="flex items-baseline mb-6">
                    <span class="text-3xl font-black text-white">R$ {{ number_format($plan->price_per_professional ?: $plan->price, 2, ',', '.') }}</span>
                    <span class="text-zinc-500 text-xs ml-2">/ profissional</span>
                </div>

                <ul class="space-y-4 mb-8 flex-grow">
                    @foreach($plan->features as $feature)
                        <li class="flex items-center text-sm text-zinc-400">
                            <i class="fas fa-check text-blue-500 mr-3 text-xs"></i>
                            {{ $feature->feature_name }}
                        </li>
                    @endforeach
                </ul>

                <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 2]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold transition-all {{ $plan->name == 'Premium' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500' : 'bg-white/5 text-zinc-300 hover:bg-white/10' }}">
                        Selecionar {{ $plan->name }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-start">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 1]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
    </div>
</div>
@endsection
