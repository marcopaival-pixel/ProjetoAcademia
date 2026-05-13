@extends('layouts.app')

@section('title', 'Adquirir Créditos de IA — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up mx-auto px-4 md:px-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">Upgrade de Inteligência</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Pacotes de <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-emerald-600">Créditos IA</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('ai-credits.dashboard') }}" class="px-6 py-3 bg-zinc-900 text-zinc-400 hover:text-white font-bold rounded-xl transition-all uppercase text-xs tracking-widest border border-zinc-800">
                Ver Histórico
            </a>
        </div>
    </div>

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($packages as $package)
        <div class="relative group bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] overflow-hidden transition-all hover:border-emerald-500/30 shadow-2xl flex flex-col">
            <!-- Glow Effect -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
            
            <div class="relative z-10 flex-grow">
                <div class="w-16 h-16 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 mb-8 group-hover:scale-110 transition-transform">
                    <i data-lucide="zap" class="w-8 h-8"></i>
                </div>
                
                <h3 class="text-3xl font-black text-white uppercase tracking-tighter mb-2">{{ $package->name }}</h3>
                <p class="text-zinc-500 text-sm font-medium mb-8 leading-relaxed italic">Adquira {{ number_format($package->credits, 0, ',', '.') }} créditos para uso imediato em qualquer ferramenta de IA.</p>
                
                <div class="space-y-4 mb-10">
                    <div class="flex items-center gap-3 text-zinc-400">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-bold uppercase tracking-tight">Sem validade mensal</span>
                    </div>
                    <div class="flex items-center gap-3 text-zinc-400">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-bold uppercase tracking-tight">Uso em todas as features</span>
                    </div>
                    <div class="flex items-center gap-3 text-zinc-400">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-bold uppercase tracking-tight">Ativação instantânea</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-auto pt-8 border-t border-zinc-800">
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-xs font-black text-zinc-500 uppercase tracking-widest">R$</span>
                    <span class="text-5xl font-black text-white tabular-nums">{{ number_format($package->price, 2, ',', '.') }}</span>
                </div>
                
                <button onclick="buyPackage({{ $package->id }})" class="w-full py-5 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 uppercase tracking-widest text-xs flex items-center justify-center gap-3 group/btn">
                    Selecionar Pacote
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- FAQ / Info Section -->
    <div class="bg-zinc-900 border border-zinc-800 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div>
                <h3 class="text-3xl font-black text-white uppercase tracking-tighter mb-6 italic">Por que adquirir créditos extras?</h3>
                <p class="text-zinc-400 text-lg font-medium leading-relaxed">
                    Créditos extras garantem que você nunca fique sem acesso às ferramentas de inteligência artificial da NexShape, mesmo que sua cota mensal do plano expire. Eles <span class="text-white font-black italic">não expiram mensalmente</span> e são consumidos apenas quando o saldo do seu plano acaba.
                </p>
            </div>
            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start gap-6 p-6 bg-zinc-950 rounded-3xl border border-zinc-800">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black uppercase tracking-tight mb-2">Compra Segura</h4>
                        <p class="text-xs text-zinc-500 font-medium">Processamento via Gateway criptografado com liberação automática de saldo.</p>
                    </div>
                </div>
                <div class="flex items-start gap-6 p-6 bg-zinc-950 rounded-3xl border border-zinc-800">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="infinity" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black uppercase tracking-tight mb-2">Sem Validade</h4>
                        <p class="text-xs text-zinc-500 font-medium">Os créditos comprados avulsos são vitalícios na sua conta NexShape.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    async function buyPackage(packageId) {
        const { value: confirm } = await Swal.fire({
            title: 'Confirmar Seleção',
            text: "Deseja prosseguir para o pagamento deste pacote?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#18181b',
            confirmButtonText: 'SIM, PROSSEGUIR',
            cancelButtonText: 'CANCELAR',
            background: '#09090b',
            color: '#ffffff'
        });

        if (!confirm) return;

        Swal.fire({
            title: 'Processando...',
            text: 'Iniciando conexão segura com o gateway de pagamento.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#09090b',
            color: '#ffffff'
        });

        try {
            const response = await fetch("{{ route('ai-credits.buy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ package_id: packageId })
            });

            const data = await response.json();

            if (data.success) {
                if (data.init_point) {
                    window.location.href = data.init_point;
                } else if (data.reload) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: data.message,
                        icon: 'success',
                        background: '#09090b',
                        color: '#ffffff'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } else {
                Swal.fire({
                    title: 'Erro',
                    text: data.message || 'Erro ao processar compra.',
                    icon: 'error',
                    background: '#09090b',
                    color: '#ffffff'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Erro Fatal',
                text: 'Não foi possível conectar ao servidor.',
                icon: 'error',
                background: '#09090b',
                color: '#ffffff'
            });
        }
    }
</script>
@endpush

<style>
    body {
        background-color: #080a0f;
        background-image: 
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%);
        background-attachment: fixed;
    }
</style>
@endsection
