@extends('layouts.admin')

@section('title', 'Cadastrar Paciente Individual')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <form action="{{ route('admin.registrations.paciente-profissional.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Sessão: Dados Pessoais -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-3xl -mr-32 -mt-32"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="relative group">
                    <div class="w-24 h-24 bg-zinc-800 rounded-2xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden group-hover:border-emerald-500/50 transition-all cursor-pointer">
                        <i class="fas fa-camera text-zinc-500 text-2xl group-hover:scale-110 transition-transform"></i>
                        <input type="file" name="avatar" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-xl">Foto do Paciente</h3>
                    <p class="text-zinc-500 text-sm">Imagem para o prontuário eletrônico.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="Nome do paciente">
                </div>
                
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">E-mail</label>
                    <input type="email" name="email" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="paciente@exemplo.com">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">CPF</label>
                    <input type="text" name="cpf" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="000.000.000-00">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">WhatsApp</label>
                    <input type="text" name="whatsapp" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all placeholder:text-zinc-700" placeholder="(00) 00000-0000">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Data de Nascimento</label>
                    <input type="date" name="birth_date" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Sexo</label>
                    <select name="sex" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all">
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                        <option value="O">Outro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sessão: Dados de Saúde -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-heartbeat text-emerald-500"></i> Parâmetros de Saúde
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Altura (cm)</label>
                    <input type="number" name="height_cm" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all" placeholder="Ex: 175">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Peso Atual (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all" placeholder="Ex: 70.5">
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo Sanguíneo</label>
                    <select name="blood_type" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all">
                        <option value="">Não informado</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2 md:col-span-3">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Alergias</label>
                    <textarea name="allergy_details" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all" placeholder="Liste alergias relevantes..."></textarea>
                </div>

                <div class="space-y-2 md:col-span-3">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Doenças Crônicas / Medicamentos</label>
                    <textarea name="medication_details" rows="2" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all" placeholder="Diabetes, Hipertensão, Uso contínuo de..."></textarea>
                </div>
            </div>
        </div>

        <!-- Sessão: Configuração de Atendimento -->
        <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-white font-bold text-lg mb-8 flex items-center gap-3">
                <i class="fas fa-user-md text-emerald-500"></i> Atendimento
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Profissional Responsável</label>
                    @if(auth()->user()->hasRole('professional'))
                        <input type="hidden" name="professional_id" value="{{ auth()->id() }}">
                        <div class="w-full bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-4 py-3 text-emerald-400 font-bold">
                            {{ auth()->user()->name }} (Você)
                        </div>
                    @else
                        <select name="professional_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all">
                            <!-- Popular com profissionais se for admin -->
                        </select>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">Tipo de Atendimento</label>
                    <select name="service_type" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-emerald-500/50 outline-none transition-all">
                        <option value="presencial">Presencial</option>
                        <option value="online">Online</option>
                        <option value="hibrido">Híbrido</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.registrations.index') }}" class="px-8 py-4 text-zinc-500 font-bold hover:text-zinc-300 transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-3">
                Registrar Paciente <i class="fas fa-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
