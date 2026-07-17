@extends('layouts.app')

@section('title', 'Configurações do Perfil Profissional')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Perfil Profissional</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Configurações Avançadas</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Meu <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Perfil</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Mantenha seus dados atualizados para que seus alunos tenham a melhor experiência e visibilidade profissional.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('professional.dashboard') }}" class="px-6 py-3 bg-zinc-900 text-zinc-300 font-bold rounded-xl hover:bg-zinc-800 transition-all border border-white/5 flex items-center gap-2">
                Voltar ao Painel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-3xl font-bold flex items-center gap-4 animate-bounce-subtle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-6 rounded-3xl font-bold space-y-2">
            <div class="flex items-center gap-4 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Atenção: Alguns campos precisam de correção</span>
            </div>
            <ul class="list-disc list-inside text-xs font-medium opacity-80 pl-10">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('professional.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            <!-- Coluna Esquerda: Dados Pessoais e Profissionais -->
            <div class="xl:col-span-8 space-y-10">
                
                <!-- Dados Pessoais -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    
                    <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-xs">1</span>
                        Dados Pessoais
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Nome Completo</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-zinc-950/50 border {{ $errors->has('name') ? 'border-rose-500/50' : 'border-white/5' }} rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                            @error('name') <p class="text-rose-500 text-[10px] font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">E-mail Profissional</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-zinc-950/50 border {{ $errors->has('email') ? 'border-rose-500/50' : 'border-white/5' }} rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                            @error('email') <p class="text-rose-500 text-[10px] font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">CPF</label>
                            <input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="000.000.000-00">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Telefone / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="(11) 99999-9999" oninput="maskPhone(this)">
                        </div>
                    </div>
                </div>

                <!-- Dados Profissionais -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-xs">2</span>
                        Dados Profissionais
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Nº Registro (CRM/CREF/etc)</label>
                            <input type="text" name="registration_number" value="{{ old('registration_number', $profile->registration_number) }}" class="w-full bg-zinc-950/50 border {{ $errors->has('registration_number') ? 'border-rose-500/50' : 'border-white/5' }} rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                            @error('registration_number') <p class="text-rose-500 text-[10px] font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Conselho</label>
                            <input type="text" name="council" value="{{ old('council', $profile->council) }}" class="w-full bg-zinc-950/50 border {{ $errors->has('council') ? 'border-rose-500/50' : 'border-white/5' }} rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all" placeholder="Ex: CRM, CREF">
                            @error('council') <p class="text-rose-500 text-[10px] font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">UF do Registro</label>
                            <select name="registration_uf" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                                @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                    <option value="{{ $uf }}" {{ old('registration_uf', $profile->registration_uf) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Tempo de Experiência (Anos)</label>
                            <input type="number" name="experience_years" value="{{ old('experience_years', $profile->experience_years) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Especialidade Principal</label>
                            <input type="text" name="specialty" value="{{ old('specialty', $profile->specialty) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Formação Acadêmica</label>
                            <textarea name="education" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">{{ old('education', $profile->education) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Perfil Público -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-xs">3</span>
                        Perfil Público
                    </h2>

                    <div class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Descrição Profissional (Bio)</label>
                            <textarea name="about" rows="4" class="w-full bg-zinc-950/50 border border-white/5 rounded-3xl p-6 text-white text-sm font-medium focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all" placeholder="Conte um pouco sobre sua trajetória e metodologia..."></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Serviços Oferecidos</label>
                            <textarea name="offered_services" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-3xl p-6 text-white text-sm font-medium focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all" placeholder="Ex: Musculação Personalizada, Dieta Esportiva, Reabilitação..."></textarea>
                        </div>

                        <div class="flex items-center gap-6 p-6 bg-zinc-950/30 rounded-3xl border border-white/5">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-zinc-800 border border-white/10 flex-shrink-0">
                                @if($profile->professional_photo_path)
                                    <img src="{{ Storage::url($profile->professional_photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-zinc-600">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2 px-2">Foto Profissional</label>
                                <input type="file" name="professional_photo" class="text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Atendimento e Local -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl">
                        <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-xs">4</span>
                            Atendimento
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Tipos de Atendimento</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach(['Presencial', 'Online', 'Domiciliar'] as $type)
                                        <label class="flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-950/50 border border-white/5 cursor-pointer hover:border-orange-500/30 transition-all">
                                            <input type="checkbox" name="service_types[]" value="{{ $type }}" {{ in_array($type, old('service_types', $profile->service_types ?? [])) ? 'checked' : '' }} class="rounded border-white/10 bg-zinc-900 text-orange-500">
                                            <span class="text-xs font-bold text-zinc-300">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Valor Consulta (R$)</label>
                                    <input type="number" step="0.01" name="consultation_price" value="{{ old('consultation_price', $profile->consultation_price) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-orange-500/50 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Duração (min)</label>
                                    <input type="number" name="appointment_duration" value="{{ old('appointment_duration', $profile->appointment_duration) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-orange-500/50 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Intervalo entre Consultas (min)</label>
                                <input type="number" name="appointment_interval" value="{{ old('appointment_interval', $profile->appointment_interval) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-orange-500/50 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl">
                        <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-pink-500 flex items-center justify-center text-xs">5</span>
                            Localização
                        </h2>

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Nome da Clínica / Empresa</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Endereço Completo</label>
                                <input type="text" name="clinic_address" value="{{ old('clinic_address', $profile->clinic_address) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-2 space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Cidade</label>
                                    <input type="text" name="clinic_city" value="{{ old('clinic_city', $profile->clinic_city) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Estado</label>
                                    <select name="clinic_state" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-pink-500/50 outline-none transition-all">
                                        @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                            <option value="{{ $uf }}" {{ old('clinic_state', $profile->clinic_state) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Agenda e Visibilidade -->
            <div class="xl:col-span-4 space-y-10">
                
                <!-- Agenda -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
                    <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center text-xs">6</span>
                        Agenda Base
                    </h2>

                    <div class="space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Dias de Atendimento</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'] as $day)
                                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl bg-zinc-950/50 border border-white/5 cursor-pointer hover:border-purple-500/30 transition-all">
                                        <input type="checkbox" name="work_days[]" value="{{ $day }}" {{ in_array($day, old('work_days', $profile->work_days ?? [])) ? 'checked' : '' }} class="rounded border-white/10 bg-zinc-900 text-purple-500">
                                        <span class="text-xs font-bold text-zinc-300">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Horário Inicial</label>
                                <input type="time" name="work_start_time" value="{{ old('work_start_time', $profile->work_start_time ? date('H:i', strtotime($profile->work_start_time)) : '08:00') }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-purple-500/50 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Horário Final</label>
                                <input type="time" name="work_end_time" value="{{ old('work_end_time', $profile->work_end_time ? date('H:i', strtotime($profile->work_end_time)) : '18:00') }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-purple-500/50 outline-none transition-all">
                            </div>
                        </div>

                        <div class="p-6 bg-purple-500/5 rounded-3xl border border-purple-500/20">
                            <p class="text-[10px] text-purple-400 font-bold uppercase tracking-tight leading-relaxed">
                                <svg class="w-4 h-4 inline-block mr-1 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Esta configuração define seus horários padrão para a geração automática de slots de agendamento no Portal do Aluno.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Visibilidade -->
                <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
                    <h2 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-rose-500 flex items-center justify-center text-xs">7</span>
                        Visibilidade
                    </h2>

                    <div class="space-y-8">
                        <div class="flex items-center justify-between p-6 bg-zinc-950/50 rounded-3xl border border-white/5">
                            <div>
                                <h3 class="text-sm font-black text-white">Perfil Visível para Alunos</h3>
                                <p class="text-[10px] text-zinc-500 font-bold">Permitir que alunos encontrem você na busca.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $profile->is_public) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-8 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-rose-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-3xl hover:from-blue-500 hover:to-indigo-500 transition-all shadow-2xl active:scale-[0.98] uppercase text-xs tracking-[0.2em]">
                                Salvar Configurações
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-dashboard-entry {
        animation: dashboard-entry 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 2s infinite ease-in-out;
    }
</style>
@endsection
