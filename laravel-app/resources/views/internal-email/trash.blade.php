@extends('internal-email.layout')

@section('title', 'Lixeira')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Restaurar Selecionados"><i class="fas fa-undo"></i></button>
            <button class="btn btn-sm btn-link text-danger p-0" title="Excluir Permanentemente"><i class="fas fa-trash"></i></button>
        </div>
    </div>
@endsection

@section('email-content')
    <div class="divide-y divide-white/5">
        @forelse($messages as $msg)
            <div onclick="window.location='{{ route('internal-email.show', $msg) }}'" 
                 class="email-row group/row opacity-60 hover:opacity-100 italic">
                
                <!-- Selection & Status -->
                <div class="flex items-center gap-4 mr-8 flex-shrink-0">
                    <input type="checkbox" onclick="event.stopPropagation()" class="w-4 h-4 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500/20">
                    <i class="fas fa-trash-alt text-zinc-700"></i>
                </div>

                <!-- Entity (Sender or Recipient) -->
                <div class="flex-shrink-0 mr-8 w-44">
                    <span class="text-xs truncate block text-zinc-500">
                        @if($msg->sender_id === auth()->id())
                            Para: {{ $msg->recipient->name }}
                        @else
                            De: {{ $msg->sender->name }}
                        @endif
                    </span>
                </div>

                <!-- Subject & Snippet -->
                <div class="flex-grow min-w-0 flex items-center gap-3">
                    <div class="truncate text-sm flex items-center gap-2">
                        <span class="text-zinc-400 font-bold line-through group-hover:no-underline">{{ $msg->subject }}</span>
                        <span class="text-zinc-600 mx-1">—</span>
                        <span class="text-zinc-600 font-medium">{{ Str::limit(strip_tags($msg->content), 80) }}</span>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="flex-shrink-0 ml-8 text-right w-32">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-700">
                        Removido {{ $msg->updated_at->format('d/m') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-trash-alt text-3xl text-zinc-700"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Lixeira vazia</h3>
                <p class="text-zinc-500 text-sm max-w-xs mx-auto">Nada para restaurar por enquanto.</p>
            </div>
        @endforelse
    </div>
@endsection
