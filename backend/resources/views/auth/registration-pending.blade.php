@extends('layouts.app')

@section('title', 'Cadastro em análise — NexShape')

@section('content')
<div class="min-h-screen flex items-start justify-center px-4 sm:px-6 lg:px-8 pt-10 sm:pt-16 lg:pt-20 pb-12 relative animate-fade-in overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-amber-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-amber-500/20 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-user-clock text-3xl text-amber-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Cadastro em análise</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                Um administrador vai rever o seu pedido. Receberá acesso à plataforma após aprovação.
            </p>
        </div>

        @include('auth.partials.verify-email-card')

        <p class="text-center text-[10px] text-zinc-600 font-bold uppercase tracking-widest">
            Dúvidas? Contacte o suporte da sua academia.
        </p>
    </div>
</div>

<script>
    (function () {
        const resendForm = document.getElementById('verify-resend-form');
        if (!resendForm) return;

        function setVerifyFeedback(message, isError) {
            const el = document.getElementById('verify-resend-feedback');
            if (!el) return;
            if (!message) {
                el.classList.add('hidden');
                el.textContent = '';
                el.className = 'hidden mb-6 p-4 rounded-2xl text-[11px] font-bold uppercase tracking-widest';
                return;
            }
            el.textContent = message;
            el.classList.remove('hidden');
            el.className = 'mb-6 p-4 rounded-2xl text-[11px] font-bold uppercase tracking-widest ' +
                (isError ? 'bg-red-500/10 border border-red-500/20 text-red-400' : 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400');
        }

        resendForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('verify-resend-submit');
            setVerifyFeedback('', false);
            if (btn) btn.disabled = true;
            try {
                const fd = new FormData(this);
                const res = await fetch(this.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    setVerifyFeedback(data.message || 'E-mail enviado.', false);
                    return;
                }
                setVerifyFeedback(data.message || 'Não foi possível reenviar. Tente em instantes.', true);
            } catch (err) {
                setVerifyFeedback('Erro de rede. Tente novamente.', true);
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    })();
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
