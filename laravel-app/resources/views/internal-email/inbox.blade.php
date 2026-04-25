@extends('internal-email.layout')

@section('title', 'Caixa de Entrada')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Atualizar" onclick="location.reload()"><i class="fas fa-sync-alt"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Marcar como lida"><i class="fas fa-envelope-open"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Arquivar"><i class="fas fa-archive"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Excluir"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
@endsection

@section('toolbar-right')
    <div class="dropdown">
        <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-filter"></i></button>
        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end p-2 border-secondary shadow-lg">
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox') }}">Tudo</a></li>
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox', ['filter' => 'unread']) }}">Não Lidas</a></li>
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox', ['filter' => 'system']) }}">Sistema</a></li>
        </ul>
    </div>
@endsection

@section('email-content')
    <div class="divide-y divide-white/5">
        @forelse($messages as $msg)
            <div onclick="window.location='{{ route('internal-email.show', $msg) }}'" 
                 class="email-row group/row {{ $msg->is_read ? 'opacity-80 hover:opacity-100' : 'unread' }}">
                
                <!-- Selection & Status -->
                <div class="flex items-center gap-4 mr-8 flex-shrink-0">
                    <input type="checkbox" onclick="event.stopPropagation()" class="w-4 h-4 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500/20">
                    <div class="relative">
                        <i class="far fa-star text-zinc-600 group-hover/row:text-amber-500/50 transition-colors"></i>
                        @if(!$msg->is_read)
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full"></span>
                        @endif
                    </div>
                </div>

                <!-- Sender -->
                <div class="flex-shrink-0 mr-8 w-44">
                    <span class="text-sm truncate block {{ $msg->is_read ? 'text-zinc-400' : 'text-white font-black' }}">
                        {{ $msg->sender->name }}
                    </span>
                </div>

                <!-- Subject & Snippet -->
                <div class="flex-grow min-w-0 flex items-center gap-3">
                    <div class="truncate text-sm flex items-center gap-2">
                        @if($msg->attachments->count() > 0)
                            <i class="fas fa-paperclip text-zinc-600 text-[10px]"></i>
                        @endif
                        <span class="{{ $msg->is_read ? 'text-zinc-300' : 'text-white font-bold' }}">{{ $msg->subject }}</span>
                        <span class="text-zinc-600 mx-1">—</span>
                        <span class="text-zinc-500 font-medium">{{ Str::limit(strip_tags($msg->content), 80) }}</span>
                    </div>
                </div>

                <!-- Metadados -->
                <div class="flex-shrink-0 ml-8 text-right w-24">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-600">
                        {{ $msg->sent_at->isToday() ? $msg->sent_at->format('H:i') : $msg->sent_at->format('d M') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-inbox text-3xl text-zinc-700"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Caixa de entrada vazia</h3>
                <p class="text-zinc-500 text-sm max-w-xs mx-auto">Tudo limpo por aqui! Aproveite este momento para focar no seu treino.</p>
            </div>
        @endforelse
    </div>
@endsection
