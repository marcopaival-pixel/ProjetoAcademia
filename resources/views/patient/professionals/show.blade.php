@extends('layouts.app')

@section('title', $professional->name . ' — Perfil Profissional')

@section('content')
<div class="py-12 animate-fade-in max-w-[1000px] mx-auto px-6 space-y-12">
    <!-- Breadcrumb & Back -->
    <a href="{{ route('patient.professionals.search') }}" class="inline-flex items-center gap-2 text-zinc-500 hover:text-white transition-colors group">
        <div class="w-8 h-8 bg-zinc-900 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 transition-all shadow-lg">
            <i class="fas fa-arrow-left text-[10px]"></i>
        </div>
        <span class="text-[10px] font-black uppercase tracking-widest">Voltar para busca</span>
    </a>

    <!-- Profile Header -->
    <div class="bg-zinc-900/40 backdrop-blur-2xl border border-white/5 rounded-[4rem] p-12 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8">
            <div class="px-6 py-3 bg-indigo-500/10 text-indigo-400 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-indigo-500/20 backdrop-blur-md">
                {{ $professional->professionalProfile?->profession?->name ?? 'Especialista' }}
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
            @if($professional->professionalProfile?->professional_photo_path)
                <img src="{{ Storage::url($professional->professionalProfile->professional_photo_path) }}" class="w-48 h-48 rounded-[3rem] object-cover shadow-2xl border-4 border-white/5">
            @elseif($professional->avatar)
                <img src="{{ asset('storage/' . $professional->avatar) }}" class="w-48 h-48 rounded-[3rem] object-cover shadow-2xl border-4 border-white/5">
            @else
                <div class="w-48 h-48 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-[3rem] flex items-center justify-center text-white font-black text-6xl shadow-2xl">
                    {{ strtoupper(mb_substr($professional->name, 0, 1)) }}
                </div>
            @endif

            <div class="text-center md:text-left space-y-4">
                <h1 class="text-4xl font-black text-white italic tracking-tighter">{{ $professional->name }}</h1>
                <p class="text-zinc-500 font-bold text-lg max-w-xl">
                    {{ $professional->professionalProfile?->specialty ?: 'Profissional dedicado a transformar vidas através da saúde e bem-estar.' }}
                </p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                        <i class="fas fa-map-marker-alt text-indigo-500 text-xs"></i>
                        <span class="text-zinc-400 text-xs font-bold">{{ $professional->professionalProfile?->clinic_city ?: ($professional->profile?->city ?: 'Atendimento Remoto') }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                        <i class="fas fa-id-card text-indigo-500 text-xs"></i>
                        <span class="text-zinc-400 text-xs font-bold">{{ $professional->professionalProfile?->registration_number }} ({{ $professional->professionalProfile?->council }})</span>
                    </div>
                    @if($professional->professionalProfile?->experience_years)
                        <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                            <i class="fas fa-star text-indigo-500 text-xs"></i>
                            <span class="text-zinc-400 text-xs font-bold">{{ $professional->professionalProfile->experience_years }} anos de experiência</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- About & Info -->
        <div class="lg:col-span-2 space-y-12">
            <section class="space-y-6">
                <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Sobre o Profissional</h2>
                <div class="bg-zinc-900/20 border border-white/5 p-8 rounded-[2.5rem] prose prose-invert max-w-none">
                    <p class="text-zinc-400 leading-relaxed text-lg">
                        {{ $professional->professionalProfile?->about ?: 'Este profissional ainda não preencheu sua biografia. Entre em contato para saber mais sobre seus métodos e abordagens.' }}
                    </p>
                    @if($professional->professionalProfile?->education)
                        <div class="mt-6 pt-6 border-t border-white/5">
                            <h4 class="text-white font-black text-sm mb-3">Formação Acadêmica</h4>
                            <p class="text-zinc-500 text-sm italic">{{ $professional->professionalProfile->education }}</p>
                        </div>
                    @endif
                </div>
            </section>

            @if($professional->professionalProfile?->offered_services)
                <section class="space-y-6">
                    <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Serviços & Especialidades</h2>
                    <div class="bg-zinc-900/20 border border-white/5 p-8 rounded-[2.5rem]">
                        <p class="text-zinc-400 leading-relaxed">
                            {{ $professional->professionalProfile->offered_services }}
                        </p>
                    </div>
                </section>
            @endif

            <section class="space-y-6">
                <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Modalidades de Atendimento</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @php $types = $professional->professionalProfile?->service_types ?? ['Online']; @endphp
                    @foreach(['Online' => 'fas fa-video', 'Presencial' => 'fas fa-user-friends', 'Domiciliar' => 'fas fa-home'] as $key => $icon)
                        <div class="p-6 rounded-[2rem] border {{ in_array($key, $types) ? 'bg-indigo-600/10 border-indigo-500/20' : 'bg-zinc-900/20 border-white/5 opacity-50' }} flex flex-col items-center gap-4 text-center">
                            <div class="w-12 h-12 rounded-2xl {{ in_array($key, $types) ? 'bg-indigo-600 text-white' : 'bg-zinc-800 text-zinc-600' }} flex items-center justify-center shadow-lg">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <div>
                                <p class="text-white font-black text-sm">{{ $key }}</p>
                                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">{{ in_array($key, $types) ? 'Disponível' : 'Indisponível' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if($professional->professionalProfile?->company_name)
                <section class="space-y-6">
                    <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Local de Atendimento</h2>
                    <div class="bg-zinc-900/20 border border-white/5 p-8 rounded-[2.5rem] flex items-start gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center flex-shrink-0 text-indigo-400">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-white font-black text-xl">{{ $professional->professionalProfile->company_name }}</h4>
                            <p class="text-zinc-400 font-medium">{{ $professional->professionalProfile->clinic_address }}</p>
                            <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest">{{ $professional->professionalProfile->clinic_city }} — {{ $professional->professionalProfile->clinic_state }}</p>
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <!-- Scheduling Form -->
        <div id="schedule" class="space-y-6">
            <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Agendar Atendimento</h2>
            <div class="bg-zinc-900/40 backdrop-blur-2xl border border-white/5 p-8 rounded-[3rem] shadow-2xl space-y-8 sticky top-8">
                <form id="formBooking" action="{{ route('patient.professionals.schedule', $professional->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="appointment_at" id="final_appointment_at">
                    
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">1. Escolha a Data</label>
                        <input type="date" id="booking_date" name="date" required min="{{ date('Y-m-d') }}"
                            class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:border-indigo-500/50 transition-all [color-scheme:dark]">
                    </div>

                    <div id="slots_container" class="space-y-4 hidden">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">2. Horários Disponíveis</label>
                        <div id="slots_grid" class="grid grid-cols-3 gap-2">
                            <!-- Slots will be injected here -->
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">3. Modalidade</label>
                        <select name="service_type" required class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:border-indigo-500/50 transition-all appearance-none cursor-pointer">
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">4. Observações (Opcional)</label>
                        <textarea name="notes" rows="4" placeholder="Algum detalhe importante para o profissional?"
                            class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold placeholder:text-zinc-700 focus:outline-none focus:border-indigo-500/50 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" id="btnSubmit" disabled class="w-full py-6 bg-zinc-800 text-zinc-500 font-black rounded-3xl transition-all uppercase tracking-[0.2em] text-xs cursor-not-allowed">
                        Selecione um horário
                    </button>
                </form>

                <p class="text-center text-zinc-600 text-[9px] font-bold uppercase tracking-widest leading-relaxed">
                    Ao confirmar, o profissional será notificado e um vínculo será criado automaticamente.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const bookingDate = document.getElementById('booking_date');
    const slotsContainer = document.getElementById('slots_container');
    const slotsGrid = document.getElementById('slots_grid');
    const btnSubmit = document.getElementById('btnSubmit');
    const finalAppointmentAt = document.getElementById('final_appointment_at');

    bookingDate.addEventListener('change', function() {
        const date = this.value;
        if (!date) return;

        slotsContainer.classList.remove('hidden');
        slotsGrid.innerHTML = '<div class="col-span-3 text-center py-4 text-zinc-500 text-[10px] font-bold uppercase">Carregando...</div>';
        
        fetch(`{{ route('patient.professionals.slots', $professional->id) }}?professional_id={{ $professional->id }}&date=${date}`)
            .then(response => response.json())
            .then(slots => {
                slotsGrid.innerHTML = '';
                if (slots.length === 0) {
                    slotsGrid.innerHTML = '<div class="col-span-3 text-center py-4 text-zinc-500 text-[10px] font-bold uppercase">Sem horários disponíveis</div>';
                    return;
                }

                slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `py-3 rounded-xl text-[10px] font-black uppercase transition-all ${slot.available ? 'bg-white/5 text-zinc-400 hover:bg-indigo-600 hover:text-white border border-white/5' : 'bg-zinc-900 text-zinc-700 border border-transparent cursor-not-allowed'}`;
                    btn.textContent = slot.time;
                    btn.disabled = !slot.available;
                    
                    if (slot.available) {
                        btn.onclick = () => {
                            document.querySelectorAll('#slots_grid button').forEach(b => b.classList.remove('bg-indigo-600', 'text-white'));
                            btn.classList.add('bg-indigo-600', 'text-white');
                            finalAppointmentAt.value = `${date} ${slot.time}`;
                            btnSubmit.disabled = false;
                            btnSubmit.classList.remove('bg-zinc-800', 'text-zinc-500', 'cursor-not-allowed');
                            btnSubmit.classList.add('bg-indigo-600', 'text-white', 'shadow-2xl', 'shadow-indigo-600/30');
                            btnSubmit.textContent = 'Confirmar Agendamento';
                        };
                    }
                    
                    slotsGrid.appendChild(btn);
                });
            });
    });
</script>
@endsection
