@extends('layouts.app')

@section('title', 'Central Jurídica & Privacidade')

@section('content')
<div class="container-fluid py-4 min-h-[80vh]" x-data="{ tab: '{{ $activeTab ?? 'terms' }}' }">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Central <span class="text-blue-500">Jurídica</span></h1>
                <p class="text-zinc-400 font-medium mt-1">Transparência, segurança e controle sobre seus dados.</p>
            </div>
            <div class="text-zinc-500 text-xs font-mono uppercase tracking-widest bg-zinc-900/50 px-3 py-1.5 rounded-full border border-white/5">
                Atualizado em: {{ date('d/m/Y') }}
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[2rem] p-4 sticky top-4">
                    <nav class="space-y-1">
                        <button @click="tab = 'terms'" 
                                :class="tab === 'terms' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-zinc-400 hover:bg-white/5'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl font-bold transition-all text-sm group">
                            <i class="fas fa-file-contract w-5" :class="tab === 'terms' ? '' : 'text-zinc-600 group-hover:text-zinc-300'"></i>
                            Termos de Uso
                        </button>
                        
                        <button @click="tab = 'privacy'" 
                                :class="tab === 'privacy' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-zinc-400 hover:bg-white/5'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl font-bold transition-all text-sm group">
                            <i class="fas fa-user-shield w-5" :class="tab === 'privacy' ? '' : 'text-zinc-600 group-hover:text-zinc-300'"></i>
                            Privacidade
                        </button>

                        <button @click="tab = 'cookies'" 
                                :class="tab === 'cookies' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-zinc-400 hover:bg-white/5'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl font-bold transition-all text-sm group">
                            <i class="fas fa-cookie-bite w-5" :class="tab === 'cookies' ? '' : 'text-zinc-600 group-hover:text-zinc-300'"></i>
                            Cookies
                        </button>

                        <div class="my-4 border-t border-white/5"></div>

                        <button @click="tab = 'lgpd'" 
                                :class="tab === 'lgpd' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/20' : 'text-zinc-400 hover:bg-white/5'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl font-bold transition-all text-sm group">
                            <i class="fas fa-fingerprint w-5" :class="tab === 'lgpd' ? '' : 'text-zinc-600 group-hover:text-zinc-300'"></i>
                            Gestão LGPD
                        </button>
                    </nav>

                    <div class="mt-8 p-4 bg-blue-500/5 rounded-2xl border border-blue-500/10">
                        <h4 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-2">Precisa de Ajuda?</h4>
                        <p class="text-xs text-zinc-500 leading-relaxed mb-3">Dúvidas sobre seus dados? Fale com nosso DPO.</p>
                        <a href="mailto:dpo@nexshape.com.br" class="text-xs font-bold text-white hover:text-blue-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-envelope text-[10px]"></i>
                            dpo@nexshape.com.br
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1">
                <div class="bg-zinc-900/30 backdrop-blur-sm border border-white/5 rounded-[2.5rem] overflow-hidden">
                    
                    <!-- Tab: Terms of Use -->
                    <div x-show="tab === 'terms'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8 md:p-12">
                        <div class="legal-section space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                                    <i class="fas fa-file-contract text-xl"></i>
                                </div>
                                <h2 class="text-2xl font-black text-white">Termos de Uso</h2>
                            </div>
                            
                            <div class="prose prose-invert max-w-none text-zinc-400 leading-relaxed space-y-6">
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">1. Aceitação dos Termos</h3>
                                    <p>Ao acessar e utilizar o NexShape, você concorda em cumprir estes termos de uso. Se você não concorda com qualquer parte destes termos, não deve utilizar o sistema.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">2. Uso do Sistema</h3>
                                    <p>O NexShape é uma plataforma de auxílio ao treinamento e nutrição. As informações fornecidas são para fins informativos e não substituem o aconselhamento profissional de médicos, nutricionistas ou educadores físicos.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">3. Cadastro e Segurança</h3>
                                    <p>Você é responsável por manter a confidencialidade de sua senha e por todas as atividades que ocorrem em sua conta. Notifique-nos imediatamente sobre qualquer uso não autorizado.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">4. Propriedade Intelectual</h3>
                                    <p>Todo o conteúdo, design e algoritmos do sistema são de propriedade exclusiva do NexShape/Projeto Academia e protegidos por leis de direitos autorais.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">5. Isenção de Responsabilidade</h3>
                                    <p>Não nos responsabilizamos por lesões ou danos à saúde decorrentes do uso indevido das sugestões de treino e alimentação fornecidas pela plataforma. Consulte sempre um profissional de saúde antes de iniciar novas rotinas.</p>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Privacy Policy -->
                    <div x-show="tab === 'privacy'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8 md:p-12">
                        <div class="legal-section space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                                    <i class="fas fa-user-shield text-xl"></i>
                                </div>
                                <h2 class="text-2xl font-black text-white">Política de Privacidade</h2>
                            </div>
                            
                            <div class="prose prose-invert max-w-none text-zinc-400 leading-relaxed space-y-6">
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">1. Informações que Coletamos</h3>
                                    <p>Coletamos informações que você nos fornece diretamente ao criar uma conta, como nome, e-mail, idade, peso e altura. Também coletamos dados de uso automaticamente para melhorar sua experiência.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">2. Como Usamos Seus Dados</h3>
                                    <p>Seus dados são usados para personalizar seus planos de treino e dieta, realizar cálculos metabólicos e fornecer suporte técnico. Não vendemos suas informações para terceiros.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">3. Segurança dos Dados</h3>
                                    <p>Implementamos medidas técnicas e organizacionais para proteger seus dados pessoais contra acesso não autorizado, perda ou alteração. Suas senhas são armazenadas em hash criptográfico de alta segurança.</p>
                                </section>
                                
                                <section>
                                    <h3 class="text-lg font-bold text-white mb-3">4. Direitos do Titular</h3>
                                    <p>Conforme a LGPD, você tem direito ao acesso, correção, anonimização ou exclusão de seus dados. Você pode gerenciar essas opções diretamente na aba de Gestão LGPD.</p>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Cookies -->
                    <div x-show="tab === 'cookies'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8 md:p-12">
                        <div class="legal-section space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                                    <i class="fas fa-cookie-bite text-xl"></i>
                                </div>
                                <h2 class="text-2xl font-black text-white">Política de Cookies</h2>
                            </div>
                            
                            <div class="prose prose-invert max-w-none text-zinc-400 leading-relaxed space-y-6">
                                <p>Utilizamos cookies para melhorar sua experiência, lembrar suas preferências e analisar o tráfego do nosso site.</p>
                                
                                <div class="grid md:grid-cols-2 gap-4 mt-8">
                                    <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                        <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                            Essenciais
                                        </h4>
                                        <p class="text-xs text-zinc-500">Necessários para o funcionamento do sistema e segurança da conta.</p>
                                    </div>
                                    <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                        <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                                            <i class="fas fa-chart-bar text-blue-500"></i>
                                            Analíticos
                                        </h4>
                                        <p class="text-xs text-zinc-500">Nos ajudam a entender como você usa o sistema para fazermos melhorias.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: LGPD Management -->
                    <div x-show="tab === 'lgpd'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8 md:p-12">
                        <div class="legal-section space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500">
                                    <i class="fas fa-fingerprint text-xl"></i>
                                </div>
                                <h2 class="text-2xl font-black text-white">Gestão de Dados (LGPD)</h2>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Portability -->
                                <div class="bg-zinc-900 border border-white/5 p-6 rounded-[2rem] space-y-4">
                                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                                        <i class="fas fa-file-export"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-white">Portabilidade de Dados</h3>
                                        <p class="text-xs text-zinc-500 mt-1">Baixe todos os seus dados pessoais, treinos e histórico em formato JSON seguro.</p>
                                    </div>
                                    <a href="{{ route('privacy.download') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-500 transition-all">
                                        <i class="fas fa-download text-xs"></i>
                                        Baixar Meus Dados
                                    </a>
                                </div>

                                <!-- Deletion -->
                                <div class="bg-zinc-900 border border-white/5 p-6 rounded-[2rem] space-y-4">
                                    <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-white">Direito ao Esquecimento</h3>
                                        <p class="text-xs text-zinc-500 mt-1">Solicite a exclusão total da sua conta e de todos os dados vinculados a ela.</p>
                                    </div>
                                    <button onclick="document.getElementById('deleteRequestModal').style.display = 'flex'" class="inline-flex items-center gap-2 bg-zinc-800 text-red-500 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-red-500/10 transition-all">
                                        <i class="fas fa-user-times text-xs"></i>
                                        Solicitar Exclusão
                                    </button>
                                </div>
                            </div>

                            <div class="bg-amber-500/5 border border-amber-500/10 p-6 rounded-[2rem] mt-8">
                                <h4 class="text-amber-500 font-bold mb-2 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Importante
                                </h4>
                                <p class="text-sm text-zinc-500 leading-relaxed">
                                    O NexShape segue rigorosamente a Lei Federal nº 13.709/2018 (LGPD). Suas solicitações de exclusão podem levar até 15 dias para serem processadas completamente em nossos backups.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</div>

<!-- Modal: Account Deletion Request -->
<div id="deleteRequestModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
    <div class="bg-zinc-950 border border-zinc-800 w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl space-y-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto text-red-500 mb-6">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tight">Confirmar Solicitação</h3>
            <p class="text-zinc-500 mt-2 text-sm">Esta ação iniciará o processo de exclusão permanente. Por favor, conte-nos o motivo (opcional).</p>
        </div>

        <form action="{{ route('privacy.request-deletion') }}" method="POST" class="space-y-4">
            @csrf
            <textarea name="reason" rows="3" placeholder="Por que deseja nos deixar?" 
                      class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-white text-sm focus:border-blue-500 outline-none transition-all"></textarea>
            
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="document.getElementById('deleteRequestModal').style.display = 'none'" 
                        class="py-3.5 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-all">
                    Cancelar
                </button>
                <button type="submit" 
                        class="py-3.5 bg-red-600 text-white font-black rounded-2xl hover:bg-red-500 transition-all">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .legal-section h3 { margin-top: 2rem; }
</style>
@endsection
