@extends('layouts.admin')

@section('title', 'Configurações de Pagamento')

@section('content')
<div class="space-y-10 animate-fade-in max-w-6xl pb-20">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tighter italic">Gateways de Pagamento</h2>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.3em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Configuração Transacional do Ecossistema
            </p>
        </div>
        
        @if(session('success'))
            <div id="success-alert" class="bg-blue-600/10 border border-blue-500/20 px-6 py-3 rounded-2xl flex items-center gap-3 animate-bounce-in">
                <div class="w-6 h-6 rounded-full bg-blue-600/20 flex items-center justify-center">
                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">{{ session('success') }}</span>
            </div>
        @endif
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar: Gateway Selection -->
        <div class="lg:col-span-1 space-y-4">
            <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-4 mb-4">Selecione o Provedor</p>
            
            @foreach($gateways as $gw)
                @php 
                    $activeGw = request('gateway', 'mercadopago');
                    $isCurrent = $activeGw == $gw;
                    $config = $settings->get($gw);
                    $isActiveInDb = $config && $config->status == 'active';
                @endphp
                <a href="?gateway={{ $gw }}" class="group block relative overflow-hidden transition-all duration-500 {{ $isCurrent ? 'scale-105 z-10' : 'hover:scale-102 opacity-60 hover:opacity-100' }}">
                    <div class="p-6 rounded-[2rem] border {{ $isCurrent ? 'bg-zinc-900 border-blue-500/30' : 'bg-transparent border-white/5' }} transition-all">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center overflow-hidden">
                                    <span class="text-[10px] font-black text-white uppercase">{{ substr($gw, 0, 2) }}</span>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-white capitalize">{{ str_replace('pago', ' Pago', str_replace('seguro', ' Seguro', $gw)) }}</h4>
                                    @if($isActiveInDb)
                                        <span class="text-[8px] font-bold text-green-500 uppercase tracking-widest flex items-center gap-1">
                                            <span class="w-1 h-1 bg-green-500 rounded-full"></span> Ativo
                                        </span>
                                    @else
                                        <span class="text-[8px] font-bold text-zinc-600 uppercase tracking-widest">Inativo</span>
                                    @endif
                                </div>
                            </div>
                            @if($isCurrent)
                                <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Main Content: Configuration Form -->
        <div class="lg:col-span-3">
            @php 
                $selectedGateway = request('gateway', 'mercadopago');
                $currentSetting = $settings->get($selectedGateway);
            @endphp
            
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3.5rem] shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 blur-[100px] rounded-full -mr-20 -mt-20 group-hover:bg-blue-600/10 transition-all duration-700"></div>
                
                <header class="mb-12 relative z-10">
                    <h3 class="text-2xl font-black text-white tracking-tight italic capitalize">Configurar {{ str_replace('pago', ' Pago', str_replace('seguro', ' Seguro', $selectedGateway)) }}</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Defina as credenciais e parâmetros operacionais</p>
                </header>

                <form action="{{ route('admin.settings.payments.store') }}" method="POST" class="space-y-12 relative z-10">
                    @csrf
                    <input type="hidden" name="gateway" value="{{ $selectedGateway }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Left Column: Status & Environment -->
                        <div class="space-y-10">
                            <!-- Toggle: Status -->
                            <div class="space-y-4">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-1">Status do Gateway</label>
                                <div class="flex items-center gap-4 bg-zinc-950/50 p-2 rounded-[1.5rem] border border-white/5 w-fit">
                                    <button type="button" onclick="setStatus('active')" id="btn-status-active" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($currentSetting->status ?? 'inactive') == 'active' ? 'bg-green-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">
                                        Ativo
                                    </button>
                                    <button type="button" onclick="setStatus('inactive')" id="btn-status-inactive" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($currentSetting->status ?? 'inactive') == 'inactive' ? 'bg-zinc-800 text-white' : 'text-zinc-500 hover:text-white' }}">
                                        Inativo
                                    </button>
                                    <input type="hidden" name="status" id="input-status" value="{{ $currentSetting->status ?? 'inactive' }}">
                                </div>
                            </div>

                            <!-- Toggle: Environment -->
                            <div class="space-y-4">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-1">Ambiente</label>
                                <div class="flex items-center gap-4 bg-zinc-950/50 p-2 rounded-[1.5rem] border border-white/5 w-fit">
                                    <button type="button" onclick="setEnv('sandbox')" id="btn-env-sandbox" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($currentSetting->environment ?? 'sandbox') == 'sandbox' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">
                                        Sandbox
                                    </button>
                                    <button type="button" onclick="setEnv('production')" id="btn-env-production" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ ($currentSetting->environment ?? 'sandbox') == 'production' ? 'bg-orange-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">
                                        Produção
                                    </button>
                                    <input type="hidden" name="environment" id="input-env" value="{{ $currentSetting->environment ?? 'sandbox' }}">
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="space-y-6">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-1">Métodos Habilitados</label>
                                <div class="grid grid-cols-1 gap-4">
                                    @php 
                                        $methods = [
                                            'enable_credit_card' => ['Cartão de Crédito', 'Visa, MasterCard, Elo, Amex'],
                                            'enable_pix' => ['Pix', 'Liquidação Instantânea'],
                                            'enable_boleto' => ['Boleto Bancário', 'Compensação em até 48h']
                                        ];
                                    @endphp
                                    @foreach($methods as $key => $info)
                                    <label class="flex items-center justify-between p-5 bg-zinc-950/40 rounded-2xl border border-white/5 cursor-pointer hover:border-blue-500/20 transition-all group/method">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center">
                                                <input type="hidden" name="{{ $key }}" value="0">
                                                <input type="checkbox" name="{{ $key }}" value="1" {{ ($currentSetting->$key ?? false) ? 'checked' : '' }} class="w-5 h-5 accent-blue-600 cursor-pointer">
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-black text-white uppercase tracking-widest">{{ $info[0] }}</p>
                                                <p class="text-[9px] text-zinc-600 font-bold">{{ $info[1] }}</p>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Credentials -->
                        <div class="space-y-8">
                            <div class="space-y-3">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Public Key</label>
                                <input type="text" name="public_key" value="{{ $currentSetting->public_key ?? '' }}" placeholder="APP_USR-..." 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-[1.5rem] text-white text-xs font-mono outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-800">
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Access Token</label>
                                <input type="password" name="access_token" value="{{ $currentSetting->access_token ?? '' }}" placeholder="••••••••••••••••••••" 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-[1.5rem] text-white text-xs font-mono outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-800">
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Webhook Secret</label>
                                <input type="password" name="webhook_secret" value="{{ $currentSetting->webhook_secret ?? '' }}" placeholder="whsec_..." 
                                    class="w-full bg-zinc-950 border border-white/5 p-5 rounded-[1.5rem] text-white text-xs font-mono outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-800">
                            </div>

                            <div class="grid grid-cols-2 gap-6 pt-4">
                                <div class="space-y-3">
                                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Venc. Boleto (Dias)</label>
                                    <input type="number" name="boleto_expiration_days" value="{{ $currentSetting->boleto_expiration_days ?? 3 }}" 
                                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-[1.5rem] text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Exp. Pix (Minutos)</label>
                                    <input type="number" name="pix_expiration_minutes" value="{{ $currentSetting->pix_expiration_minutes ?? 30 }}" 
                                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-[1.5rem] text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row items-center gap-6">
                        <button type="submit" class="w-full md:flex-1 py-6 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.3em] rounded-[1.8rem] hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 active:scale-95">
                            Salvar Configurações
                        </button>
                        
                        <button type="button" onclick="testConnection('{{ $selectedGateway }}')" class="w-full md:w-auto px-10 py-6 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-[1.8rem] border border-white/5 hover:bg-zinc-700 transition-all flex items-center justify-center gap-3 group">
                            <span id="test-spinner" class="hidden w-3 h-3 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                            <span>Testar Conexão</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setStatus(status) {
        document.getElementById('input-status').value = status;
        
        const activeBtn = document.getElementById('btn-status-active');
        const inactiveBtn = document.getElementById('btn-status-inactive');
        
        if (status === 'active') {
            activeBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
            activeBtn.classList.remove('text-zinc-500', 'hover:text-white');
            inactiveBtn.classList.remove('bg-zinc-800', 'text-white');
            inactiveBtn.classList.add('text-zinc-500', 'hover:text-white');
        } else {
            inactiveBtn.classList.add('bg-zinc-800', 'text-white');
            inactiveBtn.classList.remove('text-zinc-500', 'hover:text-white');
            activeBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
            activeBtn.classList.add('text-zinc-500', 'hover:text-white');
        }
    }

    function setEnv(env) {
        document.getElementById('input-env').value = env;
        
        const sandboxBtn = document.getElementById('btn-env-sandbox');
        const productionBtn = document.getElementById('btn-env-production');
        
        if (env === 'sandbox') {
            sandboxBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            sandboxBtn.classList.remove('text-zinc-500', 'hover:text-white');
            productionBtn.classList.remove('bg-orange-600', 'text-white', 'shadow-lg');
            productionBtn.classList.add('text-zinc-500', 'hover:text-white');
        } else {
            productionBtn.classList.add('bg-orange-600', 'text-white', 'shadow-lg');
            productionBtn.classList.remove('text-zinc-500', 'hover:text-white');
            sandboxBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            sandboxBtn.classList.add('text-zinc-500', 'hover:text-white');
        }
    }

    function testConnection(gateway) {
        const spinner = document.getElementById('test-spinner');
        spinner.classList.remove('hidden');
        
        fetch('{{ route("admin.settings.payments.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ gateway: gateway })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao processar o teste.');
        })
        .finally(() => {
            spinner.classList.add('hidden');
        });
    }

    // Auto-hide success alert
    setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    .animate-bounce-in { animation: bounceIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    @keyframes bounceIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }

    .scale-102:hover { transform: scale(1.02); }
</style>
@endsection
