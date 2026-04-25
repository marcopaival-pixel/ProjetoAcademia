{{-- Cartão: verificação de e-mail (página /register após cadastro e /verify-email) --}}
<div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl text-center">
    {{-- Verificação suspensa
    <p class="text-zinc-400 text-sm mb-8 leading-relaxed">
        Para acessar as funcionalidades do <strong>Nexshape</strong>, você precisa confirmar que este e-mail lhe pertence. Não recebeu? Verifique sua pasta de spam.
    </p>

    <div id="verify-resend-feedback" class="hidden mb-6 p-4 rounded-2xl text-[11px] font-bold uppercase tracking-widest"></div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-[11px] font-bold uppercase tracking-widest">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-[11px] font-bold uppercase tracking-widest">
            {{ session('error') }}
        </div>
    @endif

    <form id="verify-resend-form" method="POST" action="{{ route('verification.resend') }}" class="space-y-6">
        @csrf
        <button type="submit" id="verify-resend-submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:pointer-events-none text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
            Reenviar E-mail de Verificação
        </button>
    </form>
    --}}

    <div class="mt-8 border-t border-white/5 pt-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest hover:text-white transition-colors">
                Sair e entrar com outra conta
            </button>
        </form>
    </div>
</div>
