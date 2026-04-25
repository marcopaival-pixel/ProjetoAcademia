@extends('internal-email.layout')

@section('title', 'Nova Mensagem')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('internal-email.inbox') }}" class="btn btn-sm btn-link text-muted p-0" title="Cancelar"><i class="fas fa-arrow-left"></i></a>
        <span class="text-white fw-bold small">Nova Mensagem</span>
    </div>
@endsection

@section('email-content')
    <div class="p-10 md:p-16 max-w-4xl mx-auto">
        <div class="mb-12">
            <h1 class="text-3xl font-black text-white tracking-tight">Nova Mensagem</h1>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-2">NexShape Connect Engine</p>
        </div>

        <form action="{{ route('internal-email.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
            @csrf
            
            @if(isset($replyTo))
                <input type="hidden" name="parent_id" value="{{ $replyTo->id }}">
                <input type="hidden" name="recipient_id[]" value="{{ $replyTo->sender_id }}">
                <div class="p-6 rounded-3xl bg-blue-600/10 border border-blue-500/20 flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest">Respondendo para</p>
                        <p class="text-white font-bold">{{ $replyTo->sender->name }}</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2">Destinatários</label>
                    <select name="recipient_id[]" class="w-full bg-white/5 border border-white/5 text-white rounded-3xl p-5 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all custom-select-dark" multiple required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (isset($preSelectedTo) && $preSelectedTo == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[9px] text-zinc-600 font-bold ml-4 uppercase italic">* Pressione Ctrl para múltipla seleção</p>
                </div>
            @endif

            <div class="space-y-3">
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2">Assunto</label>
                <input type="text" name="subject" value="{{ isset($replyTo) ? 'Re: ' . $replyTo->subject : '' }}" 
                    class="w-full bg-white/5 border border-white/5 text-white rounded-2xl p-5 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-lg font-bold" 
                    placeholder="Sobre o que vamos conversar?" required>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2">Sua Mensagem</label>
                <textarea name="content" rows="10" 
                    class="w-full bg-white/5 border border-white/5 text-white rounded-[2rem] p-8 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all leading-relaxed" 
                    placeholder="Escreva sua mensagem aqui..." required></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2">Anexos</label>
                <div class="relative group">
                    <input type="file" name="attachments[]" multiple
                        class="block w-full text-xs text-zinc-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-400 hover:file:bg-zinc-700 cursor-pointer transition-all">
                </div>
            </div>

            <div class="pt-10 flex flex-wrap items-center gap-6 border-t border-white/5">
                <button type="submit" class="group relative px-12 py-5 bg-blue-600 text-white font-black rounded-2xl overflow-hidden hover:pr-16 transition-all active:scale-95 shadow-lg shadow-blue-500/20">
                    <span class="relative z-10 flex items-center gap-3">
                        <i class="fas fa-paper-plane text-xs"></i>
                        ENVIAR AGORA
                    </span>
                    <i class="fas fa-arrow-right absolute right-6 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all"></i>
                </button>
                <button type="button" onclick="history.back()" class="text-zinc-600 hover:text-white font-black text-xs uppercase tracking-[0.2em] transition-colors">
                    Descartar Rascunho
                </button>
            </div>
        </form>
    </div>

    <style>
        .form-select option {
            background-color: #1a1d23;
            color: white;
            padding: 10px;
        }
    </style>
@endsection
