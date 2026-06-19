@extends('layouts.app')

@section('title', 'Meus Profissionais — NexShape')

@section('content')
<div class="py-12 space-y-12 animate-fade-in max-w-[1200px] mx-auto px-6">
    <div class="text-center space-y-4">
        <h1 class="text-4xl font-black text-white italic tracking-tighter">Meus <span class="text-indigo-400 underline decoration-indigo-500/30">Profissionais</span></h1>
        <p class="text-zinc-500 font-medium text-lg">Gerencie o acesso aos seus dados de saúde por cada especialista.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($links as $link)
            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[3rem] p-8 space-y-8 shadow-2xl">
                <!-- Header Profissional -->
                <div class="flex items-center gap-6 pb-6 border-b border-white/5">
                    @if($link->professional->avatar)
                        <img src="{{ asset('storage/' . $link->professional->avatar) }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/5">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl">
                            {{ mb_substr($link->professional->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-black text-white">{{ $link->professional->name }}</h3>
                        <p class="text-indigo-400 text-[10px] font-black uppercase tracking-widest">
                            {{ $link->professional->professionalProfile?->profession?->name ?? 'Especialista' }}
                        </p>
                    </div>
                </div>

                <!-- Formulário de Permissões -->
                <form action="{{ route('patient.my-professionals.update-permissions', $link->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <h4 class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-4">O que este profissional pode acessar?</h4>
                        
                        @php
                            $perms = $link->patient_permissions ?? [];
                            $modules = [
                                'view_assessments' => ['icon' => 'clipboard-check', 'label' => 'Ver Avaliações Físicas'],
                                'view_diets' => ['icon' => 'utensils', 'label' => 'Ver Planos Alimentares'],
                                'view_workouts' => ['icon' => 'dumbbell', 'label' => 'Ver Fichas de Treino'],
                                'view_medical_records' => ['icon' => 'file-medical', 'label' => 'Ver Prontuário Médico'],
                                'view_evolution' => ['icon' => 'line-chart', 'label' => 'Ver Gráficos de Evolução'],
                            ];
                        @endphp

                        <div class="space-y-3">
                            @foreach($modules as $key => $module)
                                <label class="flex items-center justify-between p-4 bg-black/20 rounded-2xl border border-white/5 cursor-pointer hover:bg-black/40 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-400">
                                            <i class="fas fa-{{ $module['icon'] }}"></i>
                                        </div>
                                        <span class="text-white text-sm font-bold">{{ $module['label'] }}</span>
                                    </div>
                                    <div class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="permissions[{{ $key }}]" value="1" class="sr-only peer" {{ (isset($perms[$key]) && $perms[$key] == '1') || !isset($perms[$key]) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl transition-all">
                            Salvar Permissões
                        </button>
                </form>
                        <form action="{{ route('patient.my-professionals.revoke', $link->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja revogar o acesso deste profissional?');">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 font-black uppercase text-[10px] tracking-widest rounded-2xl transition-all border border-rose-500/20">
                                Revogar Acesso
                            </button>
                        </form>
                    </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[3rem]">
                <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-times text-zinc-500 text-2xl"></i>
                </div>
                <h3 class="text-white font-black text-xl mb-2">Nenhum Profissional Vinculado</h3>
                <p class="text-zinc-500 font-bold mb-6">Você ainda não concedeu acesso a nenhum especialista.</p>
                <a href="{{ route('patient.professionals.search') }}" class="inline-block py-3 px-8 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase tracking-widest text-[10px] rounded-2xl transition-all">
                    Buscar Profissionais
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
