@extends('layouts.admin')

@section('title', 'Gestão de Resgates - Representantes')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Solicitações de Resgate</h1>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-950 border-b border-zinc-800">
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Representante</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Valor</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Chave PIX</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Data</th>
                        <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-zinc-400">#{{ $withdrawal->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-white">{{ $withdrawal->representative->name }}</span>
                                <span class="text-xs text-zinc-500">{{ $withdrawal->representative->email }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-emerald-500">R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-400"><code>{{ $withdrawal->pix_key }}</code></td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest 
                                {{ $withdrawal->status === 'PENDENTE' ? 'bg-amber-500/10 text-amber-500' : 
                                   ($withdrawal->status === 'PAGO' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($withdrawal->status === 'RECUSADO' ? 'bg-rose-500/10 text-rose-500' : 'bg-blue-500/10 text-blue-500')) }}">
                                {{ $withdrawal->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <button onclick="openModal('{{ $withdrawal->id }}', '{{ $withdrawal->status }}', '{{ $withdrawal->admin_notes }}')" 
                                class="text-zinc-400 hover:text-white transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-zinc-500">Nenhuma solicitação encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-950/50">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal de Edição --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-2xl p-8 shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-6">Atualizar Solicitação</h3>
        
        <form id="updateForm" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Novo Status</label>
                <select name="status" id="modalStatus" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg py-3 px-4 text-white focus:border-emerald-500 outline-none">
                    <option value="PENDENTE">PENDENTE</option>
                    <option value="APROVADO">APROVADO</option>
                    <option value="PAGO">PAGO</option>
                    <option value="RECUSADO">RECUSADO</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Notas Administrativas</label>
                <textarea name="admin_notes" id="modalNotes" rows="3" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg py-3 px-4 text-white focus:border-emerald-500 outline-none"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-zinc-800 text-white font-bold rounded-lg hover:bg-zinc-700 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-emerald-500 text-zinc-950 font-bold rounded-lg hover:bg-emerald-400 transition-colors">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, status, notes) {
        document.getElementById('updateForm').action = `/admin/representatives/withdrawals/${id}/update`;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalNotes').value = notes || '';
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
@endsection
