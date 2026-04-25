@extends('layouts.app')

@section('title', 'Solicitações de Vínculo — NexShape')

@section('content')
<div class="py-12 space-y-12 animate-fade-in max-w-[1200px] mx-auto px-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-white tracking-tight">Solicitações de Vínculo</h1>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Gerencie novos pacientes que desejam ser acompanhados por você</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($requests as $request)
            <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-8 transition-all hover:border-blue-500/30">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-400 font-black text-xl border border-blue-500/20">
                        {{ strtoupper(mb_substr($request->patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">{{ $request->patient->name }}</h3>
                        <p class="text-zinc-500 text-sm font-medium">{{ $request->patient->email }}</p>
                        @if($request->message)
                            <div class="mt-4 p-4 bg-black/20 rounded-2xl border border-white/5 max-w-lg">
                                <p class="text-xs text-zinc-400 italic">"{{ $request->message }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('professional.requests.reject', $request) }}" method="POST" class="flex-1 md:flex-none">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700 font-bold rounded-xl transition-all text-xs uppercase tracking-widest border border-white/5">
                            Rejeitar
                        </button>
                    </form>
                    <form action="{{ route('professional.requests.approve', $request) }}" method="POST" class="flex-1 md:flex-none">
                        @csrf
                        <button type="submit" class="w-full px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-500/20 text-xs uppercase tracking-widest">
                            Aprovar Paciente
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[3rem]">
                <div class="w-20 h-20 bg-zinc-900 rounded-full flex items-center justify-center mb-6 mx-auto border border-white/10">
                    <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h4 class="text-xl font-black text-white mb-2">Nenhuma solicitação pendente</h4>
                <p class="text-zinc-500 font-medium max-w-sm mx-auto text-sm">Quando um paciente usar seu código para solicitar vínculo, ele aparecerá aqui para aprovação.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
