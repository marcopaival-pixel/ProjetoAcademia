@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Cadastrar Novo Usuário
            </h1>
            <p class="text-gray-400 mt-2">Crie uma nova conta e atribua um perfil de acesso.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="group flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl transition-all duration-300">
            <i class="fas fa-arrow-left text-blue-400 group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-gray-300">Voltar à Lista</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @if(isset($selectedCompanyId))
                        <input type="hidden" name="academy_company_id" value="{{ $selectedCompanyId }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Nome Completo</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                placeholder="Ex: João Silva">
                            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">E-mail Corporativo</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                placeholder="joao@projeto.com">
                            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">CPF (Obrigatório)</label>
                            <input type="text" name="cpf" value="{{ old('cpf') }}" required
                                x-mask="999.999.999-99"
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                placeholder="000.000.000-00">
                            @error('cpf') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Perfil de Acesso</label>
                            <select name="profile_id" id="profile_id" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                                <option value="">Selecione um perfil...</option>
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}" {{ (old('profile_id', $selectedRoleId) == $profile->id) ? 'selected' : '' }}>
                                        {{ $profile->label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('profile_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Plano de Assinatura</label>
                            <select name="plan_id" id="plan_id" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id', 1) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Status Inicial</label>
                            <div class="flex items-center gap-4 mt-2">
                                <label class="flex items-center gap-2 cursor-pointer bg-black/20 px-4 py-2 border border-white/10 rounded-xl group hover:border-blue-500 transition-all">
                                    <input type="radio" name="status" value="active" checked class="hidden peer">
                                    <div class="w-4 h-4 rounded-full border border-gray-600 peer-checked:bg-blue-500 peer-checked:border-blue-500 transition-all"></div>
                                    <span class="text-gray-300">Ativo</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-black/20 px-4 py-2 border border-white/10 rounded-xl group hover:border-red-500 transition-all">
                                    <input type="radio" name="status" value="blocked" class="hidden peer">
                                    <div class="w-4 h-4 rounded-full border border-gray-600 peer-checked:bg-red-500 peer-checked:border-red-500 transition-all"></div>
                                    <span class="text-gray-300">Bloqueado</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-black/20 px-4 py-2 border border-white/10 rounded-xl group hover:border-yellow-500 transition-all">
                                    <input type="radio" name="status" value="pending" class="hidden peer">
                                    <div class="w-4 h-4 rounded-full border border-gray-600 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                    <span class="text-gray-300">Pendente</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Data de Nascimento</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all [color-scheme:dark]">
                            @error('birth_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Sexo</label>
                            <select name="sex" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                                <option value="">Selecione...</option>
                                <option value="M" {{ old('sex') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sex') == 'F' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            @error('sex') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Senha</label>
                            <input type="password" name="password" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                placeholder="Ex: Academia@2026">
                            <p class="text-gray-500 text-xs mt-1">Mínimo 8 caracteres contendo maiúscula, número e símbolo.</p>
                            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Confirmar Senha</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                placeholder="Repita a senha">
                        </div>
                    </div>

                    @if(!isset($selectedCompanyId))
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Clínica / Empresa</label>
                        <select name="academy_company_id" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="">Nenhuma (Global)</option>
                            @foreach($companies as $company_item)
                                <option value="{{ $company_item->id }}" {{ old('academy_company_id', $selectedCompanyId) == $company_item->id ? 'selected' : '' }}>
                                    {{ $company_item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academy_company_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    <!-- SELECÇÃO PROFISSIONAL -->
                    <div id="professional_section" class="hidden space-y-8 animate-fadeIn border-t border-white/10 pt-8 mt-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white uppercase tracking-wider">Dados Profissionais</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Profissão (Obrigatório)</label>
                                <select name="profession_id" id="profession_id"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all">
                                    <option value="">Selecione...</option>
                                    @foreach($professions as $prof)
                                        <option value="{{ $prof->id }}" {{ old('profession_id') == $prof->id ? 'selected' : '' }}>
                                            {{ $prof->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('profession_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Especialidade (Obrigatório)</label>
                                <select name="specialty" id="specialty_id"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all">
                                    <option value="">Selecione uma profissão primeiro...</option>
                                </select>
                                @error('specialty') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4 md:col-span-2">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Conselho</label>
                                    <select name="council"
                                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all">
                                        @foreach(['CRM', 'CREF', 'CRN', 'CREFITO', 'CRP', 'COREN'] as $council)
                                            <option value="{{ $council }}" {{ old('council') == $council ? 'selected' : '' }}>{{ $council }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Registro</label>
                                    <input type="text" name="registration_number" value="{{ old('registration_number') }}"
                                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all"
                                        placeholder="123456">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">UF</label>
                                    <select name="registration_uf"
                                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all">
                                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                            <option value="{{ $uf }}" {{ old('registration_uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Validade do Registro (Obrigatório)</label>
                                <input type="date" name="registration_expiry_date" value="{{ old('registration_expiry_date') }}"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all">
                                @error('registration_expiry_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Documento do Conselho (PDF/JPG/PNG)</label>
                                <input type="file" name="document_file"
                                    class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-gray-400 focus:outline-none focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                                <p class="text-xs text-gray-500 mt-2">Máximo 5MB</p>
                            </div>
                        </div>

                        <!-- ASSINATURA DIGITAL -->
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-400">Assinatura Digital</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-black/40 border border-white/5 rounded-2xl p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm text-gray-300">Desenhar Assinatura</span>
                                        <button type="button" id="clear_signature" class="text-xs text-red-400 hover:text-red-300 transition-colors uppercase font-bold tracking-tighter">
                                            <i class="fas fa-eraser mr-1"></i> Limpar
                                        </button>
                                    </div>
                                    <canvas id="signature_pad" class="w-full h-40 bg-white/5 border border-white/10 rounded-xl cursor-crosshair"></canvas>
                                    <input type="hidden" name="signature_data" id="signature_data">
                                </div>

                                <div class="flex flex-col justify-center bg-black/40 border border-white/5 rounded-2xl p-6">
                                    <span class="text-sm text-gray-300 mb-4">Ou Upload da Assinatura (PNG transparente)</span>
                                    <input type="file" name="signature_file" accept="image/png"
                                        class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                                </div>
                            </div>
                        </div>
                                    <div class="border-t border-white/10 pt-8 flex items-center gap-6 mb-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_admin" value="1" class="hidden peer">
                            <div class="w-6 h-6 rounded-md border border-white/20 peer-checked:bg-indigo-500 peer-checked:border-indigo-500 flex items-center justify-center transition-all">
                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                            </div>
                            <span class="text-gray-300 group-hover:text-white transition-colors">Super Administrador</span>
                        </label>
                    </div>      </div>

                    <div class="flex justify-end gap-4">
                        <button type="reset" id="reset_form" class="px-6 py-3 text-gray-400 hover:text-white transition-colors">Limpar Campos</button>
                        <button type="submit" class="px-10 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transform hover:-translate-y-1 transition-all duration-300">
                            Cadastrar Usuário
                        </button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const roleSelect = document.getElementById('profile_id');
                        const profSection = document.getElementById('professional_section');
                        const professionSelect = document.getElementById('profession_id');
                        const specialtySelect = document.getElementById('specialty_id');
                        const canvas = document.getElementById('signature_pad');
                        const signatureDataInput = document.getElementById('signature_data');
                        const clearBtn = document.getElementById('clear_signature');
                        const ctx = canvas?.getContext('2d');
                        
                        const specialties = @json($specialties);
                        let drawing = false;

                        // Toggle Section
                        function checkRole() {
                            if (roleSelect.value == '4') { // Profile Profissional
                                profSection.classList.remove('hidden');
                                setTimeout(() => profSection.classList.add('opacity-100'), 10);
                            } else {
                                profSection.classList.add('hidden');
                            }
                        }

                        // Filter Specialties
                        function filterSpecialties() {
                            const professionId = professionSelect.value;
                            specialtySelect.innerHTML = '<option value="">Selecione a especialidade...</option>';
                            
                            if (!professionId) {
                                specialtySelect.innerHTML = '<option value="">Selecione uma profissão primeiro...</option>';
                                return;
                            }

                            const filtered = specialties.filter(s => s.profession_id == professionId || !s.profession_id);
                            
                            if (filtered.length === 0) {
                                specialtySelect.innerHTML = '<option value="">Nenhuma especialidade disponível</option>';
                                return;
                            }

                            filtered.forEach(s => {
                                const option = document.createElement('option');
                                option.value = s.nome; // Salvamos o nome como string para manter compatibilidade
                                option.textContent = s.nome;
                                specialtySelect.appendChild(option);
                            });
                        }

                        roleSelect.addEventListener('change', checkRole);
                        professionSelect.addEventListener('change', filterSpecialties);
                        
                        checkRole();
                        if (professionSelect.value) filterSpecialties();

                        // Signature Pad Logic
                        if (canvas) {
                            // Ajustar tamanho do canvas
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
                                const x = (e.clientX || e.touches[0].clientX) - rect.left;
                                const y = (e.clientY || e.touches[0].clientY) - rect.top;

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
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-blue-600/10 border border-blue-500/20 rounded-3xl p-6">
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-400 mb-4 text-xl">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="text-white font-bold mb-2">Dica de Gestão</h3>
                <p class="text-blue-100/70 text-sm leading-relaxed">
                    Ao cadastrar um novo usuário, ele receberá automaticamente um e-mail de boas-vindas com instruções de acesso e detalhes sobre o seu perfil.
                </p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-3xl p-6">
                <h3 class="text-white font-bold mb-4">Hierarquia de Perfis</h3>
                <div class="space-y-4">
                    @foreach($profiles as $profile)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $profile->label }}</p>
                            <p class="text-gray-400 text-xs">{{ $profile->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
