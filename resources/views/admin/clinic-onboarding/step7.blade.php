@extends('layouts.clinic-onboarding')

@section('title', 'Configuração da Agenda')

@section('content')
<form action="{{ route('admin.clinic-onboarding.step.save', [$company, 7]) }}" method="POST" class="space-y-10">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="space-y-8">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-clock mr-3 text-blue-500"></i> Horário de Funcionamento
            </h3>
            
            <div class="space-y-6">
                @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'] as $dia)
                <div class="flex items-center justify-between p-4 bg-white/5 border border-white/5 rounded-2xl">
                    <span class="text-sm font-semibold text-zinc-400">{{ $dia }}</span>
                    <div class="flex items-center space-x-2">
                        <input type="time" value="08:00" class="bg-zinc-900 border border-white/10 rounded-lg px-3 py-2 text-white text-xs">
                        <span class="text-zinc-600">às</span>
                        <input type="time" value="18:00" class="bg-zinc-900 border border-white/10 rounded-lg px-3 py-2 text-white text-xs">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-8">
            <h3 class="text-white font-bold flex items-center">
                <i class="fas fa-calendar-check mr-3 text-emerald-500"></i> Padrões de Atendimento
            </h3>

            <div class="space-y-6">
                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-zinc-400">Duração Padrão da Consulta (min)</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none">
                        <option value="15">15 minutos</option>
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60" selected>1 hora</option>
                        <option value="90">1 hora e 30 min</option>
                    </select>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-zinc-400">Intervalo entre Consultas (min)</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none">
                        <option value="0">Sem intervalo</option>
                        <option value="5">5 minutos</option>
                        <option value="10">10 minutos</option>
                        <option value="15" selected>15 minutos</option>
                    </select>
                </div>

                <div class="p-6 bg-blue-500/5 border border-blue-500/20 rounded-2xl">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Essas configurações servem como padrão para novos profissionais, mas cada um pode personalizar sua própria agenda individualmente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-between">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 6]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Salvar Agenda
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>
</form>
@endsection
