@extends('layouts.app')

@section('title', 'Verificação de E-mail — NexShape')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<div class="min-h-screen flex bg-zinc-950 font-['Outfit'] selection:bg-emerald-500/30 overflow-hidden">
    <!-- Lado Esquerdo: Conteúdo -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12 relative z-10 bg-zinc-950">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-md w-full mx-auto relative">
            <!-- Header -->
            <div class="mb-10 text-left animate-fade-in-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i data-lucide="mail-search" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tighter uppercase">NEX<span class="text-emerald-500">SHAPE</span></span>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2 leading-tight">Verifique seu e-mail.</h1>
                <p class="text-zinc-500 text-lg">Quase lá! Enviamos um link de confirmação para o seu endereço de e-mail.</p>
            </div>

            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                @include('auth.partials.verify-email-card')
            </div>
        </div>
    </div>

    <!-- Lado Direito: Imagem/Conteúdo Premium -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden bg-zinc-900">
        <div class="absolute inset-0 z-10 bg-gradient-to-r from-zinc-950 via-transparent to-transparent opacity-60"></div>
        <div class="absolute inset-0 z-10 bg-gradient-to-t from-zinc-950 via-transparent to-transparent opacity-40"></div>
        
        <img src="https://images.unsplash.com/photo-1434494878577-86c23bddca6a?auto=format&fit=crop&q=80&w=1470" 
             alt="Email Verification" 
             class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 mix-blend-luminosity scale-110 animate-slow-zoom">

        <div class="absolute inset-0 z-20 flex flex-col justify-end p-20">
            <div class="max-w-md space-y-8 animate-fade-in-right">
                <div class="inline-flex items-center gap-3 px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-full backdrop-blur-md">
                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Protocolo de Identidade</span>
                </div>
                
                <h2 class="text-5xl font-black text-white tracking-tighter uppercase italic leading-[0.9]">Confirmação <br><span class="text-blue-500">Neural.</span></h2>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-blue-500 shrink-0">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Integridade Total</h4>
                            <p class="text-xs text-zinc-500 font-medium italic">A verificação de e-mail garante que apenas você tenha acesso ao seu ecossistema de performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slowZoom {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-fade-in-right { animation: fadeInRight 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
</style>
@endsection
