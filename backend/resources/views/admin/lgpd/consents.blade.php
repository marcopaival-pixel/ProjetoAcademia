@extends('layouts.admin')

@section('title', 'Registos de Consentimento (LGPD)')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-white">Log de Aceites e Termos</h2>
            <p class="text-sm text-zinc-400 mt-1">Registo inalterável de concordância com Termos de Uso e Política de Privacidade.</p>
        </div>
        <a href="{{ route('admin.lgpd.index') }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 border border-white/5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-zinc-700 transition-all">
            &larr; Voltar
        </a>
    </div>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="p-8 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">
                        <th class="pb-4 pt-2 font-medium">Data/Hora</th>
                        <th class="pb-4 pt-2 font-medium">Usuário</th>
                        <th class="pb-4 pt-2 font-medium">Termo(s) Aceito(s)</th>
                        <th class="pb-4 pt-2 font-medium">Endereço IP</th>
                        <th class="pb-4 pt-2 font-medium">Agente / Fingerprint</th>
                        <th class="pb-4 pt-2 font-medium">Acções</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-white/5">
                    @forelse($consents as $consent)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-4 text-zinc-300 font-mono text-xs">{{ \Carbon\Carbon::parse($consent->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td class="py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-200">{{ $consent->user->name ?? 'Usuário '.$consent->user_id }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $consent->user->email ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 text-zinc-400">{{ $consent->type }}</td>
                        <td class="py-4 text-zinc-400 font-mono text-xs">{{ $consent->ip_address }}</td>
                        <td class="py-4 text-zinc-500 text-[10px] max-w-[200px] truncate" title="{{ $consent->user_agent }}">
                            {{ $consent->user_agent }}
                        </td>
                        <td class="py-4 text-right">
                            @if($consent->user)
                                <a href="{{ route('admin.lgpd.export-user', $consent->user_id) }}" class="text-xs text-blue-500 hover:text-white" title="Exportar Log Completo">
                                    <i class="fas fa-file-export"></i> ZIP
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-zinc-500 italic">Nenhum consentimento encontrado na base de dados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consents->hasPages())
        <div class="px-8 py-4 border-t border-white/5 border-b">
            {{ $consents->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection
