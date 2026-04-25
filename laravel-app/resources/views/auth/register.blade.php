@extends('layouts.app')

@section('title', 'Criar Conta — NexShape')

@section('content')
<div class="min-h-screen overflow-y-auto overflow-x-hidden px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12 relative animate-fade-in" 
     x-data="{ 
        step: 0, 
        tipo_acesso: '{{ old('tipo_acesso', '') }}',
        professions: @js(\App\Models\Profession::all())
     }"
     x-init="if(tipo_acesso) step = 1">
    
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-2xl w-full mx-auto space-y-8 relative z-10 transition-all duration-500">
        
        <!-- STEP 0: SELEÇÃO DE TIPO -->
        <div x-show="step === 0" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             class="space-y-8">
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                    <i class="fas fa-users text-3xl text-indigo-500"></i>
                </div>
                <h2 class="text-3xl font-black text-white tracking-tight">Selecione o tipo de acesso</h2>
                <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest">Como você pretende utilizar a NexShape?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Aluno -->
                <button @click="tipo_acesso = 'aluno'; step = 1" class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] text-left hover:border-indigo-500/50 transition-all hover:scale-[1.02] shadow-2xl">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors">
                        <i class="fas fa-user-graduate text-indigo-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Sou Aluno</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium group-hover:text-zinc-400 transition-colors italic">Treinar, acompanhar minha evolução, registrar minha saúde e receber orientações de profissionais.</p>
                </button>

                <!-- Card Profissional -->
                <button @click="tipo_acesso = 'professional'; step = 1" class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] text-left hover:border-emerald-500/50 transition-all hover:scale-[1.02] shadow-2xl">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600/10 flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition-colors">
                        <i class="fas fa-user-tie text-emerald-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Sou Profissional</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium group-hover:text-zinc-400 transition-colors italic">Gerenciar alunos ou pacientes, acompanhar evolução e prescrever treinos, dietas e cuidados de saúde.</p>
                </button>
            </div>

            <div class="text-center mt-8">
                <p class="text-[11px] text-zinc-400 font-bold uppercase tracking-widest">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="text-indigo-500 hover:text-indigo-400 ml-1 transition-colors">Fazer login &rarr;</a>
                </p>
            </div>
        </div>

        <!-- STEP 1: FORMULÁRIO COMPLETO -->
        <div x-show="step === 1" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             id="register-step">
            
            <div class="text-center mb-8">
                <button @click="step = 0" class="text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-indigo-400 transition-colors mb-4 block mx-auto flex items-center gap-2">
                    <i class="fas fa-chevron-left"></i> Voltar para seleção
                </button>
                <h2 class="text-3xl font-black text-white tracking-tight">Criar sua conta</h2>
                <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest">
                    Perfil: <span class="text-indigo-500" x-text="tipo_acesso === 'aluno' ? 'Aluno' : 'Profissional'"></span>
                </p>
            </div>

            <div id="register-errors" class="hidden p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold mb-4"></div>

            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl relative">
                <!-- Decorative element -->
                <div class="absolute -top-6 -right-6 w-20 h-20 bg-indigo-600/5 rounded-full blur-2xl pointer-events-none"></div>

                <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-6" novalidate>
                    @csrf
                    <input type="hidden" name="tipo_acesso" x-model="tipo_acesso">

                    <!-- Campos Compartilhados -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="space-y-2">
                            <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                            <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-800"
                                placeholder="Seu nome completo">
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail</label>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-800"
                                placeholder="exemplo@email.com">
                        </div>

                        <div class="space-y-2">
                            <label for="cpf" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">CPF (11 dígitos)</label>
                            <input id="cpf" name="cpf" type="text" inputmode="numeric" required maxlength="14" value="{{ old('cpf') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-800"
                                placeholder="000.000.000-00">
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Telefone (Opcional)</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all"
                                placeholder="(00) 00000-0000">
                        </div>

                        <div class="space-y-2">
                            <label for="birth_date" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Data de Nascimento</label>
                            <input id="birth_date" name="birth_date" type="date" required value="{{ old('birth_date') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                        </div>

                        <div class="space-y-2">
                            <label for="sex" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Sexo</label>
                            <select id="sex" name="sex" required
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="" class="bg-zinc-900">Selecione...</option>
                                <option value="M" class="bg-zinc-900" @selected(old('sex') === 'M')>Masculino</option>
                                <option value="F" class="bg-zinc-900" @selected(old('sex') === 'F')>Feminino</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campos Específicos do Profissional -->
                    <div x-show="tipo_acesso === 'professional'" 
                         x-collapse
                         class="space-y-5 pt-6 border-t border-white/5 mt-2 bg-emerald-500/[0.02] -mx-10 px-10 pb-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-emerald-500 font-black uppercase tracking-widest ml-1">Tipo de Profissional *</label>
                            <select name="profession_id" :required="tipo_acesso === 'professional'"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all cursor-pointer">
                                <option value="" class="bg-zinc-900">Selecione sua profissão...</option>
                                <template x-for="p in professions" :key="p.id">
                                    <option :value="p.id" x-text="p.name" class="bg-zinc-900" :selected="p.id == {{ old('profession_id', 0) }}"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nº Registro Pro (Opcional)</label>
                                <input name="registration_number" type="text" value="{{ old('registration_number') }}"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none placeholder:text-zinc-800"
                                    placeholder="Ex: CREF 000000">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Especialidade</label>
                                <input name="specialty" type="text" value="{{ old('specialty') }}"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none placeholder:text-zinc-800"
                                    placeholder="Ex: Musculação">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da empresa ou clínica (Opcional)</label>
                            <input name="company_name" type="text" value="{{ old('company_name') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none placeholder:text-zinc-800"
                                placeholder="Onde você atua?">
                        </div>
                    </div>

                    <!-- Senhas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <div class="space-y-2">
                            <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha (Mín. 8 caracteres)</label>
                            <div class="relative group">
                                <input id="password" name="password" type="password" required minlength="8"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 pr-12 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder:text-zinc-800"
                                    placeholder="••••••••••••">
                                <button type="button" onclick="toggleRegisterPass('password', 'registerEyePassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-500 px-2">
                                    <i class="fas fa-eye" id="registerEyePassword"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar Senha</label>
                            <div class="relative group">
                                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 pr-12 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder:text-zinc-800"
                                    placeholder="••••••••••••">
                                <button type="button" onclick="toggleRegisterPass('password_confirmation', 'registerEyePasswordConfirm')" class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-500 px-2">
                                    <i class="fas fa-eye" id="registerEyePasswordConfirm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Termos -->
                    <label class="flex items-start gap-4 cursor-pointer group pt-2">
                        <input type="checkbox" id="terms" name="terms" value="1" required class="peer sr-only">
                        <div class="w-6 h-6 mt-0.5 rounded-lg bg-zinc-950 border border-white/10 peer-checked:bg-indigo-600 peer-checked:border-indigo-500 transition-all flex items-center justify-center shadow-inner">
                            <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-[11px] text-zinc-400 leading-relaxed pt-0.5">
                            Li e aceito os <a href="{{ route('legal.terms') }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 font-bold transition-all underline underline-offset-4">Termos de Uso</a> e a
                            <a href="{{ route('legal.privacy') }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 font-bold transition-all underline underline-offset-4">Política de Privacidade (LGPD)</a>.
                        </span>
                    </label>

                    <button type="submit" id="register-submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 disabled:pointer-events-none text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-indigo-600/20 uppercase tracking-[0.2em] text-[11px] mt-6">
                        Finalizar cadastro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Popup: e-mail ou CPF já cadastrados --}}
<div id="register-duplicate-modal" class="fixed inset-0 z-[500] hidden flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-hidden="true">
    <div data-reg-dup-backdrop class="absolute inset-0 bg-zinc-950/90 backdrop-blur-md"></div>
    <div class="relative w-full max-w-md rounded-[1.75rem] border border-amber-500/25 bg-zinc-900/95 shadow-2xl ring-1 ring-white/5 p-8 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-400 mb-4">
            <i class="fas fa-user-lock text-xl"></i>
        </div>
        <h3 class="text-lg font-black text-white tracking-tight mb-3">Cadastro existente</h3>
        <p id="register-duplicate-message" class="text-sm text-zinc-400 leading-relaxed mb-8"></p>
        <button type="button" id="register-duplicate-close" class="w-full py-4 rounded-2xl bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-widest border border-white/10 transition-all">
            Entendi
        </button>
    </div>
</div>

<script>
    const REGISTER_REDIRECT_FALLBACK = @json(route('registration.pending'));

    function maskRegisterCpf(input) {
        if (!input) return;
        let v = input.value.replace(/\D/g, '').slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = v;
    }

    (function () {
        const cpfEl = document.getElementById('cpf');
        if (cpfEl) {
            cpfEl.addEventListener('input', function () { maskRegisterCpf(cpfEl); });
            cpfEl.addEventListener('blur', function () { maskRegisterCpf(cpfEl); });
        }
    })();

    function openRegisterDuplicateModal(kind) {
        const modal = document.getElementById('register-duplicate-modal');
        const msg = document.getElementById('register-duplicate-message');
        if (!modal || !msg) return;
        var text = '';
        if (kind === 'both') {
            text = 'O e-mail e o CPF informados já possuem cadastro neste sistema. Se já tem conta, utilize a opção «Fazer login».';
        } else if (kind === 'email') {
            text = 'O e-mail informado já possui cadastro. Utilize outro e-mail ou aceda a «Fazer login» se já tem conta.';
        } else if (kind === 'cpf') {
            text = 'O CPF informado já possui cadastro neste sistema. Se já tem conta, utilize «Fazer login».';
        } else {
            text = 'Os dados informados não puderam ser utilizados para um novo cadastro.';
        }
        msg.textContent = text;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeRegisterDuplicateModal() {
        const modal = document.getElementById('register-duplicate-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    (function () {
        const modal = document.getElementById('register-duplicate-modal');
        const closeBtn = document.getElementById('register-duplicate-close');
        const backdrop = modal ? modal.querySelector('[data-reg-dup-backdrop]') : null;
        if (closeBtn) closeBtn.addEventListener('click', closeRegisterDuplicateModal);
        if (backdrop) backdrop.addEventListener('click', closeRegisterDuplicateModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeRegisterDuplicateModal();
        });
    })();

    function toggleRegisterPass(fieldId, iconId) {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(iconId);
        if (!passwordInput || !eyeIcon) return;

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        // Se estiver oculto pelo Alpine, não validar
        requiredFields.forEach(field => {
            if (field.offsetParent === null) return;

            if (field.type === 'checkbox') {
                if (!field.checked) {
                    isValid = false;
                    field.parentElement?.querySelector('.rounded-lg')?.classList.add('border-red-500/50');
                } else {
                    field.parentElement?.querySelector('.rounded-lg')?.classList.remove('border-red-500/50');
                }
            } else {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500/50');
                } else {
                    field.classList.remove('border-red-500/50');
                }
            }
        });

        // Termos: input pode estar com sr-only (offsetParent nulo em alguns browsers)
        const terms = document.getElementById('terms');
        if (terms && terms.offsetParent === null) {
            if (!terms.checked) {
                isValid = false;
                terms.parentElement?.querySelector('.rounded-lg')?.classList.add('border-red-500/50');
            } else {
                terms.parentElement?.querySelector('.rounded-lg')?.classList.remove('border-red-500/50');
            }
        }

        if (!isValid) {
            showRegisterErrors(['Preencha todos os campos obrigatórios e aceite os termos para continuar.']);
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message: 'Preencha todos os campos obrigatórios para continuar.',
                    type: 'error'
                }
            }));
            return false;
        }
        return true;
    }

    function showRegisterErrors(messages) {
        const box = document.getElementById('register-errors');
        if (!box) return;
        if (!messages.length) {
            box.classList.add('hidden');
            box.textContent = '';
            return;
        }
        box.innerHTML = '<ul class="list-disc list-inside space-y-1">' + messages.map(m => '<li>' + m + '</li>').join('') + '</ul>';
        box.classList.remove('hidden');
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    (function () {
        const registerForm = document.getElementById('register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (!validateForm(this)) return;

                const submitBtn = document.getElementById('register-submit');
                showRegisterErrors([]);
                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                try {
                    const fd = new FormData(this);
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf || '',
                        },
                        credentials: 'same-origin',
                    });

                    const contentType = res.headers.get('content-type') || '';
                    const isJson = contentType.includes('application/json');

                    if (res.ok && isJson) {
                        const okData = await res.json().catch(() => ({}));
                        const next = (okData && typeof okData.redirect === 'string' && okData.redirect)
                            ? okData.redirect
                            : REGISTER_REDIRECT_FALLBACK;
                        window.location.assign(next);
                        return;
                    }

                    if (res.ok && !isJson) {
                        if (res.redirected && res.url) {
                            window.location.assign(res.url);
                            return;
                        }
                        showRegisterErrors(['Recarregue a página e tente novamente.']);
                        return;
                    }

                    let data = {};
                    if (isJson) {
                        try {
                            data = await res.json();
                        } catch (parseErr) {
                            data = {};
                        }
                    }

                    if (res.status === 422) {
                        if (data.duplicate_registration) {
                            openRegisterDuplicateModal(data.duplicate_registration);
                            return;
                        }
                        const msgs = [];
                        if (data.errors && typeof data.errors === 'object') {
                            Object.values(data.errors).forEach(arr => {
                                if (Array.isArray(arr)) msgs.push(...arr);
                            });
                        }
                        if (data.message) msgs.unshift(data.message);
                        showRegisterErrors(msgs.length ? [...new Set(msgs)] : ['Verifique os dados e tente novamente.']);
                        return;
                    }

                    if (res.status === 419) {
                        showRegisterErrors(['Sessão expirou. Recarregue a página.']);
                        window.location.reload();
                        return;
                    }

                    const serverMsg = typeof data.message === 'string' ? data.message.trim() : '';
                    showRegisterErrors([serverMsg || 'Não foi possível concluir o cadastro.']);
                } catch (err) {
                    showRegisterErrors(['Erro de rede. Verifique sua conexão.']);
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        }
    })();
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
    
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
    }
    
    input[type='date']::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>
@endsection
