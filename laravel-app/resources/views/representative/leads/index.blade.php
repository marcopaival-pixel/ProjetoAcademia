@extends('layouts.app')

@section('title', 'CRM - Gestão de Leads')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">Meus <span class="text-emerald-500">Leads</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Gerencie seus contatos e prospectos comerciais.</p>
        </div>
        <a href="{{ route('representative.leads.create') }}" class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors inline-block">
            + Novo Lead
        </a>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-zinc-900/50">
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Nome / Empresa</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Contato</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-600 uppercase tracking-widest">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-900/30">
                @forelse($leads ?? [] as $lead)
                <!-- Implementar loop real aqui -->
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-zinc-600 font-medium italic">
                        Nenhum lead encontrado. Comece a prospectar!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
