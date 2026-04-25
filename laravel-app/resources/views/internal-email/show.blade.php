@extends('internal-email.layout')

@section('title', $message->subject)

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('internal-email.inbox') }}" class="btn btn-sm btn-link text-muted p-0" title="Voltar"><i class="fas fa-arrow-left"></i></a>
        <div class="d-flex gap-2">
            @if($message->excluded_at_sender || $message->excluded_at_receiver)
                <form action="{{ route('internal-email.restore', $message) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Restaurar"><i class="fas fa-undo"></i></button>
                </form>
                <form action="{{ route('internal-email.permanent', $message) }}" method="POST" class="m-0"
                data-confirm-delete
                data-confirm-title="Exclusão permanente"
                data-confirm-message="Excluir esta mensagem permanentemente? Esta ação não pode ser desfeita.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Excluir Permanentemente"><i class="fas fa-trash"></i></button>
                </form>
            @else
                <form action="{{ route('internal-email.unread', $message) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Marcar como não lida"><i class="fas fa-envelope"></i></button>
                </form>
                <form action="{{ route('internal-email.destroy', $message) }}" method="POST" class="m-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Excluir"><i class="fas fa-trash-alt"></i></button>
                </form>
            @endif
            
            @php($otherUser = $message->sender_id === auth()->id() ? $message->recipient : $message->sender)
            @if($otherUser && $otherUser->id !== auth()->id())
                <form action="{{ route('user.block', $otherUser) }}" method="POST" class="m-0 border-l border-white/10 pl-2">
                    @csrf
                    <button type="submit" 
                            class="btn btn-sm btn-link {{ auth()->user()->isBlocking($otherUser) ? 'text-danger' : 'text-muted' }} p-0" 
                            title="{{ auth()->user()->isBlocking($otherUser) ? 'Desbloquear Usuário' : 'Bloquear Usuário' }}">
                        <i class="fas fa-user-slash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection

@section('email-content')
    <div class="p-12 md:p-16 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-black text-white tracking-tight mb-4">{{ $message->subject }}</h1>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-zinc-800 rounded-full text-[10px] font-bold text-zinc-500 uppercase tracking-widest border border-white/5">
                    {{ $message->created_at->translatedFormat('d M Y, H:i') }}
                </span>
                @if(!$message->is_read)
                    <span class="px-3 py-1 bg-blue-600/20 rounded-full text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] border border-blue-500/20 animate-pulse">
                        Nova Mensagem
                    </span>
                @endif
            </div>
        </div>

        <!-- Sender Info Card -->
        <div class="flex items-center justify-between p-6 rounded-3xl bg-white/5 border border-white/5 mb-12 group">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-2xl shadow-lg group-hover:scale-105 transition-transform">
                    {{ substr($message->sender->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-white font-black text-lg">{{ $message->sender->name }}</h4>
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">{{ $message->sender->email }}</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-1">Para</p>
                <p class="text-[11px] text-zinc-400 font-bold">{{ $message->recipient->name }}</p>
            </div>
        </div>

        <!-- Body -->
        <div class="prose prose-invert max-w-none">
            <div class="text-zinc-300 text-lg leading-relaxed font-medium whitespace-pre-wrap selection:bg-blue-500/30">
                {{ $message->content }}
            </div>
        </div>

        @if($message->attachments->count() > 0)
            <div class="mt-16 pt-8 border-t border-white/5">
                <h6 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <i class="fas fa-paperclip"></i> Anexos ({{ $message->attachments->count() }})
                </h6>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($message->attachments as $anexo)
                        <a href="{{ Storage::url($anexo->file_path) }}" target="_blank" 
                           class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all group/attachment no-underline">
                            <div class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-500 group-hover/attachment:text-blue-400 transition-colors">
                                @if(in_array($anexo->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <i class="fas fa-image"></i>
                                @elseif($anexo->file_type === 'pdf')
                                    <i class="fas fa-file-pdf"></i>
                                @else
                                    <i class="fas fa-file-alt"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate mb-0.5">{{ $anexo->file_name }}</p>
                                <p class="text-[9px] text-zinc-600 font-black uppercase tabular-nums">
                                    {{ number_format($anexo->file_size / 1024, 1) }} KB
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="mt-20 flex flex-wrap gap-4 pt-10 border-t border-white/5">
            <a href="{{ route('internal-email.create', ['reply_to' => $message->id]) }}" 
               class="flex items-center gap-3 px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-blue-600 hover:text-white transition-all no-underline">
                <i class="fas fa-reply"></i> RESPONDER
            </a>
            <a href="{{ route('internal-email.create', ['forward_from' => $message->id]) }}" 
               class="flex items-center gap-3 px-8 py-4 bg-white/5 text-white font-black rounded-2xl border border-white/10 hover:bg-white/10 transition-all no-underline">
                <i class="fas fa-share"></i> ENCAMINHAR
            </a>
        </div>
    </div>

    <style>
        .hover-bg-opacity-10:hover { background-color: rgba(255, 255, 255, 0.1) !important; }
        .transition-all { transition: all 0.2s ease; }
    </style>
@endsection
