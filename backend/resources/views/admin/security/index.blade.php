@extends('layouts.admin')

@section('title', 'Segurança e Controle de Acessos')

@section('content')
<div class="space-y-10 animate-fade-in max-w-6xl mx-auto">
    <!-- Header Context -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Cofre de Segurança</h2>
            <p class="text-zinc-500 text-sm mt-1">Gestão de senhas e políticas de proteção de dados NexShape.</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
            <i class="fas fa-user-shield text-xl"></i>
        </div>
    </div>

    @if(session('error'))
        <div class="p-5 bg-red-500/10 border border-red-500/20 text-red-500 rounded-2xl flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-10 flex flex-wrap gap-4">
        <a href="{{ route('admin.roles.index') }}" class="flex-1 min-w-[200px] glass-card p-6 border-blue-500/10 hover:border-blue-500/30 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i data-lucide="users-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-widest">Controle de Acessos</h4>
                    <p class="text-[9px] text-zinc-500 uppercase font-bold mt-1">Gerir Perfis e Permissões</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.lgpd.index') }}" class="flex-1 min-w-[200px] glass-card p-6 border-emerald-500/10 hover:border-emerald-500/30 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i data-lucide="fingerprint" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-widest">Privacidade & LGPD</h4>
                    <p class="text-[9px] text-zinc-500 uppercase font-bold mt-1">Auditoria e Consentimentos</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- CARD: POLÍTICAS DE SENHA (DINÂMICO) -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/10 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Políticas de Segurança</h3>
            </div>

            <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-4">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Requisitos de Senha</label>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <span class="text-xs text-zinc-400 font-bold">Comprimento Mínimo</span>
                            <input type="number" name="password_min_length" value="{{ \App\Models\AdminSetting::get('password_min_length', '8') }}" 
                                class="w-16 bg-zinc-900 border border-white/10 rounded-xl p-2 text-white text-xs text-center outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <span class="text-xs text-zinc-400 font-bold">Exigir Letra Maiúscula</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="password_require_uppercase" value="false">
                                <input type="checkbox" name="password_require_uppercase" value="true" class="sr-only peer" {{ \App\Models\AdminSetting::isTrue('password_require_uppercase', true) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <span class="text-xs text-zinc-400 font-bold">Exigir Números</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="password_require_numeric" value="false">
                                <input type="checkbox" name="password_require_numeric" value="true" class="sr-only peer" {{ \App\Models\AdminSetting::isTrue('password_require_numeric', true) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <span class="text-xs text-zinc-400 font-bold">Exigir Símbolos (!@#$)</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="password_require_special" value="false">
                                <input type="checkbox" name="password_require_special" value="true" class="sr-only peer" {{ \App\Models\AdminSetting::isTrue('password_require_special', true) ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-amber-500/5 rounded-3xl border border-amber-500/10">
                    <p class="text-[9px] text-amber-500 font-black uppercase tracking-widest leading-relaxed">
                        <i class="fas fa-exclamation-circle mr-1"></i> Atenção: Alterar estas políticas afeta apenas novos registros e trocas de senha. Contas existentes permanecem ativas.
                    </p>
                </div>

                <button type="submit" class="w-full py-4 bg-zinc-950 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl border border-white/5 hover:bg-emerald-600 transition-all shadow-xl">
                    Atualizar Políticas
                </button>
            </form>
        </div>

        <!-- CARD: ALTERAR MINHA SENHA -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Alterar Minha Senha</h3>
            </div>

            <form action="{{ route('admin.security.change-password') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="current_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="new_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>
                </div>

                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                    <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i> A senha deve ter no mínimo 8 caracteres, 
                        contendo letra maiúscula, número e caractere especial.
                    </p>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                    Salvar Nova Senha
                </button>
            </form>
        </div>

        <!-- CARD: RESETAR SENHA DE USUÁRIO -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-600/10 flex items-center justify-center text-amber-500">
                    <i class="fas fa-user-lock"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Gestão de Senhas Externas</h3>
            </div>

            <div class="space-y-10">
                <!-- Ação 1: Enviar link via Email -->
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Opção 1: Enviar Link de Recuperação</h4>
                    <p class="text-xs text-zinc-600">O utilizador receberá um e-mail com instruções para redefinir a própria senha com segurança.</p>
                    
                    <form action="#" id="form-send-reset" method="POST" class="space-y-4">
                        @csrf
                        <div class="relative space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Buscar Utilizador</label>
                            <input type="text" id="user-search" 
                                class="w-full bg-zinc-950 border border-white/5 p-4 pl-12 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"
                                placeholder="Nome, E-mail ou ID do usuário...">
                            <input type="hidden" name="selected_user_id" id="selected-user-id-reset">
                            <div id="search-results" class="absolute left-0 right-0 top-full mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl z-50 max-h-60 overflow-y-auto hidden"></div>
                        </div>

                        <button type="submit" id="btn-submit-reset" class="w-full py-4 bg-zinc-950 border border-white/10 text-zinc-500 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-amber-600 hover:text-white transition-all opacity-30 cursor-not-allowed">
                            Enviar Redefinição de Senha
                        </button>
                    </form>
                </div>

                <div class="border-t border-white/5 pt-8 space-y-4">
                    <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Opção 2: Reset Forçado (Manual)</h4>
                    <p class="text-xs text-zinc-600">Altere a senha do utilizador imediatamente. Recomendado apenas para casos de suporte crítico.</p>
                    
                    <form action="#" id="form-force-reset" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="force_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                                <input type="password" id="force_password" name="new_password" required
                                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-red-600 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label for="force_password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar</label>
                                <input type="password" id="force_password_confirmation" name="new_password_confirmation" required
                                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-red-600 transition-all">
                            </div>
                        </div>
                        <input type="hidden" name="selected_user_id" id="selected-user-id-force">

                        <button type="submit" id="btn-submit-force" class="w-full py-4 bg-red-600/10 text-red-500 font-black text-[10px] uppercase tracking-[0.2em] border border-red-500/20 rounded-2xl hover:bg-red-600 hover:text-white transition-all opacity-30 cursor-not-allowed">
                            Forçar Troca de Senha
                        </button>
                    </form>
                </div>

                <div class="border-t border-white/5 pt-8 space-y-4">
                    <h4 class="text-xs font-black text-emerald-400 uppercase tracking-widest">Opção 3: Automação Total</h4>
                    <p class="text-xs text-zinc-600">O sistema gerará uma senha segura de 16 caracteres, atualizará a conta e enviará por e-mail automaticamente.</p>
                    
                    <form action="#" id="form-auto-reset" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="selected_user_id" id="selected-user-id-auto">

                        <button type="button" id="btn-submit-auto" 
                            onclick="handleAutoReset(this)"
                            class="w-full py-5 bg-emerald-600/10 text-emerald-500 font-black text-[10px] uppercase tracking-[0.2em] border border-emerald-500/20 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all opacity-30 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i>
                            🔐 Gerar Nova Senha e Enviar por E-mail
                        </button>
                    </form>
                </div>

                <div class="border-t border-white/5 pt-8 space-y-4">
                    <h4 class="text-xs font-black text-blue-400 uppercase tracking-widest">Opção 4: Status da Conta (Bloquear/Desbloquear)</h4>
                    <p class="text-xs text-zinc-600">Verifique se o acesso está ativo ou bloqueado e altere instantaneamente.</p>
                    
                    <form action="#" id="form-toggle-status" method="POST" class="space-y-4">
                        @csrf
                        <div id="status-display" class="hidden p-4 rounded-2xl border flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div id="status-icon" class="w-8 h-8 rounded-lg flex items-center justify-center"></div>
                                <span id="status-text" class="text-xs font-bold uppercase tracking-widest"></span>
                            </div>
                            <button type="submit" id="btn-submit-toggle" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg">
                                Alterar Status
                            </button>
                        </div>
                        <div id="status-placeholder" class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-600 text-[10px] font-black uppercase tracking-widest text-center italic">
                            Selecione um utilizador para ver o status
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('user-search');
        const searchResults = document.getElementById('search-results');
        const selectedUserIdReset = document.getElementById('selected-user-id-reset');
        const selectedUserIdForce = document.getElementById('selected-user-id-force');
        const selectedUserIdAuto = document.getElementById('selected-user-id-auto');
        const btnReset = document.getElementById('btn-submit-reset');
        const btnForce = document.getElementById('btn-submit-force');
        const btnAuto = document.getElementById('btn-submit-auto');
        const formReset = document.getElementById('form-send-reset');
        const formForce = document.getElementById('form-force-reset');
        const formAuto = document.getElementById('form-auto-reset');
        const statusDisplay = document.getElementById('status-display');
        const statusPlaceholder = document.getElementById('status-placeholder');
        const statusIcon = document.getElementById('status-icon');
        const statusText = document.getElementById('status-text');
        const btnToggle = document.getElementById('btn-submit-toggle');
        const formToggle = document.getElementById('form-toggle-status');

        let debounceTimer;
        let isUserSelected = false;

        let selectedUserName = '';

        function showToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { message: message, type: type } 
            }));
            
            // Visual feedback: Highlight the search bar
            userSearch.focus();
            userSearch.classList.add('ring-2', 'ring-red-500/50', 'border-red-500/50');
            setTimeout(() => {
                userSearch.classList.remove('ring-2', 'ring-red-500/50', 'border-red-500/50');
            }, 2000);
        }

        formReset.addEventListener('submit', function(e) {
            if (!isUserSelected) {
                e.preventDefault();
                showToast('⚠️ Primeiro, escreva o nome do utilizador no campo "Pesquisar utilizador..." no topo e clique no resultado para selecionar.', 'error');
                return;
            }
            e.preventDefault();
            window.openNxConfirmAction({
                title: 'Enviar Link de Redefinição',
                message: `
                    <div class="text-left space-y-4">
                        <p class="text-white font-bold">Deseja enviar instruções de redefinição para <span class="text-amber-500 underline">${selectedUserName}</span>?</p>
                        <p class="text-[11px] opacity-80">O utilizador receberá um e-mail oficial contendo um link seguro para definir uma nova senha. O link expira em 60 minutos.</p>
                    </div>
                `,
                icon: 'mail',
                type: 'warning',
                confirmLabel: 'Sim, Enviar E-mail',
                onConfirm: () => {
                    formReset.submit();
                }
            });
        });

        formForce.addEventListener('submit', function(e) {
            if (!isUserSelected) {
                e.preventDefault();
                showToast('⚠️ Primeiro, escreva o nome do utilizador no campo "Pesquisar utilizador..." no topo e clique no resultado para selecionar.', 'error');
                return;
            }
            e.preventDefault();
            window.openNxConfirmAction({
                title: 'Forçar Troca de Senha',
                message: `
                    <div class="text-left space-y-4">
                        <p class="text-white font-bold">Atenção: Você está prestes a invalidar a senha de <span class="text-red-500 underline">${selectedUserName}</span>.</p>
                        <p class="text-[11px] opacity-80">Esta ação irá forçar o utilizador a definir uma nova senha obrigatoriamente no próximo login para garantir a segurança da conta.</p>
                    </div>
                `,
                icon: 'lock',
                type: 'danger',
                confirmLabel: 'Sim, Forçar Troca',
                onConfirm: () => {
                    formForce.submit();
                }
            });
        });

        // Global function for Option 3
        window.handleAutoReset = function(btn) {
            if (!isUserSelected) {
                showToast('⚠️ Primeiro, escreva o nome do utilizador no campo "Pesquisar utilizador..." no topo e clique no resultado para selecionar.', 'error');
                return;
            }

            window.openNxConfirmAction({
                title: '🔐 Automação de Segurança',
                message: `
                    <div class="text-left space-y-4">
                        <p class="text-white font-bold">Você está prestes a realizar um reset total na conta de <span class="text-emerald-400 underline">${selectedUserName}</span>.</p>
                        <ul class="text-[11px] space-y-2 list-disc list-inside opacity-80">
                            <li>Gerar uma <span class="text-white">senha segura de 16 caracteres</span> automaticamente.</li>
                            <li>Atualizar a base de dados de forma <span class="text-white">instantânea</span>.</li>
                            <li>Enviar e-mail com as novas credenciais para o utilizador.</li>
                            <li>Forçar a troca de senha no <span class="text-white">primeiro acesso</span>.</li>
                        </ul>
                        <p class="text-[10px] text-amber-500 font-black uppercase tracking-wider bg-amber-500/10 p-2 rounded-lg border border-amber-500/20">Atenção: Esta ação é crítica e enviará notificações reais.</p>
                    </div>
                `,
                icon: 'shield-check',
                type: 'success',
                confirmLabel: 'Confirmar Automação',
                onConfirm: () => {
                    btn.closest('form').submit();
                }
            });
        };

        // Handle pre-selected user from Server-side (PHP)
        @if(isset($preSelectedUser))
            selectUser({
                id: {{ $preSelectedUser->id }},
                name: "{{ $preSelectedUser->name }}",
                email: "{{ $preSelectedUser->email }}"
            });
        @endif

        // Handle pre-selected user from URL (Legacy/Alternative)
        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedUserId = urlParams.get('user_id');
        if (preSelectedUserId && !isUserSelected) {
            fetch(`{{ route('admin.users') }}?search=${preSelectedUserId}&ajax=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.users && data.users.length > 0) {
                        const user = data.users.find(u => u.id == preSelectedUserId) || data.users[0];
                        selectUser(user);
                    }
                });
        }

        userSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;

            if (query.length < 3) {
                searchResults.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('admin.users') }}?search=${encodeURIComponent(query)}&ajax=1`)
                    .then(res => res.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (!data.users || data.users.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-zinc-500 text-xs italic">Nenhum utilizador encontrado.</div>';
                        } else {
                            data.users.forEach(user => {
                                const div = document.createElement('div');
                                div.className = 'p-3 hover:bg-white/5 cursor-pointer border-b border-white/5 transition-colors flex justify-between items-center';
                                div.innerHTML = `
                                    <div>
                                        <div class="text-white text-xs font-black">${user.name}</div>
                                        <div class="text-[10px] text-zinc-500">${user.email}</div>
                                    </div>
                                    <span class="text-[9px] bg-zinc-900 text-zinc-400 px-2 py-0.5 rounded uppercase font-black">Selecionar</span>
                                `;
                                div.onclick = () => selectUser(user);
                                searchResults.appendChild(div);
                            });
                        }
                        searchResults.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectUser(user) {
            isUserSelected = true;
            selectedUserName = user.name;
            userSearch.value = `${user.name} (${user.email})`;
            selectedUserIdReset.value = user.id;
            selectedUserIdForce.value = user.id;
            selectedUserIdAuto.value = user.id;
            searchResults.classList.add('hidden');
            
            // Update Status Display
            statusPlaceholder.classList.add('hidden');
            statusDisplay.classList.remove('hidden');
            
            // Lógica de "Totalmente Ativo" (Status Active + Email Verificado + Cadastro Aprovado)
            // Nota: email_verified no banco é boolean, registration_approval_status é string
            const isEmailVerified = user.email_verified == 1 || user.email_verified === true || user.email_verified_at !== null;
            const isApproved = user.registration_approval_status === 'approved';
            const isStatusActive = user.status === 'active';
            
            const isFullyActive = isStatusActive && isEmailVerified && isApproved;
            
            statusText.innerText = isFullyActive ? 'Conta Ativa / Liberada' : 'Acesso Bloqueado / Pendente';
            statusText.className = `text-xs font-black uppercase tracking-widest ${isFullyActive ? 'text-emerald-500' : 'text-red-500'}`;
            
            statusDisplay.className = `p-4 rounded-2xl border flex items-center justify-between ${isFullyActive ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-red-500/10 border-red-500/20'}`;
            
            statusIcon.className = `w-8 h-8 rounded-lg flex items-center justify-center ${isFullyActive ? 'bg-emerald-500/20 text-emerald-500' : 'bg-red-500/20 text-red-500'}`;
            statusIcon.innerHTML = `<i class="fas ${isFullyActive ? 'fa-user-check' : 'fa-user-slash'}"></i>`;
            
            btnToggle.innerText = isFullyActive ? 'Bloquear Acesso' : 'Liberar Acesso Total';
            btnToggle.className = `px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg ${isFullyActive ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500'}`;
            
            formToggle.action = `/admin/users/${user.id}/toggle-status`;

            
            // Enable Option 1

            btnReset.classList.remove('opacity-30', 'cursor-not-allowed', 'text-zinc-500');
            btnReset.classList.add('text-black', 'bg-amber-600');
            
            // Enable Option 2
            btnForce.classList.remove('opacity-30', 'cursor-not-allowed', 'text-red-500', 'bg-red-600/10');
            btnForce.classList.add('text-white', 'bg-red-600');

            // Enable Option 3
            btnAuto.classList.remove('opacity-30', 'cursor-not-allowed', 'text-emerald-500', 'bg-emerald-600/10');
            btnAuto.classList.add('text-white', 'bg-emerald-600');

            formReset.action = `/admin/users/${user.id}/send-reset-link`;
            formForce.action = `/admin/users/${user.id}/reset-password`;
            formAuto.action = `/admin/users/${user.id}/reset-and-email`;
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!userSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    });
</script>
@endpush

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
