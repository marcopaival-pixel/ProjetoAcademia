@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Conta Criada!</h2>
            <p class="mt-2 text-sm text-slate-600">
                Seja bem-vindo ao <strong>{{ $accessLink->system_name }}</strong>.
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Seu Link de Acesso Direto</p>
                <div id="access-link" class="text-blue-600 font-medium break-all mb-4">
                    {{ $accessLink->system_url }}
                </div>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ $accessLink->system_url }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Acessar Sistema
                    </a>
                    <button onclick="copyToClipboard('{{ $accessLink->system_url }}')" class="w-full flex justify-center py-3 px-4 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-all">
                        Copiar Link
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 text-center hover:border-blue-300 transition-colors">
                    <p class="text-xs font-bold text-slate-400 mb-2 uppercase">Acesso Mobile</p>
                    @if($accessLink->qr_code_path)
                        <img src="{{ asset('storage/' . $accessLink->qr_code_path) }}" alt="QR Code" class="mx-auto w-24 h-24 mb-2">
                    @endif
                    <p class="text-[10px] text-slate-500">Aponte a câmera para acessar</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col justify-center items-center hover:border-blue-300 transition-colors">
                    <p class="text-xs font-bold text-slate-400 mb-2 uppercase">Dica Pro</p>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p class="text-[10px] text-slate-600 font-medium">Salve nos Favoritos (Ctrl+D)</p>
                </div>
            </div>

            <div class="text-center space-y-4 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-500">Instale como aplicativo no seu computador ou celular para acesso rápido.</p>
                <button id="install-pwa" class="hidden text-blue-600 text-sm font-bold hover:underline">
                    Instalar Aplicativo (PWA)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link copiado para a área de transferência!');
        });
    }

    // PWA Install Logic (Basic)
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const installBtn = document.getElementById('install-pwa');
        if (installBtn) installBtn.classList.remove('hidden');
    });

    const installBtn = document.getElementById('install-pwa');
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                }
                deferredPrompt = null;
            }
        });
    }
</script>
@endsection
