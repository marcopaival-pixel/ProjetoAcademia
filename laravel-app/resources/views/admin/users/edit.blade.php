@extends('layouts.admin')

@section('title', 'Editar Utilizador: ' . $user->name)

@section('content')
<div class="space-y-10 animate-fade-in max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Ficha do Utilizador <span class="text-blue-500">#{{ $user->id }}</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Gestão de acessos, perfil e conformidade LGPD.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.security.index') }}?user_id={{ $user->id }}" title="Segurança / Senhas" class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-amber-500 hover:bg-amber-600 hover:text-white transition-all shadow-xl">
                <i class="fas fa-key text-xs"></i>
            </a>
            <a href="{{ route('admin.users') }}" class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-white/5 hover:text-white transition-all shadow-xl">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Main Configuration Form -->
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 p-6 rounded-2xl mb-10">
            <div class="flex items-center gap-3 mb-4 text-red-500">
                <i class="fas fa-exclamation-triangle"></i>
                <h4 class="font-black text-xs uppercase tracking-widest">Erros de Validação</h4>
            </div>
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-xs text-red-400 font-medium">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl">

            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="space-y-2">
                    <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                </div>

                <div class="space-y-2">
                    <label for="cpf" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">CPF (Obrigatório)</label>
                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf) }}" required
                        x-mask="999.999.999-99"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                </div>

                <div class="space-y-2">
                    <label for="birth_date_main" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nascimento</label>
                    <input type="date" id="birth_date_main" name="birth_date" value="{{ old('birth_date', $user->profile?->birth_date?->format('Y-m-d')) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all [color-scheme:dark]">
                </div>

                <div class="space-y-2">
                    <label for="sex_main" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Sexo</label>
                    <select id="sex_main" name="sex" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                        <option value="">Selecione...</option>
                        <option value="M" {{ old('sex', $user->profile?->sex) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sex', $user->profile?->sex) == 'F' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>
            </div>

            <div class="p-8 bg-zinc-950/40 border border-white/5 rounded-[2rem] space-y-8">
                <h3 class="text-lg font-black text-white tracking-tight italic border-b border-white/5 pb-4">Nível de Acesso & Assinatura</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label for="plan_id" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Plano Atual</label>
                        <select id="plan_id" name="plan_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-1 px-1">O plano define o nível de funcionalidades liberadas.</p>
                    </div>

                     <!-- Admin Toggle -->
                     <label class="flex items-start gap-4 cursor-pointer group">
                         <div class="relative flex items-center justify-center mt-1">
                             <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="peer sr-only">
                             <div class="w-6 h-6 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-red-600 peer-checked:border-red-500 transition-colors flex items-center justify-center">
                                 <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                             </div>
                         </div>
                         <div>
                             <span class="block text-sm font-bold text-red-500 group-hover:text-red-400 transition-colors">Acesso Root (Administrador)</span>
                             <span class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest mt-1 block">Permite gerir toda a plataforma no painel admin.</span>
                         </div>
                     </label>

                     <!-- Verification Toggle -->
                     <label class="flex items-start gap-4 cursor-pointer group">
                         <div class="relative flex items-center justify-center mt-1">
                             <input type="checkbox" name="is_verified" value="1" {{ $user->email_verified_at ? 'checked' : '' }} class="peer sr-only">
                             <div class="w-6 h-6 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-emerald-600 peer-checked:border-emerald-500 transition-colors flex items-center justify-center">
                                 <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                             </div>
                         </div>
                         <div>
                             <span class="block text-sm font-bold text-emerald-500 group-hover:text-emerald-400 transition-colors">Vínculo de E-mail Confirmado</span>
                             <span class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest mt-1 block">Liberado para autenticação no sistema.</span>
                         </div>
                     </label>
                 </div>

                @if(!$user->email_verified_at && !$user->is_admin)
                <div class="pt-4 border-t border-white/5">
                    <button type="button" onclick="document.getElementById('resend-v-form').submit()" class="px-6 py-3 rounded-2xl bg-blue-600/20 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all">Reenviar confirmação de e-mail</button>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-white/5">
                    <div class="space-y-4">
                        <label for="profile_id" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Perfil de Acesso (Cargo)</label>
                        <select id="profile_id" name="profile_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}" {{ $user->profile_id == $profile->id ? 'selected' : '' }} {{ $user->profile_id == 5 && $profile->id != 5 ? 'disabled' : '' }}>{{ $profile->label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tight mt-1 px-1">O perfil define o tipo de utilizador no sistema.</p>
                        @if($user->profile_id == 5)
                            <p class="text-[9px] text-amber-500 font-bold uppercase tracking-tight mt-1 px-1">Nota: Perfil Aluno é permanente e não pode ser alterado.</p>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <label for="status" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status da Conta</label>
                        <div class="flex items-center gap-4 mt-1">
                            <label class="flex-1 flex items-center gap-3 p-4 bg-zinc-950 border {{ $user->status === 'active' ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-white/5' }} rounded-2xl cursor-pointer group hover:border-emerald-500/30 transition-all">
                                <input type="radio" name="status" value="active" {{ $user->status === 'active' ? 'checked' : '' }} class="hidden peer">
                                <div class="w-5 h-5 rounded-full border-2 border-white/10 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center transition-all">
                                    <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100"></i>
                                </div>
                                <span class="text-sm font-bold {{ $user->status === 'active' ? 'text-emerald-400' : 'text-zinc-500' }}">Ativo</span>
                            </label>
                            <label class="flex-1 flex items-center gap-3 p-4 bg-zinc-950 border {{ $user->status === 'blocked' ? 'border-red-500/30 bg-red-500/5' : 'border-white/5' }} rounded-2xl cursor-pointer group hover:border-red-500/30 transition-all">
                                <input type="radio" name="status" value="blocked" {{ $user->status === 'blocked' ? 'checked' : '' }} class="hidden peer">
                                <div class="w-5 h-5 rounded-full border-2 border-white/10 peer-checked:bg-red-500 peer-checked:border-red-500 flex items-center justify-center transition-all">
                                    <i class="fas fa-ban text-white text-[8px] opacity-0 peer-checked:opacity-100"></i>
                                </div>
                                <span class="text-sm font-bold {{ $user->status === 'blocked' ? 'text-red-400' : 'text-zinc-500' }}">Bloqueado</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-white/5">
                    <div class="space-y-2">
                        <label for="department" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Vínculo Departamental</label>
                        <select id="department" name="department" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                            <option value="">Nenhum</option>
                            <option value="support" {{ $user->department === 'support' ? 'selected' : '' }}>Suporte</option>
                            <option value="finance" {{ $user->department === 'finance' ? 'selected' : '' }}>Financeiro</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="premium_expires_at" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Expiração do Premium</label>
                        <input type="date" id="premium_expires_at" name="premium_expires_at" value="{{ $user->premium_expires_at ? $user->premium_expires_at->format('Y-m-d') : '' }}"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700">
                    </div>
                </div>

                <!-- SELECÇÃO ALUNO / BIOMETRIA -->
                <div id="student_profile_section" class="space-y-10 animate-fade-in border-t border-white/5 pt-10 mt-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight italic">Fisiometria e Perfil</h3>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Dados antropométricos e biológicos do aluno.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="height_cm" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Altura (cm)</label>
                            <input type="number" id="height_cm" name="height_cm" value="{{ old('height_cm', $user->profile?->height_cm) }}" step="0.1"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all" placeholder="Ex: 175">
                        </div>

                        <div class="space-y-2">
                            <label for="weight_kg" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Peso (kg)</label>
                            <input type="number" id="weight_kg" name="weight_kg" value="{{ old('weight_kg', $latestWeight?->weight_kg) }}" step="0.1"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all" placeholder="Ex: 75.5">
                        </div>
                    </div>
                </div>

                <!-- SELECÇÃO PROFISSIONAL -->
                <div id="professional_section" class="hidden space-y-10 animate-fade-in border-t border-white/5 pt-10 mt-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-500/20">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white tracking-tight italic">Dados Profissionais</h3>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Informações de conselho e especialidade.</p>
                            </div>
                        </div>
                        @if($user->professionalProfile?->daysUntilExpiry() !== null)
                            @php $days = $user->professionalProfile->daysUntilExpiry(); @endphp
                            <div class="px-4 py-2 rounded-xl {{ $days <= 30 ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' }} text-[10px] font-black uppercase">
                                {{ $user->professionalProfile->expiry_warning ?? 'Validade em dia' }}
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="profession_id" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Profissão</label>
                            <select id="profession_id" name="profession_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="">Selecione...</option>
                                @foreach($professions as $prof)
                                    <option value="{{ $prof->id }}" {{ (old('profession_id', $user->professionalProfile?->profession_id) == $prof->id) ? 'selected' : '' }}>
                                        {{ $prof->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="specialty" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Especialidade</label>
                            <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $user->professionalProfile?->specialty) }}"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all placeholder:text-zinc-700"
                                placeholder="Ex: Nutrição Esportiva">
                        </div>

                        <div class="grid grid-cols-3 gap-4 md:col-span-2">
                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Conselho</label>
                                <select name="council" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                                    @foreach(['CRM', 'CREF', 'CRN', 'CREFITO', 'CRP', 'COREN'] as $council)
                                        <option value="{{ $council }}" {{ (old('council', $user->professionalProfile?->council) == $council) ? 'selected' : '' }}>{{ $council }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nº Registro</label>
                                <input type="text" name="registration_number" value="{{ old('registration_number', $user->professionalProfile?->registration_number) }}"
                                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">UF</label>
                                <select name="registration_uf" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                        <option value="{{ $uf }}" {{ (old('registration_uf', $user->professionalProfile?->registration_uf) == $uf) ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="registration_expiry_date" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Validade do Registro</label>
                            <input type="date" id="registration_expiry_date" name="registration_expiry_date" value="{{ $user->professionalProfile?->registration_expiry_date ? $user->professionalProfile->registration_expiry_date->format('Y-m-d') : '' }}"
                                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Documento do Conselho (PDF/JPG)</label>
                            <div class="flex items-center gap-4">
                                <input type="file" name="document_file" class="flex-1 bg-zinc-950 border border-white/5 p-3 rounded-2xl text-xs text-zinc-500 file:bg-blue-600/20 file:border-0 file:text-blue-400 file:rounded-xl file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase">
                                @if($user->professionalProfile?->document_path)
                                    <a href="{{ asset('storage/' . $user->professionalProfile->document_path) }}" target="_blank" class="px-4 py-3 bg-zinc-900 border border-white/5 rounded-2xl text-blue-500 hover:text-white hover:bg-blue-600 transition-all">
                                        <i class="fas fa-file-download mr-1"></i> Ver v{{ $user->professionalProfile->document_version }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ASSINATURA DIGITAL -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Assinatura Digital</span>
                            <div class="h-[1px] flex-1 bg-white/5"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-6 bg-zinc-950 border border-white/5 rounded-3xl space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase text-zinc-500">Desenhar nova</span>
                                    <button type="button" id="clear_signature" class="text-[8px] font-black uppercase text-red-500 border border-red-500/20 px-2 py-1 rounded-lg hover:bg-red-500 hover:text-white transition-all">Limpar</button>
                                </div>
                                <canvas id="signature_pad" class="w-full h-32 bg-zinc-900 border border-white/5 rounded-2xl cursor-crosshair"></canvas>
                                <input type="hidden" name="signature_data" id="signature_data">
                            </div>

                            <div class="p-6 bg-zinc-950 border border-white/5 rounded-3xl space-y-4 flex flex-col justify-center text-center">
                                @if($user->professionalProfile?->signature_path)
                                    <span class="text-[10px] font-black uppercase text-emerald-500 mb-2 italic">Assinatura Atual</span>
                                    <div class="bg-white p-2 rounded-xl mb-4 w-32 h-16 mx-auto flex items-center justify-center">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($user->professionalProfile->signature_path, 'data:') ? $user->professionalProfile->signature_path : asset('storage/' . $user->professionalProfile->signature_path) }}" class="max-w-full max-h-full" alt="Assinatura">
                                    </div>
                                @endif
                                <label class="text-[10px] font-black uppercase text-zinc-500 block mb-2">Upload nova (PNG)</label>
                                <input type="file" name="signature_file" accept="image/png" class="bg-zinc-900 border border-white/5 p-3 rounded-2xl text-[10px] text-zinc-500 file:bg-zinc-800 file:border-0 file:text-zinc-400 file:rounded-xl file:px-4 file:py-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
    </div>

        <!-- Permissões do Perfil -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] shadow-2xl mt-10" x-data="{ tab: 'role' }">
            <div class="flex items-center gap-8 mb-10 pb-6 border-b border-white/5">
                <button type="button" @click="tab = 'role'" :class="tab === 'role' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-zinc-500'" class="pb-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all">Regras do Perfil (Global)</button>
                <button type="button" @click="tab = 'direct'" :class="tab === 'direct' ? 'text-emerald-500 border-b-2 border-emerald-500' : 'text-zinc-500'" class="pb-2 text-[10px] font-black uppercase tracking-[0.2em] transition-all">Exceções Individuais (Privado)</button>
            </div>

            <!-- Role Permissions (Global) -->
            <div x-show="tab === 'role'" class="animate-fade-in">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-500/20">
                        <i class="fas fa-users-cog text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Permissões do Perfil: {{ $user->userProfile?->label }}</h3>
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Configure o que este perfil pode aceder no sistema.</p>
                    </div>
                    <div class="ml-auto">
                        <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest leading-none">Atenção: Alterações afetam TODOS com este perfil.</span>
                        </div>
                    </div>
                </div>

                @php
                    $permissionGroups = [
                        'Sistema' => $allPermissions->filter(fn($p) => str_contains($p->name, 'admin') || str_contains($p->name, 'portal')),
                        'Usuários' => $allPermissions->filter(fn($p) => str_contains($p->name, 'users')),
                        'Documentos PDF' => $allPermissions->filter(fn($p) => str_contains($p->name, 'pdf')),
                        'Operacional' => $allPermissions->filter(fn($p) => str_contains($p->name, 'training') || str_contains($p->name, 'reception')),
                        'Financeiro / Suporte' => $allPermissions->filter(fn($p) => str_contains($p->name, 'finance') || str_contains($p->name, 'support')),
                    ];
                    
                    $userProfilePermissionIds = $user->userProfile ? $user->userProfile->permissions->pluck('id')->toArray() : [];
                    $userDirectPermissionIds = $user->permissions->pluck('id')->toArray();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach($permissionGroups as $groupName => $groupPermissions)
                        @if($groupPermissions->count() > 0)
                        <div class="space-y-6">
                            <h4 class="text-xs font-black text-blue-500 uppercase tracking-widest border-l-4 border-blue-600 pl-3 leading-none">{{ $groupName }}</h4>
                            
                            <div class="space-y-3">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-start gap-3 cursor-pointer group p-3 bg-zinc-950/40 border border-white/5 rounded-2xl hover:border-blue-500/30 transition-all">
                                        <div class="relative flex items-center justify-center mt-1">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                {{ in_array($permission->id, $userProfilePermissionIds) ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <div class="w-5 h-5 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-colors flex items-center justify-center">
                                                <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-zinc-300 group-hover:text-white transition-colors block">{{ $permission->label }}</span>
                                            <span class="text-[9px] text-zinc-600 font-medium uppercase tracking-tight block mt-0.5">{{ $permission->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Direct Permissions (Individual) -->
            <div x-show="tab === 'direct'" class="animate-fade-in" x-cloak>
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Exceções para {{ $user->name }}</h3>
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Permissões atribuídas EXCLUSIVAMENTE a este utilizador.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach($permissionGroups as $groupName => $groupPermissions)
                        @if($groupPermissions->count() > 0)
                        <div class="space-y-6">
                            <h4 class="text-xs font-black text-emerald-500 uppercase tracking-widest border-l-4 border-emerald-600 pl-3 leading-none">{{ $groupName }}</h4>
                            
                            <div class="space-y-3">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-start gap-3 cursor-pointer group p-3 bg-zinc-950/40 border border-white/5 rounded-2xl hover:border-emerald-500/30 transition-all">
                                        <div class="relative flex items-center justify-center mt-1">
                                            <input type="checkbox" name="direct_permissions[]" value="{{ $permission->id }}" 
                                                {{ in_array($permission->id, $userDirectPermissionIds) ? 'checked' : '' }}
                                                class="peer sr-only">
                                            <div class="w-5 h-5 rounded-lg bg-zinc-900 border border-white/10 peer-checked:bg-emerald-600 peer-checked:border-emerald-500 transition-colors flex items-center justify-center">
                                                <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-zinc-300 group-hover:text-white transition-colors block">{{ $permission->label }}</span>
                                            <span class="text-[9px] text-zinc-600 font-medium uppercase tracking-tight block mt-0.5">{{ $permission->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4 pt-10 border-t border-white/5 mt-10">
            <button type="submit" class="w-full md:w-auto flex-1 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10">
                Guardar Alterações do Perfil e Permissões
            </button>
            <a href="{{ route('admin.users') }}" class="w-full md:w-auto py-5 px-8 bg-zinc-900/50 border border-white/5 text-zinc-400 font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:text-white hover:bg-white/5 transition-all text-center">
                Cancelar
            </a>
        </div>
    </form>


    <!-- Additional Information & LGPD -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Account Info -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-xl">
            <h3 class="text-lg font-black text-white tracking-tight italic mb-6">Informações de Conta</h3>
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4 bg-zinc-950/50 p-4 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Membro desde</span>
                        <span class="text-sm font-bold text-white mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 bg-zinc-950/50 p-4 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600/10 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Última Atualização</span>
                        <span class="text-sm font-bold text-white mt-1">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Nunca' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LGPD & Compliance -->
        <div class="bg-gradient-to-br from-indigo-900/20 to-blue-900/10 backdrop-blur-3xl border border-blue-500/20 p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
            
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-lg font-black text-white tracking-tight italic">LGPD & Conformidade</h3>
                </div>
                
                <p class="text-zinc-400 text-xs leading-relaxed mb-8 flex-grow">
                    Direito à Portabilidade: É possível exportar todos os dados deste utilizador, incluindo configurações, em formato estruturado JSON de acordo com a legislação.
                </p>
                
                <a href="{{ route('admin.lgpd.export-user', $user->id) }}" class="flex items-center justify-between w-full p-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/20">
                    <span>Exportar Todos os Dados (JSON)</span>
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
    </div>

    @if((auth()->user()->isAdministrator() || auth()->user()->hasPermission('users.delete')) && $user->userProfile?->name === 'aluno' && $user->id !== auth()->id() && ! $user->is_admin)
        <div class="bg-red-950/30 backdrop-blur-xl border border-red-500/25 p-8 rounded-[2.5rem] shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h3 class="text-lg font-black text-red-400 tracking-tight italic">Zona de exclusão</h3>
                    <p class="text-zinc-500 text-xs mt-2 max-w-xl">Remove permanentemente este aluno da base de dados. Esta operação não pode ser desfeita.</p>
                </div>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                    data-confirm-delete
                    data-confirm-title="Excluir aluno"
                    data-confirm-message="Confirma a exclusão permanente deste aluno da base de dados? Esta operação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-8 py-4 bg-red-600/20 text-red-400 border border-red-500/40 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-red-600 hover:text-white transition-all whitespace-nowrap">
                        Excluir aluno da base
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<form id="resend-v-form" action="{{ route('admin.users.resend-verification', $user) }}" method="POST" class="hidden">
    @csrf
</form>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const roleSelect = document.getElementById('profile_id');
                    const profSection = document.getElementById('professional_section');
                    const studentSection = document.getElementById('student_profile_section');
                    const canvas = document.getElementById('signature_pad');
                    const signatureDataInput = document.getElementById('signature_data');
                    const clearBtn = document.getElementById('clear_signature');
                    const ctx = canvas?.getContext('2d');
                    
                    let drawing = false;

                    function checkRole() {
                        if (roleSelect.value == '4') { // Profile Profissional
                            profSection.classList.remove('hidden');
                            studentSection.classList.add('hidden');
                        } else {
                            profSection.classList.add('hidden');
                            studentSection.classList.remove('hidden');
                        }
                    }

                    roleSelect.addEventListener('change', checkRole);
                    checkRole();

                    if (canvas) {
                        function resizeCanvas() {
                            canvas.width = canvas.offsetWidth;
                            canvas.height = canvas.offsetHeight;
                        }
                        resizeCanvas();
                        window.addEventListener('resize', resizeCanvas);

                        function startDrawing(e) {
                            drawing = true;
                            draw(e);
                        }

                        function endDrawing() {
                            drawing = false;
                            ctx.beginPath();
                            signatureDataInput.value = canvas.toDataURL();
                        }

                        function draw(e) {
                            if (!drawing) return;
                            const rect = canvas.getBoundingClientRect();
                            const x = (e.clientX || (e.touches ? e.touches[0].clientX : 0)) - rect.left;
                            const y = (e.clientY || (e.touches ? e.touches[0].clientY : 0)) - rect.top;

                            ctx.lineWidth = 2;
                            ctx.lineCap = 'round';
                            ctx.strokeStyle = '#ffffff';

                            ctx.lineTo(x, y);
                            ctx.stroke();
                            ctx.beginPath();
                            ctx.moveTo(x, y);
                        }

                        canvas.addEventListener('mousedown', startDrawing);
                        canvas.addEventListener('mouseup', endDrawing);
                        canvas.addEventListener('mousemove', draw);
                        
                        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDrawing(e); });
                        canvas.addEventListener('touchend', (e) => { e.preventDefault(); endDrawing(); });
                        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); });

                        clearBtn.addEventListener('click', () => {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            signatureDataInput.value = '';
                        });
                    }
                });
            </script>
@endsection
