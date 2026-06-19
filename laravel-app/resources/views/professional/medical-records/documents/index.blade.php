@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-folder-open text-blue-500"></i>
            Exames e Documentos
        </h3>
        <button class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-upload"></i> Upload de Arquivo
        </button>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-zinc-800">
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Documento</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Categoria</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Tamanho / Tipo</th>
                    <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/50">
                @forelse($documents as $doc)
                    <tr class="hover:bg-zinc-800/30 transition-all group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-500 group-hover:text-blue-500 transition-colors">
                                    <i class="fas {{ $doc->file_type == 'pdf' ? 'fa-file-pdf' : 'fa-file-image' }}"></i>
                                </div>
                                <span class="text-white font-bold text-sm">{{ $doc->title }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-zinc-800 text-zinc-400 rounded-md text-[10px] font-black uppercase">
                                {{ $doc->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-zinc-500 text-sm">
                            {{ $doc->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-zinc-500 text-xs">
                            {{ number_format($doc->file_size / 1024, 1) }} KB / {{ strtoupper($doc->file_type) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="#" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-white transition-all"><i class="fas fa-eye"></i></a>
                                <a href="#" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-white transition-all"><i class="fas fa-download"></i></a>
                                <button class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-red-500 transition-all"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                                <i class="fas fa-folder-open text-xl"></i>
                            </div>
                            <p class="text-zinc-500 text-sm">Nenhum documento anexado ao prontuário.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $documents->links() }}
    </div>
</div>
@endsection



