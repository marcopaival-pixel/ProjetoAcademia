@extends('layouts.app')

@section('title', 'Central de Nutrição — NexShape')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('nutritionHub', () => ({
            goalModalOpen: false, 
            supplementModalOpen: false,
            stackModalOpen: false,
            editStackModalOpen: false,
            aiStackModalOpen: false,
            auditModalOpen: false,
            confirmDeleteModalOpen: false,
            deleteActionUrl: '',
            deleteItemName: '',
            confirmDelete(url, name) {
                this.deleteActionUrl = url;
                this.deleteItemName = name;
                this.confirmDeleteModalOpen = true;
            },
            aiCredits: {{ (int)(auth()->user()->ai_credits ?? 0) }},
            aiStackLoading: false,
            aiStackSuggestion: null,
            selectedStackId: null,
            selectedStack: null,
            suggestionModalOpen: false,
            mealSuggestion: '',
            loadingMeal: false,
            auditResult: '',
            loadingAudit: false,
            supplementSearch: '',
            catalogResults: [],
            showCatalog: false,
            async searchSupplement() {
                if (this.supplementSearch.length < 2) {
                    this.catalogResults = [];
                    this.showCatalog = false;
                    return;
                }
                try {
                    const resp = await fetch(`/smart-stacks/search-catalog?q=${encodeURIComponent(this.supplementSearch)}`);
                    this.catalogResults = await resp.json();
                    this.showCatalog = this.catalogResults.length > 0;
                } catch (e) {
                    console.error(e);
                }
            },
            selectFromCatalog(item) {
                this.supplementSearch = item.name;
                // Busca os campos dentro do modal de suplementos para preenchimento
                const modal = document.querySelector('[x-show="supplementModalOpen"]');
                if (modal) {
                    const dosageInput = modal.querySelector('input[name="dosage"]');
                    const unitInput = modal.querySelector('input[name="unit"]');
                    if (dosageInput && item.default_dosage) dosageInput.value = item.default_dosage;
                    if (unitInput && item.default_unit) unitInput.value = item.default_unit;
                }
                this.showCatalog = false;
            },
            async takeSupplement(id) {
                try {
                    const resp = await fetch(`/supplements/${id}/take`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // Success feedback
                    }
                } catch (e) {}
            },
            async getAISuggestion() {
                this.aiStackLoading = true;
                this.aiStackModalOpen = true;
                this.aiStackSuggestion = null;
                try {
                    const resp = await fetch('{{ route('smart-stacks.suggest') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const data = await resp.json();
                    if (data.success) {
                        this.aiStackSuggestion = data.suggestion;
                        this.aiCredits--;
                    } else {
                        alert(data.error || 'Erro ao gerar sugestão.');
                    }
                } catch (e) {
                    alert('Erro ao gerar sugestão da IA.');
                } finally {
                    this.aiStackLoading = false;
                }
            },
            async adoptAISuggestion() {
                if (!this.aiStackSuggestion) return;
                try {
                    const resp = await fetch('{{ route('smart-stacks.adopt-suggestion') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ suggestion: this.aiStackSuggestion })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erro ao adotar o stack.');
                    }
                } catch (e) {
                    alert('Erro na comunicação com o servidor.');
                }
            },
            async generateMeal() {
                this.loadingMeal = true;
                this.suggestionModalOpen = true;
                this.mealSuggestion = 'Analisando seus macros e gerando a melhor opção...';
                try {
                    const resp = await fetch('{{ route('nutrition.suggest-meal') }}');
                    const data = await resp.json();
                    if (data.success) {
                        this.mealSuggestion = data.suggestion;
                    } else {
                        this.mealSuggestion = 'Erro ao gerar sugestão: ' + data.error;
                        if (data.code === 'credits_exceeded') {
                            this.suggestionModalOpen = false;
                            window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
                        }
                    }
                } catch (e) {
                    this.mealSuggestion = 'Erro na comunicação com o servidor.';
                } finally {
                    this.loadingMeal = false;
                }
            },
            async runAudit() {
                this.loadingAudit = true;
                this.auditModalOpen = true;
                this.auditResult = 'Analisando sua semana...';
                try {
                    const resp = await fetch('{{ route('nutrition.audit') }}');
                    const data = await resp.json();
                    if (data.success) {
                        this.auditResult = data.audit;
                    } else {
                        this.auditResult = 'Erro na auditoria: ' + data.error;
                        if (data.code === 'credits_exceeded') {
                            this.auditModalOpen = false;
                            window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
                        }
                    }
                } catch (e) {
                    this.auditResult = 'Erro na comunicação com o servidor.';
                } finally {
                    this.loadingAudit = false;
                }
            },
            async addWaterInHub(amount) {
                try {
                    const resp = await fetch('{{ route('nutrition.add-water') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ amount })
                    });
                    const data = await resp.json();
                    if (resp.ok && data.success) {
                        window.location.reload(); 
                    } else {
                        alert('Erro ao registrar água: ' + (data.message || 'Erro desconhecido'));
                    }
                } catch (e) {
                    console.error('Erro ao registrar água', e);
                    alert('Falha de conexão ao registrar água.');
                }
            },
            async adoptSuggestedMeal() {
                if (!this.mealSuggestion) return;
                
                let metrics = { cal: 0, p: 0, c: 0, f: 0 };
                const regex = /METRICS:\s*\[cal:(\d+),\s*p:(\d+),\s*c:(\d+),\s*f:(\d+)\]/i;
                const match = this.mealSuggestion.match(regex);
                
                if (match) {
                    metrics = {
                        cal: parseInt(match[1]),
                        p: parseInt(match[2]),
                        c: parseInt(match[3]),
                        f: parseInt(match[4])
                    };
                } else {
                    metrics = {
                        cal: {{ (int)($remaining->cal ?? 0) }},
                        p: {{ (int)($remaining->p ?? 0) }},
                        c: {{ (int)($remaining->c ?? 0) }},
                        f: {{ (int)($remaining->f ?? 0) }}
                    };
                }

                try {
                    const resp = await fetch('{{ route('nutrition.adopt-meal') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ metrics, text: this.mealSuggestion })
                    });
                    if (resp.ok) {
                        window.location.href = '{{ route('diary') }}?flash=added';
                    }
                } catch (e) {
                    alert('Erro ao registrar refeição.');
                }
            },
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            selectedFood: null,
            amount: 100,
            unit: 'g',
            unitLabels: {
                'g': 'Gramas', 'ml': 'Mililitros', 'tbsp': 'Colher de sopa',
                'tsp': 'Colher de chá', 'cup': 'Xícara', 'slice': 'Fatia', 'un': 'Unidade'
            },
            conversionFactors: {
                'g': 1, 'ml': 1, 'tbsp': 15, 'tsp': 5, 'cup': 240, 'slice': 30, 'un': 100
            },
            favorites: [],
            loadingFavorites: false,
            async searchFood() {
                if (this.searchQuery.length < 3) {
                    this.searchResults = [];
                    return;
                }
                this.isSearching = true;
                try {
                    const resp = await fetch(`/api/food/search?q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await resp.json();
                    if (data.ok) {
                        this.searchResults = data.products;
                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    }
                } catch (e) {} finally { this.isSearching = false; }
            },
            async selectProduct(code) {
                try {
                    const resp = await fetch(`/api/food/product/${code}`);
                    const data = await resp.json();
                    if (data.ok) {
                        this.selectedFood = data.product;
                        this.searchQuery = data.product.name;
                        this.searchResults = [];
                    }
                } catch (e) {}
            },
            async loadFavorites(mealType = '') {
                this.loadingFavorites = true;
                try {
                    const resp = await fetch(`{{ route('nutrition.api.favorites') }}?meal_type=${mealType}`);
                    const data = await resp.json();
                    this.favorites = data;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                } catch (e) {
                    console.error('Erro ao carregar favoritos:', e);
                } finally { this.loadingFavorites = false; }
            },
            scannerOpen: false,
            photoOpen: false,
            photoFile: null,
            isAnalyzingPhoto: false,
            async analyzePhoto() {
                if (!this.photoFile) return;
                this.isAnalyzingPhoto = true;
                try {
                    const formData = new FormData();
                    formData.append('photo', this.photoFile);
                    formData.append('_token', '{{ csrf_token() }}');

                    const resp = await fetch('{{ route('nutrition.api.process-photo') }}', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.success) {
                        const food = data.foods[0];
                        this.selectedFood = {
                            name: food.name,
                            energy_kcal: food.kcal,
                            protein_g: food.p,
                            carbohydrates_g: food.c,
                            fat_g: food.f
                        };
                        this.amount = food.amount.replace(/[^0-9]/g, '') || 100;
                        this.photoOpen = false;
                        this.photoFile = null;
                    } else {
                        alert(data.error || 'Erro ao analisar foto.');
                    }
                } catch (e) {
                    alert('Erro na comunicação com o servidor.');
                } finally { this.isAnalyzingPhoto = false; }
            },
            aiInput: '',
            isProcessingAI: false,
            async processAI() {
                if (!this.aiInput) return;
                this.isProcessingAI = true;
                try {
                    const resp = await fetch('{{ route('nutrition.api.natural-language') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ text: this.aiInput })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        const food = data.foods[0];
                        this.selectedFood = {
                            name: food.name,
                            energy_kcal: food.kcal,
                            protein_g: food.p,
                            carbohydrates_g: food.c,
                            fat_g: food.f
                        };
                        this.amount = food.amount.replace(/[^0-9]/g, '') || 100;
                        this.aiInput = '';
                    } else {
                        if (data.code === 'credits_exceeded') {
                            window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
                        } else {
                            alert(data.error || 'Erro ao processar IA.');
                        }
                    }
                } catch (e) {} finally { this.isProcessingAI = false; }
            },
            get currentMacros() {
                if (!this.selectedFood) return { kcal: 0, p: 0, c: 0, f: 0 };
                const factor = this.conversionFactors[this.unit] || 1;
                const multiplier = (this.amount * factor) / 100;
                return {
                    kcal: Math.round(this.selectedFood.energy_kcal * multiplier),
                    p: (this.selectedFood.protein_g * multiplier).toFixed(1),
                    c: (this.selectedFood.carbohydrates_g * multiplier).toFixed(1),
                    f: (this.selectedFood.fat_g * multiplier).toFixed(1)
                };
            },
            repeatModalOpen: false,
            repeatMealType: '',
            repeatSource: 'yesterday',
            repeatTargetDate: '{{ $date }}',
            openRepeatModal(mtype) {
                this.repeatMealType = mtype;
                this.repeatModalOpen = true;
            }
        }));
    });
</script>

<div x-data="nutritionHub" class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Futuristic Tab Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-900 pb-1">
        <div class="flex items-center gap-4">
            <a href="{{ route('nutrition.index', ['tab' => 'dashboard', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'dashboard' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Dashboard
               @if($tab === 'dashboard')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 rounded-t-full shadow-[0_-4px_12px_rgba(16,185,129,0.5)]"></div>
               @endif
            </a>
            <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'diary' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Diário de Comida
               @if($tab === 'diary')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 rounded-t-full shadow-[0_-4px_12px_rgba(16,185,129,0.5)]"></div>
               @endif
            </a>
            <a href="{{ route('nutrition.index', ['tab' => 'stacks', 'date' => $date]) }}" 
               class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative {{ $tab === 'stacks' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300' }}">
               Suplementação
               @if($tab === 'stacks')
                   <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 rounded-t-full shadow-[0_-4px_12px_rgba(16,185,129,0.5)]"></div>
               @endif
            </a>
        </div>

        <!-- High-Performance Weekly Navigator -->
        <div class="flex items-center gap-3 bg-zinc-900/30 p-1 rounded-[2.5rem] border border-zinc-800/50 shadow-inner">
            <a href="{{ route('nutrition.index', ['tab' => $tab, 'date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:bg-emerald-500 hover:text-zinc-950 rounded-full transition-all">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>

            <div class="flex items-center gap-2 overflow-hidden px-2">
                @php
                    $pivot = \Carbon\Carbon::parse($date);
                    $start = $pivot->copy()->subDays(2);
                @endphp
                @for($i = 0; $i < 5; $i++)
                    @php
                        $day = $start->copy()->addDays($i);
                        $isCurrent = $day->isSameDay($pivot);
                        $isToday = $day->isToday();
                    @endphp
                    <a href="{{ route('nutrition.index', ['tab' => $tab, 'date' => $day->format('Y-m-d')]) }}" 
                       class="flex flex-col items-center justify-center min-w-[50px] h-14 rounded-2xl transition-all {{ $isCurrent ? 'bg-emerald-500 text-zinc-950 shadow-lg scale-105' : 'hover:bg-white/5 text-zinc-500' }}">
                        <span class="text-[7px] font-black uppercase tracking-widest">{{ $day->translatedFormat('D') }}</span>
                        <span class="text-sm font-black">{{ $day->format('d') }}</span>
                    </a>
                @endfor
            </div>

            <div class="relative group">
                <input type="date" value="{{ $date }}" 
                       onchange="window.location.href = '{{ route('nutrition.index', ['tab' => $tab]) }}&date=' + this.value"
                       class="absolute inset-0 opacity-0 cursor-pointer z-10">
                <div class="w-10 h-10 flex items-center justify-center bg-zinc-950 border border-zinc-800 rounded-full text-emerald-500 group-hover:border-emerald-500/50 transition-all shadow-xl">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
            </div>

            <a href="{{ route('nutrition.index', ['tab' => $tab, 'date' => date('Y-m-d', strtotime($date . ' + 1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:bg-emerald-500 hover:text-zinc-950 rounded-full transition-all">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    @if($tab === 'diary' || $tab === 'stacks')
        @if(!$isPremium)
            <div class="relative min-h-[500px] flex flex-col items-center justify-center p-12 text-center bg-zinc-900/40 border border-amber-500/10 rounded-[3rem] overflow-hidden group shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-zinc-950/20 to-zinc-950/40"></div>
                <div class="relative z-10 max-w-lg space-y-8 animate-fade-in-up">
                    <div class="w-24 h-24 rounded-[2rem] bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/20 mx-auto shadow-2xl">
                        <i data-lucide="lock" class="w-10 h-10"></i>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-3xl font-black text-white uppercase tracking-tighter">Diário & Suplementação <span class="text-emerald-500">Premium</span></h2>
                        <p class="text-zinc-500 text-sm font-medium leading-relaxed">
                            Controle cada grama da sua evolução com o diário completo, busca em banco de dados global e gestão inteligente de suplementação (Smart Stacks).
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <x-premium-button variant="primary" size="lg" data-premium-locked>
                            DESBLOQUEAR PLANO COMPLETO
                        </x-premium-button>
                        <x-premium-button variant="secondary" size="lg" href="{{ route('nutrition.index', ['tab' => 'dashboard']) }}">
                            VOLTAR AO DASHBOARD
                        </x-premium-button>
                    </div>
                </div>
                
                <!-- Background Teaser Elements (Blurred) -->
                <div class="absolute inset-x-0 bottom-0 top-1/2 blur-2xl opacity-10 select-none pointer-events-none transform translate-y-20">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-40 bg-white rounded-3xl"></div>
                        <div class="h-40 bg-white rounded-3xl"></div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="{{ (!$isPremium && ($tab === 'diary' || $tab === 'stacks')) ? 'hidden' : '' }}">
    @if($tab === 'dashboard')
        <!-- Barra de Metas Nutricionais -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $targetP = $macroTargets['p'] ?? 0;
            $targetC = $macroTargets['c'] ?? 0;
            $targetF = $macroTargets['f'] ?? 0;
            
            $percCal = (int)min((($selectedDateSums->cal ?? 0) / ($targetKcal ?: 1)) * 100, 100);
            $percP = (int)min((($selectedDateSums->p ?? 0) / ($targetP ?: 1)) * 100, 100);
            $percC = (int)min((($selectedDateSums->c ?? 0) / ($targetC ?: 1)) * 100, 100);
            $percF = (int)min((($selectedDateSums->f ?? 0) / ($targetF ?: 1)) * 100, 100);
        @endphp
        
        <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-3xl relative overflow-hidden group shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Calorias</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->cal ?? 0, 0) }} / {{ number_format($targetKcal, 0) }} kcal</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden p-0.5 border border-white/5">
                <div class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] transition-all duration-1000 rounded-full" style="width: {{ $percCal }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-3xl shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-rose-400">Proteínas</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->p ?? 0, 0) }} / {{ number_format($targetP, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden p-0.5 border border-white/5">
                <div class="h-full bg-rose-500 transition-all duration-1000 rounded-full shadow-[0_0_10px_rgba(244,63,94,0.3)]" style="width: {{ $percP }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-3xl shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-emerald-400">Carboidratos</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->c ?? 0, 0) }} / {{ number_format($targetC, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden p-0.5 border border-white/5">
                <div class="h-full bg-emerald-400 transition-all duration-1000 rounded-full shadow-[0_0_10px_rgba(52,211,153,0.3)]" style="width: {{ $percC }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-3xl shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest text-amber-400">Gorduras</span>
                <span class="text-xs font-black text-white">{{ number_format($selectedDateSums->f ?? 0, 0) }} / {{ number_format($targetF, 0) }}g</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden p-0.5 border border-white/5">
                <div class="h-full bg-amber-500 transition-all duration-1000 rounded-full shadow-[0_0_10px_rgba(245,158,11,0.3)]" style="width: {{ $percF }}%"></div>
            </div>
        </div>
    </div>

    <!-- Header Clean -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-5">
            <a href="{{ route('patient.reports.index') }}" class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 border border-zinc-800 hover:text-white hover:border-emerald-500/50 transition-all shadow-xl" title="Voltar ao Hub">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Central de <span class="text-emerald-500">Nutrição</span></h1>
                <p class="text-zinc-500 text-sm mt-1">Gestão inteligente de metas e balanço calórico.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-premium-button variant="secondary" icon="microscope" size="sm" @click="runAudit()">
                ANALISAR COM IA
            </x-premium-button>
            <x-premium-button variant="primary" icon="settings" size="sm" @click="goalModalOpen = true">
                AJUSTAR ESTRATÉGIA
            </x-premium-button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Column 1: Metabolic & Consistency -->
        <div class="space-y-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    Análise Metabólica
                </h3>

                @if($stats['ok'])
                <div class="space-y-6">
                    <div>
                        <span class="text-[10px] text-zinc-500 font-bold uppercase block mb-1">Taxa Basal (TMB)</span>
                        <p class="text-xl font-black text-white tracking-tight">{{ number_format($stats['bmr'], 0, ',', '.') }} <small class="text-zinc-600 text-[10px]">kcal</small></p>
                    </div>
                    <div>
                        <span class="text-[10px] text-zinc-500 font-bold uppercase block mb-1">Gasto Total (TDEE)</span>
                        <p class="text-xl font-black text-emerald-500 tracking-tight">{{ number_format($stats['tdee'], 0, ',', '.') }} <small class="text-zinc-600 text-[10px]">kcal</small></p>
                    </div>
                    <div class="pt-6 border-t border-zinc-800">
                        <span class="text-[10px] text-emerald-400 font-black uppercase block mb-1 tracking-widest">Meta Diária</span>
                        <p class="text-3xl font-black text-white tracking-tighter">{{ number_format($targetKcal, 0, ',', '.') }} <small class="text-zinc-600 text-xs italic font-normal">kcal</small></p>
                    </div>
                </div>
                @else
                <div class="p-4 bg-zinc-950 rounded-2xl border border-zinc-800 text-zinc-500 text-[10px] italic">
                    {{ $stats['message'] }}
                </div>
                @endif
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-2xl">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Consistência Semanal
                </h3>
                <div class="text-center py-2">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/5 border border-emerald-500/20 mb-4 shadow-inner">
                        <span class="text-3xl font-black text-emerald-400">{{ $consistencyCount }}<small class="text-zinc-600 text-xs">/7</small></span>
                    </div>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Dias no alvo</p>
                    <p class="text-xs text-zinc-400 mt-2 font-medium">
                        @if($consistencyCount >= 6) Excelente disciplina!
                        @elseif($consistencyCount >= 4) Boa constância.
                        @else Foco no objetivo! @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Column 2 & 3: Main Charts -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Tendência de Calorias (15 dias)
                    </h3>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Real
                        </span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full border border-dashed border-zinc-600"></span> Meta
                        </span>
                    </div>
                </div>
                <div style="height: 250px;">
                    <canvas id="calorieTrendChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Distribuição de Macros
                    </h3>
                    <div class="flex items-center justify-center p-4">
                        <div style="width: 180px; height: 180px;">
                            <canvas id="macroDonutChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl flex flex-col justify-center">
                    <div class="space-y-6">
                        @foreach([
                            ['label' => 'Proteínas', 'val' => $macroTargets['p'], 'color' => '#fb7185', 'icon' => 'P'],
                            ['label' => 'Carbo', 'val' => $macroTargets['c'], 'color' => '#34d399', 'icon' => 'C'],
                            ['label' => 'Gorduras', 'val' => $macroTargets['f'], 'color' => '#fbbf24', 'icon' => 'G']
                        ] as $m)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-zinc-950 flex items-center justify-center text-[10px] font-black" style="color: {{ $m['color'] }}; border: 1px solid {{ $m['color'] }}20;">{{ $m['icon'] }}</span>
                                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ $m['label'] }}</span>
                            </div>
                            <span class="text-lg font-black text-white tabular-nums">{{ $m['val'] ?? '—' }}g</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 4: Water & Insights -->
        <div class="space-y-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Hidratação
                </h3>
                <div class="relative flex flex-col items-center">
                    @php 
                        $waterPerc = (int)min(($waterToday / ($waterTarget ?: 2500)) * 100, 100);
                    @endphp
                    <div class="w-36 h-36 rounded-full border-4 border-zinc-800 flex flex-col items-center justify-center overflow-hidden relative shadow-inner">
                        <div class="absolute bottom-0 left-0 w-full transition-all duration-1000 bg-emerald-500/20 backdrop-blur-sm" style="height: {{ $waterPerc }}%"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center relative z-10">
                            <span class="text-2xl font-black text-white tracking-tight tabular-nums">{{ number_format($waterToday / 1000, 1) }}L</span>
                            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mt-1">de {{ number_format($waterTarget / 1000, 1) }}L</span>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-8 w-full">
                        <button @click="addWaterInHub(250)" class="flex-1 py-4 bg-emerald-500/5 hover:bg-emerald-500 hover:text-zinc-950 border border-emerald-500/20 rounded-2xl text-[10px] font-black text-emerald-400 transition-all active:scale-95 shadow-lg">
                            +250ml
                        </button>
                        <button @click="addWaterInHub(500)" class="flex-1 py-4 bg-emerald-500/5 hover:bg-emerald-500 hover:text-zinc-950 border border-emerald-500/20 rounded-2xl text-[10px] font-black text-emerald-400 transition-all active:scale-95 shadow-lg">
                            +500ml
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Insights Nutricionais
                </h3>
                <div class="space-y-6">
                    @php
                        $diffAverages = ($averages->cal ?? 0) - $targetKcal;
                    @endphp
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <i data-lucide="lightbulb" class="w-4 h-4 text-emerald-500"></i>
                        </div>
                        <p class="text-xs text-zinc-400 leading-relaxed font-medium">
                            @if(abs($diffAverages) < 100)
                                Balanço <span class="text-emerald-400 font-bold tracking-tight">excelente</span>! Você está quase idêntico à sua meta.
                            @elseif($diffAverages > 0)
                                Consumo médio está <span class="text-rose-400 font-bold">{{ number_format($diffAverages) }} kcal</span> acima da meta. 
                            @else
                                Consumo médio está <span class="text-amber-400 font-bold">{{ number_format(abs($diffAverages)) }} kcal</span> abaixo da meta.
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-400"></i>
                        </div>
                        <p class="text-xs text-zinc-400 leading-relaxed font-medium">
                            @if(in_array($currentGoal, ['lose', 'lose_aggressive']))
                                Priorize alimentos com baixa densidade calórica e alto volume.
                            @elseif($currentGoal == 'gain')
                                Garanta superávit e bom aporte de proteínas para hipertrofia.
                            @elseif($currentGoal == 'recomp')
                                Foco em proteínas altas e treinos intensos para recomposição.
                            @elseif($currentGoal == 'performance')
                                Garanta carboidratos suficientes para suportar sua carga de treino.
                            @else
                                Mantenha a consistência e foco na densidade nutricional.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tab === 'stacks')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- AI Suggester -->
        <div class="bg-gradient-to-br from-emerald-600/10 to-zinc-900 border border-zinc-800 rounded-[2rem] p-8 relative overflow-hidden group shadow-2xl">
            <!-- Decorative AI Core Background -->
            <div class="absolute -right-20 -top-20 opacity-20 group-hover:opacity-40 transition-opacity duration-1000">
                <x-ai-core size="lg" />
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-3">
                        <span class="p-2.5 bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20">
                            <i data-lucide="bot" class="w-4 h-4 text-zinc-950"></i>
                        </span>
                        NexShape AI — Sugestão Inteligente
                    </h3>
                    <span class="px-3 py-1 bg-emerald-500/10 rounded-full text-[10px] font-black text-emerald-400 uppercase tracking-widest border border-emerald-500/20">Elite IA</span>
                </div>

                <div class="space-y-8">
                    <p class="text-zinc-400 text-sm leading-relaxed font-medium">
                        Detectamos que você ainda tem <strong class="text-white">{{ number_format($remaining->cal) }} kcal</strong> disponíveis para hoje. 
                        Para otimizar seu resultado, sugerimos a ingestão de:
                    </p>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 shadow-inner text-center">
                            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Proteínas</span>
                            <p class="text-xl font-black text-rose-400 tabular-nums">{{ number_format($remaining->p) }}g</p>
                        </div>
                        <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 shadow-inner text-center">
                            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Carbos</span>
                            <p class="text-xl font-black text-emerald-400 tabular-nums">{{ number_format($remaining->c) }}g</p>
                        </div>
                        <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 shadow-inner text-center">
                            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Gorduras</span>
                            <p class="text-xl font-black text-amber-400 tabular-nums">{{ number_format($remaining->f) }}g</p>
                        </div>
                    </div>

                    <x-premium-button variant="primary" size="lg" class="w-full" @click="generateMeal()">
                        <span x-show="!loadingMeal">GERAR CARDÁPIO SUGERIDO</span>
                        <span x-show="loadingMeal" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            PROCESSANDO...
                        </span>
                    </x-premium-button>
                </div>
            </div>
        </div>



        <!-- Smart Stacks -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <span class="p-2.5 bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20">
                        <i data-lucide="layers" class="w-4 h-4 text-zinc-950"></i>
                    </span>
                    <div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Smart Stack</h3>
                        <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Suplementação Inteligente</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="getAISuggestion()" class="px-3 py-2 bg-emerald-500/5 text-emerald-400 text-[10px] font-black uppercase rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-zinc-950 transition-all flex items-center gap-2 shadow-lg">
                        <i data-lucide="sparkles" class="w-3 h-3"></i> IA
                    </button>
                    <button @click="stackModalOpen = true" class="w-10 h-10 flex items-center justify-center bg-zinc-950 border border-zinc-800 text-emerald-500 rounded-xl hover:border-emerald-500/50 transition-all shadow-xl">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($stacks as $stack)
                <div class="bg-zinc-950 border border-zinc-900 rounded-3xl overflow-hidden group/stack shadow-inner">
                    <div class="p-5 flex items-center justify-between border-b border-zinc-900 bg-gradient-to-r from-emerald-500/5 to-transparent">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-emerald-500 border border-emerald-500/20 shadow-lg">
                                <i data-lucide="package" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-white tracking-tight">{{ $stack->name }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase rounded tracking-widest">{{ $stack->goal ?? 'Saúde' }}</span>
                                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $stack->supplements->count() }} itens</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="selectedStackId = {{ $stack->id }}; supplementModalOpen = true; supplementSearch = ''; showCatalog = false" class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-white hover:border-emerald-500/50 transition-all">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                            </button>
                            <button @click='selectedStackId = {{ $stack->id }}; selectedStack = {!! json_encode($stack, JSON_HEX_APOS) !!}; editStackModalOpen = true' class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-emerald-400 transition-all">
                                <i data-lucide="edit-3" class="w-3 h-3"></i>
                            </button>
                            <form method="POST" action="{{ route('smart-stacks.destroy', $stack->id) }}" class="inline" onsubmit="return confirm('Deseja excluir permanentemente este Smart Stack?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-rose-500 transition-all">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="p-2 space-y-1">
                        @foreach($stack->supplements as $sup)
                        <div class="flex items-center justify-between p-3 bg-zinc-900/30 hover:bg-zinc-900/60 rounded-2xl transition-all group/item">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-zinc-950 border border-zinc-900 flex items-center justify-center">
                                    <i data-lucide="pill" class="w-3 h-3 text-zinc-700 group-hover/item:text-emerald-500 transition-colors"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white tracking-tight">{{ $sup->name }}</p>
                                    <p class="text-[9px] text-zinc-600 uppercase font-black tracking-widest mt-0.5">{{ $sup->dosage }}{{ $sup->unit }} &bull; {{ $sup->time_of_day }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="takeSupplement({{ $sup->id }}); $el.classList.add('bg-emerald-500', 'text-zinc-950')" 
                                        class="w-7 h-7 rounded-full border border-zinc-800 flex items-center justify-center hover:bg-emerald-500 hover:border-emerald-500 text-zinc-600 hover:text-zinc-950 transition-all @if($sup->last_taken_at && $sup->last_taken_at->isToday()) bg-emerald-500 text-zinc-950 border-emerald-500 @endif shadow-lg">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                </button>
                                <button type="button" 
                                        @click='confirmDelete("{{ route('smart-stacks.remove-supplement', $sup->id) }}", {!! json_encode($sup->name, JSON_HEX_APOS) !!})'
                                        class="w-7 h-7 rounded-full border border-zinc-800 flex items-center justify-center hover:bg-rose-500 hover:border-rose-500 text-zinc-600 hover:text-white transition-all shadow-lg">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-12 border-2 border-dashed border-zinc-800 rounded-[2rem]">
                    <i data-lucide="pills" class="w-8 h-8 text-zinc-800 mx-auto mb-4"></i>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nenhum stack configurado</p>
                    <button @click="stackModalOpen = true" class="mt-6 px-6 py-2 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-lg">CRIAR STACK</button>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    @if($tab === 'diary')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start pb-20 animate-fade-in">
        <!-- Timeline Header -->
        <div class="lg:col-span-12 flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900">
             <div>
                <h2 class="text-2xl font-black text-white tracking-tighter uppercase">Timeline <span class="text-emerald-500">Alimentar</span></h2>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Registros de {{ date('d/m/Y', strtotime($date)) }}</p>
             </div>

            <div class="flex items-center gap-6 bg-zinc-900/50 p-4 rounded-3xl border border-zinc-800 shadow-2xl">
                 <div class="text-right">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Saldo do Dia</p>
                    <p class="text-xl font-black tabular-nums {{ ($targetKcal - ($selectedDateSums->cal ?? 0)) < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                        {{ number_format($targetKcal - ($selectedDateSums->cal ?? 0), 0) }} kcal
                    </p>
                 </div>
                 <div class="h-10 w-px bg-zinc-800"></div>
                 <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.3)]"></span>
                        <span class="text-[10px] text-zinc-400 font-black uppercase tabular-nums">{{ number_format($selectedDateSums->p ?? 0, 0) }}g P</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.3)]"></span>
                        <span class="text-[10px] text-zinc-400 font-black uppercase tabular-nums">{{ number_format($selectedDateSums->c ?? 0, 0) }}g C</span>
                    </div>
                 </div>
            </div>
        </div>

        <!-- Meals Timeline -->
        <div class="lg:col-span-8 space-y-8">
            @foreach(['breakfast', 'lunch', 'dinner', 'snack', 'other'] as $mtype)
                @php
                    $mealRows = $diaryRows->where('meal_type', $mtype);
                    $mealCal = $mealRows->sum('calories');
                    $icons = ['breakfast' => 'sun', 'lunch' => 'utensils', 'dinner' => 'moon', 'snack' => 'apple', 'other' => 'coffee'];
                @endphp
                <div class="group relative bg-zinc-900 border border-zinc-800 rounded-[2rem] overflow-hidden transition-all hover:border-emerald-500/20 shadow-xl">
                    <div class="p-6 flex items-center justify-between border-b border-zinc-800 bg-gradient-to-r from-emerald-500/5 to-transparent">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-emerald-500 border border-emerald-500/20 shadow-lg">
                                <i data-lucide="{{ $icons[$mtype] }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white tracking-tight uppercase">{{ $mealLabels[$mtype] }}</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $mealRows->count() }} Itens</span>
                                    <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">{{ $mealCal }} Kcal</span>
                                </div>
                            </div>
                        </div>
                        <button @click="openRepeatModal('{{ $mtype }}')" class="px-4 py-2 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-lg flex items-center gap-2">
                            <i data-lucide="repeat" class="w-3 h-3"></i> Repetir
                        </button>
                    </div>
                    
                    <div class="divide-y divide-zinc-900/50">
                        @forelse($mealRows as $row)
                        <div class="p-6 flex items-center justify-between group/item hover:bg-zinc-950/50 transition-all">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-white group-hover/item:text-emerald-400 transition-colors">{{ $row->food_name }}</h4>
                                <div class="flex items-center gap-3 text-[10px] text-zinc-600 mt-1 uppercase font-black tracking-widest">
                                    <span>{{ $row->amount }} {{ $row->unit }}</span>
                                    <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                                    <span class="text-zinc-500">{{ $row->calories }} kcal</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover/item:opacity-100 transition-all scale-95 group-hover/item:scale-100">
                                <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date, 'edit' => $row->id]) }}" class="w-9 h-9 bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-emerald-400 hover:border-emerald-400/50 rounded-xl flex items-center justify-center transition-all shadow-lg">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('diary') }}" data-confirm-delete>
                                    @csrf
                                    <input type="hidden" name="action" value="delete_food">
                                    <input type="hidden" name="entry_date" value="{{ $date }}">
                                    <input type="hidden" name="food_id" value="{{ $row->id }}">
                                    <button type="submit" class="w-9 h-9 bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-rose-500 hover:border-rose-500/50 rounded-xl flex items-center justify-center transition-all shadow-lg">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center opacity-40 italic">
                            <p class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">Nenhum registro</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Sidebar Form -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                <header class="mb-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter">{{ $editRow ? 'Editar Registro' : 'Lançar Alimento' }}</h3>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-1">Smart Engine Integration</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="scannerOpen = true" class="w-11 h-11 rounded-xl bg-zinc-950 text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all flex items-center justify-center border border-zinc-800 shadow-xl group">
                            <i data-lucide="barcode" class="w-5 h-5"></i>
                        </button>
                        <button type="button" @click="photoOpen = true" class="w-11 h-11 rounded-xl bg-zinc-950 text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all flex items-center justify-center border border-zinc-800 shadow-xl group">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                        </button>
                    </div>
                </header>

                <!-- Natural Language -->
                <div class="mb-8 group">
                    <div class="relative">
                        <textarea x-model="aiInput" placeholder="Ex: Almoço de hoje foi 200g de frango e salada..." 
                                  class="w-full bg-zinc-950 border border-zinc-800 rounded-3xl p-5 text-sm text-white outline-none focus:border-emerald-500/50 resize-none h-28 transition-all shadow-inner"></textarea>
                        <button @click="processAI()" 
                                class="absolute bottom-4 right-4 p-3 bg-emerald-500 text-zinc-950 rounded-2xl text-[10px] font-black uppercase hover:scale-105 active:scale-95 transition-all disabled:opacity-50 shadow-xl"
                                :disabled="!aiInput || isProcessingAI">
                            <span x-show="!isProcessingAI">PROCESSAR</span>
                            <i x-show="isProcessingAI" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        </button>
                    </div>
                </div>

                <div class="h-px bg-zinc-800 mb-8"></div>

                <form method="POST" action="{{ route('diary') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="food_edit_id" value="{{ $editRow->id }}"> @endif

                    <div class="space-y-6">
                        <div class="relative">
                            <x-premium-input label="Buscar Alimento" name="food_name" x-model="searchQuery" @input.debounce.300ms="searchFood()" placeholder="Busque no banco de dados..." required />
                            <div x-show="isSearching" class="absolute right-4 top-11">
                                <svg class="animate-spin h-4 w-4 text-emerald-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                        
                        <!-- Search Preview -->
                        <div x-show="searchResults.length > 0" 
                             class="absolute z-50 left-8 right-8 mt-2 bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-72 overflow-y-auto animate-fade-in"
                             @click.away="searchResults = []">
                            <template x-for="product in searchResults" :key="product.code">
                                <button type="button" @click="selectProduct(product.code)" 
                                        class="w-full p-5 flex items-center justify-between hover:bg-emerald-500/5 border-b border-zinc-800/50 transition-all text-left group">
                                    <div>
                                        <p class="text-xs font-bold text-white group-hover:text-emerald-400" x-text="product.name"></p>
                                        <p class="text-[9px] text-zinc-600 uppercase font-black tracking-widest mt-1" x-text="product.brands"></p>
                                    </div>
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-500 opacity-40 group-hover:opacity-100 transition-all"></i>
                                </button>
                            </template>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-premium-input label="Qtde" name="amount" type="number" x-model="amount" />
                            <div class="space-y-1.5">
                                <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Unidade</label>
                                <select name="unit" x-model="unit" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-white text-sm font-medium outline-none focus:border-emerald-500/50 transition-all appearance-none shadow-inner">
                                    <template x-for="(label, key) in unitLabels" :key="key">
                                        <option :value="key" x-text="label"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Real-time Macro Preview -->
                        <div x-show="selectedFood" class="bg-emerald-500/5 border border-emerald-500/20 rounded-3xl p-6 animate-fade-in shadow-inner">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-[9px] text-emerald-400 font-black uppercase tracking-widest">Preview Nutricional</p>
                                <button type="button" @click="selectedFood = null; searchQuery = ''" class="text-[9px] text-zinc-600 font-black uppercase hover:text-rose-500 transition-colors">Limpar</button>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-center">
                                <div>
                                    <p class="text-sm font-black text-white tabular-nums" x-text="currentMacros.kcal"></p>
                                    <p class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest">Kcal</p>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-rose-400 tabular-nums" x-text="currentMacros.p"></p>
                                    <p class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest">Prot</p>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-emerald-400 tabular-nums" x-text="currentMacros.c"></p>
                                    <p class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest">Carb</p>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-amber-400 tabular-nums" x-text="currentMacros.f"></p>
                                    <p class="text-[8px] text-zinc-600 uppercase font-bold tracking-widest">Gord</p>
                                </div>
                            </div>
                            <input type="hidden" name="calories" :value="selectedFood.energy_kcal">
                            <input type="hidden" name="p_g" :value="selectedFood.protein_g">
                            <input type="hidden" name="c_g" :value="selectedFood.carbohydrates_g">
                            <input type="hidden" name="f_g" :value="selectedFood.fat_g">
                        </div>

                        <!-- Manual Fallback -->
                        <div x-show="!selectedFood" class="space-y-6 animate-fade-in">
                             <x-premium-input label="Calorias (kcal p/ 100g)" name="calories" type="number" value="{{ old('calories', $editRow->calories ?? '') }}" />
                             <div class="grid grid-cols-3 gap-3">
                                 @foreach([['p', 'Prot', 'rose'], ['c', 'Carb', 'emerald'], ['f', 'Gord', 'amber']] as $macro)
                                 <div class="space-y-2">
                                     <label class="block text-[9px] text-zinc-600 font-black uppercase tracking-widest text-center">{{ $macro[1] }} (g)</label>
                                     <input type="number" step="0.1" name="{{ $macro[0] }}_g" value="{{ old($macro[0].'_g', $editRow->{$macro[0].'_g'} ?? '') }}" 
                                            class="w-full bg-zinc-950 border border-zinc-800 rounded-xl p-3 text-center text-white text-xs font-black outline-none focus:border-{{ $macro[2] }}-500/50 shadow-inner">
                                 </div>
                                 @endforeach
                             </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3 px-1">Refeição</label>
                            <div class="grid grid-cols-2 gap-3">
                                @php
                                    $currentHour = now()->hour;
                                    $defaultMeal = 'other';
                                    if ($currentHour >= 5 && $currentHour < 10) $defaultMeal = 'breakfast';
                                    elseif ($currentHour >= 11 && $currentHour < 15) $defaultMeal = 'lunch';
                                    elseif ($currentHour >= 15 && $currentHour < 19) $defaultMeal = 'snack';
                                    elseif ($currentHour >= 19 && $currentHour < 23) $defaultMeal = 'dinner';
                                @endphp
                                @foreach($mealLabels as $val => $txt)
                                <label class="cursor-pointer">
                                    <input type="radio" name="meal_type" value="{{ $val }}" class="peer hidden" {{ ($editRow->meal_type ?? $defaultMeal) === $val ? 'checked' : '' }}>
                                    <div @click="loadFavorites('{{ $val }}')" class="py-3 text-center rounded-2xl bg-zinc-950 border border-zinc-800 text-[9px] font-black uppercase text-zinc-600 peer-checked:bg-emerald-500 peer-checked:text-zinc-950 peer-checked:border-emerald-500 shadow-xl transition-all active:scale-95">
                                        {{ $txt }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Favorites -->
                        <div class="pt-4" x-init="loadFavorites('{{ $defaultMeal }}')">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-[10px] text-zinc-600 font-black uppercase tracking-widest px-1">⭐ Favoritos Recentes</label>
                                <i x-show="loadingFavorites" data-lucide="loader-2" class="w-3 h-3 animate-spin text-zinc-700"></i>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="fav in favorites" :key="fav.food_name">
                                    <button type="button" @click="selectedFood = {energy_kcal: fav.calories, protein_g: fav.protein_g, carbohydrates_g: fav.carbs_g, fat_g: fav.fat_g, name: fav.food_name}; searchQuery = fav.food_name; unit = fav.unit || 'un'"
                                            class="px-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full text-[9px] font-black uppercase text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all shadow-xl">
                                        <span x-text="fav.food_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <x-premium-button variant="primary" size="lg" type="submit" class="w-full">
                            {{ $editRow ? 'ATUALIZAR REGISTRO' : 'CONFIRMAR LANÇAMENTO' }}
                        </x-premium-button>
                        
                        @if($editRow)
                            <a href="{{ route('nutrition.index', ['tab' => 'diary', 'date' => $date]) }}" class="block text-center mt-6 text-zinc-600 text-[10px] font-black uppercase tracking-widest hover:text-white transition-colors">Cancelar Edição</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Side Hydration Card -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl shadow-emerald-500/5">
                 <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    Hidratação Express
                </h3>
                <div class="flex flex-col items-center">
                    <p class="text-3xl font-black text-white tracking-tighter tabular-nums mb-2">{{ number_format($waterToday / 1000, 1) }}L</p>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-8 italic">Meta: {{ number_format($waterTarget / 1000, 1) }}L</p>
                    <div class="flex gap-2 w-full">
                        @foreach([250, 500, 1000] as $ml)
                        <button @click="addWaterInHub({{ $ml }})" class="flex-1 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl text-[9px] font-black text-emerald-500 hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-xl active:scale-95">
                            +{{ $ml >= 1000 ? '1L' : $ml.'ml' }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modals Section -->
    <!-- Create Smart Stack Modal -->
    <div x-show="stackModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         x-cloak
         @keydown.escape.window="stackModalOpen = false">
        <div class="absolute inset-0" @click="stackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
                <div>
                    <h5 class="text-white font-black text-2xl uppercase tracking-tighter">Novo <span class="text-emerald-500">Smart Stack</span></h5>
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Configure sua nova rotina de performance</p>
                </div>
                <button @click="stackModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('smart-stacks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="responsible_type" value="ia">
                <div class="p-10 space-y-6">
                    <x-premium-input label="Nome do Stack" name="name" placeholder="Ex: Performance Matinal, Recovery Noite..." required />
                    <x-premium-input label="Objetivo principal" name="goal" placeholder="Ex: Hipertrofia, Queima de Gordura, Foco..." />
                    <div class="space-y-2">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Notas / Observações</label>
                        <textarea name="notes" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-sm text-white outline-none focus:border-emerald-500/50 resize-none h-24 transition-all shadow-inner"></textarea>
                    </div>
                </div>

                <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                    <x-premium-button variant="secondary" class="flex-1" type="button" @click="stackModalOpen = false">CANCELAR</x-premium-button>
                    <x-premium-button variant="primary" class="flex-1" type="submit">CRIAR STACK</x-premium-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Smart Stack Modal -->
    <div x-show="editStackModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         x-cloak
         @keydown.escape.window="editStackModalOpen = false">
        <div class="absolute inset-0" @click="editStackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
                <div>
                    <h5 class="text-white font-black text-2xl uppercase tracking-tighter">Editar <span class="text-emerald-500">Smart Stack</span></h5>
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Ajustando sua rotina de performance</p>
                </div>
                <button @click="editStackModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form :action="'/smart-stacks/' + selectedStackId" method="POST">
                @csrf
                @method('PUT')
                <div class="p-10 space-y-6">
                    <x-premium-input label="Nome do Stack" name="name" x-bind:value="selectedStack?.name" required />
                    <x-premium-input label="Objetivo principal" name="goal" x-bind:value="selectedStack?.goal" />
                    <div class="space-y-4">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Status</label>
                        <select name="status" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white font-black outline-none focus:border-emerald-500/50 transition-all appearance-none cursor-pointer shadow-inner">
                            <option value="ativo" :selected="selectedStack?.status === 'ativo'">Ativo</option>
                            <option value="pausado" :selected="selectedStack?.status === 'pausado'">Pausado</option>
                            <option value="concluído" :selected="selectedStack?.status === 'concluído'">Concluído</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Notas / Observações</label>
                        <textarea name="notes" x-text="selectedStack?.notes" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-sm text-white outline-none focus:border-emerald-500/50 resize-none h-24 transition-all shadow-inner"></textarea>
                    </div>
                </div>

                <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                    <x-premium-button variant="secondary" class="flex-1" type="button" @click="editStackModalOpen = false">CANCELAR</x-premium-button>
                    <x-premium-button variant="primary" class="flex-1" type="submit">SALVAR ALTERAÇÕES</x-premium-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Supplement Modal -->
    <div x-show="supplementModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         x-cloak
         @keydown.escape.window="supplementModalOpen = false">
        <div class="absolute inset-0" @click="supplementModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
                <div>
                    <h5 class="text-white font-black text-2xl uppercase tracking-tighter">Novo <span class="text-emerald-500">Suplemento</span></h5>
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Adicionando ao seu stack inteligente</p>
                </div>
                <button @click="supplementModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form :action="'/smart-stacks/' + selectedStackId + '/supplements'" method="POST">
                @csrf
                <div class="p-10 space-y-6">
                    <div class="relative">
                        <x-premium-input label="Nome do Suplemento" name="name" x-model="supplementSearch" @input.debounce.300ms="searchSupplement()" placeholder="Busque no catálogo ou digite..." required />
                        
                        <!-- Catalog Preview -->
                        <div x-show="showCatalog" 
                             class="absolute z-50 left-0 right-0 mt-2 bg-zinc-900 border border-zinc-800 rounded-[2rem] shadow-2xl overflow-hidden max-h-60 overflow-y-auto animate-fade-in"
                             @click.away="showCatalog = false">
                            <template x-for="item in catalogResults" :key="item.id">
                                <button type="button" @click="selectFromCatalog(item)" 
                                        class="w-full p-4 flex items-center justify-between hover:bg-emerald-500/5 border-b border-zinc-800/50 transition-all text-left group">
                                    <div>
                                        <p class="text-xs font-bold text-white group-hover:text-emerald-400" x-text="item.name"></p>
                                        <p class="text-[9px] text-zinc-600 uppercase font-black tracking-widest mt-1" x-text="item.category"></p>
                                    </div>
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-500 opacity-40 group-hover:opacity-100 transition-all"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-premium-input label="Dosagem" name="dosage" placeholder="Ex: 5, 500, 1..." />
                        <x-premium-input label="Unidade" name="unit" placeholder="Ex: g, mg, cápsula..." />
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <x-premium-input label="Horário / Período" name="time_of_day" placeholder="Ex: Pós-treino, Jejum, Antes de dormir..." />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Observações</label>
                        <textarea name="observations" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-sm text-white outline-none focus:border-emerald-500/50 resize-none h-24 transition-all shadow-inner"></textarea>
                    </div>
                </div>

                <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                    <x-premium-button variant="secondary" class="flex-1" type="button" @click="supplementModalOpen = false">CANCELAR</x-premium-button>
                    <x-premium-button variant="primary" class="flex-1" type="submit">ADICIONAR SUPLEMENTO</x-premium-button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Stack Suggestion Modal -->
    <div x-show="aiStackModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-2xl animate-fade-in"
         style="display: none;"
         x-cloak
         @keydown.escape.window="aiStackModalOpen = false">
        <div class="absolute inset-0" @click="aiStackModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-3xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between bg-emerald-500/5">
                <h5 class="text-white font-black text-2xl uppercase tracking-tighter flex items-center gap-4">
                    <i data-lucide="sparkles" class="w-6 h-6 text-emerald-500"></i>
                    IA — Sugestão de Stack
                </h5>
                <button @click="aiStackModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-10 max-h-[70vh] overflow-y-auto">
                <div x-show="aiStackLoading" class="flex flex-col items-center justify-center py-16 space-y-10">
                    <x-ai-core size="md" />
                    <div class="text-center">
                        <p class="text-white font-black uppercase text-base tracking-widest mb-2 animate-pulse">Bio-análise de Necessidades</p>
                        <p class="text-zinc-600 text-[10px] uppercase font-bold tracking-widest">A IA está processando seu perfil para otimizar os suplementos...</p>
                    </div>
                </div>
                
                <div x-show="!aiStackLoading && aiStackSuggestion" class="space-y-8 animate-fade-in">
                    <div class="bg-zinc-950 p-8 rounded-[2rem] border border-zinc-800 shadow-inner">
                        <h6 class="text-emerald-500 font-black text-xs uppercase tracking-widest mb-4" x-text="aiStackSuggestion?.stack_name"></h6>
                        <p class="text-zinc-400 text-sm italic leading-relaxed" x-text="aiStackSuggestion?.goal"></p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <template x-for="sup in aiStackSuggestion?.supplements" :key="sup.name">
                            <div class="flex items-center justify-between p-5 bg-zinc-950 border border-zinc-800 rounded-2xl">
                                <div>
                                    <p class="text-sm font-black text-white" x-text="sup.name"></p>
                                    <p class="text-[10px] text-zinc-600 font-bold uppercase mt-1">
                                        <span x-text="sup.dosage"></span><span x-text="sup.unit"></span> &bull; 
                                        <span x-text="sup.time_of_day"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest" x-text="sup.goal"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                <x-premium-button variant="secondary" class="flex-1" @click="aiStackModalOpen = false">FECHAR</x-premium-button>
                <x-premium-button variant="primary" class="flex-1" x-show="aiStackSuggestion" @click="adoptAISuggestion()">ADOTAR ESTE STACK</x-premium-button>
            </div>
        </div>
    </div>

    <!-- Repeat Meal Modal -->
    <div x-show="repeatModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="repeatModalOpen = false">
        <div class="absolute inset-0" @click="repeatModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-8 border-b border-zinc-800 bg-zinc-950/50 flex items-center justify-between">
                <h5 class="text-white font-black text-xl uppercase tracking-tighter">Repetir Refeição</h5>
                <button @click="repeatModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ route('nutrition.api.repeat-meal') }}" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="meal_type" :value="repeatMealType">
                <input type="hidden" name="target_date" :value="repeatTargetDate">
                
                <div class="space-y-4">
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Origem dos dados:</p>
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center gap-4 p-4 bg-zinc-950 border border-zinc-800 rounded-2xl cursor-pointer hover:border-emerald-500/50 transition-all">
                            <input type="radio" name="source" value="yesterday" x-model="repeatSource" class="text-emerald-500 focus:ring-emerald-500 bg-zinc-900 border-zinc-700">
                            <div>
                                <p class="text-sm font-bold text-white">Ontem</p>
                                <p class="text-[10px] text-zinc-600 uppercase font-black tracking-widest">Repetir o que comeu no dia anterior</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-4 p-4 bg-zinc-950 border border-zinc-800 rounded-2xl cursor-pointer hover:border-emerald-500/50 transition-all">
                            <input type="radio" name="source" value="last" x-model="repeatSource" class="text-emerald-500 focus:ring-emerald-500 bg-zinc-900 border-zinc-700">
                            <div>
                                <p class="text-sm font-bold text-white">Última vez</p>
                                <p class="text-[10px] text-zinc-600 uppercase font-black tracking-widest">Repetir a última ocorrência desta refeição</p>
                            </div>
                        </label>
                    </div>
                </div>

                <x-premium-button variant="primary" class="w-full">CONFIRMAR REPETIÇÃO</x-premium-button>
            </form>
        </div>
    </div>

    <!-- Scanner Modal (Placeholder for Mobile/Webcam) -->
    <div x-show="scannerOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;">
        <div class="absolute inset-0" @click="scannerOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3rem] overflow-hidden shadow-2xl">
            <div class="p-8 text-center space-y-6">
                <div class="w-20 h-20 bg-emerald-500/10 rounded-3xl flex items-center justify-center text-emerald-500 mx-auto">
                    <i data-lucide="smartphone" class="w-10 h-10"></i>
                </div>
                <div>
                    <h5 class="text-white font-black text-xl uppercase tracking-tighter">Scanner de Código de Barras</h5>
                    <p class="text-zinc-500 text-sm mt-2">O scanner está otimizado para o aplicativo mobile NexShape.</p>
                </div>
                <div class="bg-zinc-950 p-6 rounded-2xl border border-zinc-800">
                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Dica de Performance</p>
                    <p class="text-xs text-zinc-600 mt-2">Baixe nosso app para escanear produtos instantaneamente e sincronizar com seu diário.</p>
                </div>
                <x-premium-button variant="secondary" class="w-full" @click="scannerOpen = false">ENTENDI</x-premium-button>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div x-show="photoOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;">
        <div class="absolute inset-0" @click="photoOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3rem] overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-zinc-800 bg-zinc-950/50 flex items-center justify-between">
                <h5 class="text-white font-black text-xl uppercase tracking-tighter">Análise por Foto</h5>
                <button @click="photoOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="border-2 border-dashed border-zinc-800 rounded-[2rem] p-12 text-center hover:border-emerald-500/30 transition-all group">
                    <input type="file" id="photoInput" class="hidden" accept="image/*" @change="photoFile = $event.target.files[0]">
                    <label for="photoInput" class="cursor-pointer space-y-4 block">
                        <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center text-zinc-700 mx-auto group-hover:text-emerald-500 transition-colors">
                            <i data-lucide="image-plus" class="w-8 h-8"></i>
                        </div>
                        <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest" x-text="photoFile ? photoFile.name : 'Selecionar Foto do Prato'"></p>
                    </label>
                </div>

                <x-premium-button variant="primary" class="w-full" @click="analyzePhoto()" x-bind:disabled="!photoFile || isAnalyzingPhoto">
                    <span x-show="!isAnalyzingPhoto">ANALISAR COM IA</span>
                    <span x-show="isAnalyzingPhoto" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        PROCESSANDO...
                    </span>
                </x-premium-button>
            </div>
        </div>
    </div>

    <!-- Goal Adjustment Modal -->
    <div x-show="goalModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="goalModalOpen = false">
        <div class="absolute inset-0" @click="goalModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
                <div>
                    <h5 class="text-white font-black text-2xl uppercase tracking-tighter">Ajustar <span class="text-emerald-500">Estratégia</span></h5>
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">Recalculando seu caminho para o sucesso</p>
                </div>
                <button @click="goalModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('nutrition.update-goal') }}" method="POST">
                @csrf
                <div class="p-10 space-y-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Objetivo Primário</label>
                        <select name="goal" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white font-black outline-none focus:border-emerald-500/50 transition-all appearance-none cursor-pointer shadow-inner">
                            @foreach(\App\Models\UserProfile::getAvailableGoals() as $slug => $data)
                                <option value="{{ $slug }}" {{ $currentGoal == $slug ? 'selected' : '' }}>
                                    {{ $data['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest px-1">Distribuição de Macros (Split)</label>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach([
                                ['split' => 'cutting', 'label' => '🥩 High Protein', 'desc' => 'Máxima preservação de massa magra.'],
                                ['split' => 'bulking', 'label' => '🍝 High Carb', 'desc' => 'Foco em performance e volume muscular.'],
                                ['split' => 'maintenance', 'label' => '🥗 Equilibrado', 'desc' => 'Proporções otimizadas NexShape.']
                            ] as $s)
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="split" value="{{ $s['split'] }}" class="peer hidden" {{ $s['split'] == 'maintenance' ? 'checked' : '' }}>
                                <div class="p-5 rounded-3xl bg-zinc-950 border border-zinc-800 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 shadow-xl transition-all hover:border-emerald-500/30">
                                    <p class="text-white text-sm font-black peer-checked:text-zinc-950 transition-colors uppercase tracking-tight">{{ $s['label'] }}</p>
                                    <p class="text-[10px] text-zinc-600 mt-1 peer-checked:text-zinc-900 font-bold transition-colors">{{ $s['desc'] }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                    <x-premium-button variant="secondary" class="flex-1" type="button" @click="goalModalOpen = false">CANCELAR</x-premium-button>
                    <x-premium-button variant="primary" class="flex-1" type="submit">SALVAR ESTRATÉGIA</x-premium-button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Suggestion Result Modal -->
    <div x-show="suggestionModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-2xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="suggestionModalOpen = false">
        <div class="absolute inset-0" @click="suggestionModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between bg-emerald-500/5">
                <h5 class="text-white font-black text-2xl uppercase tracking-tighter flex items-center gap-4">
                    <i data-lucide="sparkles" class="w-6 h-6 text-emerald-500"></i>
                    NexShape AI Suggestion
                </h5>
                <button @click="suggestionModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-10 max-h-[60vh] overflow-y-auto">
                <div x-show="loadingMeal" class="flex flex-col items-center justify-center py-12 space-y-10">
                    <x-ai-core size="sm" />
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest animate-pulse">Consultando Engenharia Dietética IA...</p>
                </div>
                
                <div x-show="!loadingMeal" class="prose prose-emerald max-w-none">
                    <div class="bg-zinc-950 p-8 rounded-[2rem] border border-zinc-800 leading-relaxed text-zinc-400 text-sm italic whitespace-pre-line shadow-inner" x-text="mealSuggestion"></div>
                </div>
            </div>

            <div class="p-10 bg-zinc-950/50 border-t border-zinc-800 flex gap-4">
                <x-premium-button variant="secondary" class="flex-1" @click="suggestionModalOpen = false">FECHAR</x-premium-button>
                <x-premium-button variant="primary" class="flex-1" @click="adoptSuggestedMeal()">ADOTAR REFEIÇÃO</x-premium-button>
            </div>
        </div>
    </div>

    <!-- AI Audit Modal -->
    <div x-show="auditModalOpen" 
         class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-2xl animate-fade-in"
         style="display: none;"
         @keydown.escape.window="auditModalOpen = false">
        <div class="absolute inset-0" @click="auditModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-3xl rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between bg-emerald-500/5">
                <h5 class="text-white font-black text-2xl uppercase tracking-tighter flex items-center gap-4">
                    <i data-lucide="microscope" class="w-6 h-6 text-emerald-500"></i>
                    Weekly Performance Audit
                </h5>
                <button @click="auditModalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-10 max-h-[60vh] overflow-y-auto">
                <div x-show="loadingAudit" class="flex flex-col items-center justify-center py-16 space-y-12">
                    <x-ai-core size="md" />
                    <div class="text-center">
                        <p class="text-white font-black uppercase text-base tracking-widest mb-2 animate-pulse">Auditando Bio-indicadores</p>
                        <p class="text-zinc-600 text-[10px] uppercase font-bold tracking-widest">Calculando desvios e padrões dos últimos 7 dias...</p>
                    </div>
                </div>
                
                <div x-show="!loadingAudit" class="prose prose-emerald max-w-none">
                    <div class="bg-zinc-950 p-10 rounded-[2.5rem] border border-zinc-800 leading-relaxed text-zinc-400 text-sm italic whitespace-pre-line shadow-inner" x-text="auditResult"></div>
                </div>
            </div>

            <div class="p-10 bg-zinc-950/50 border-t border-zinc-800">
                <x-premium-button variant="primary" class="w-full" @click="auditModalOpen = false">ENTENDIDO, VAMOS EVOLUIR</x-premium-button>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div x-show="confirmDeleteModalOpen" 
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-3xl animate-fade-in"
         style="display: none;"
         x-cloak>
        <div class="absolute inset-0" @click="confirmDeleteModalOpen = false"></div>
        <div class="relative bg-zinc-900 border border-zinc-800 w-full max-w-sm rounded-[3rem] overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="p-10 text-center">
                <div class="w-24 h-24 bg-rose-500/10 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-8 border border-rose-500/20 shadow-lg">
                    <i data-lucide="trash-2" class="w-8 h-8"></i>
                </div>
                <h3 class="text-white font-black text-2xl mb-4 tracking-tighter uppercase">Confirmar Exclusão</h3>
                <p class="text-zinc-500 text-sm leading-relaxed mb-10 font-medium">
                    Tem certeza que deseja remover <strong class="text-white" x-text="deleteItemName"></strong>? Esta ação é irreversível.
                </p>
                
                <div class="flex flex-col gap-4">
                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-premium-button variant="primary" class="w-full !bg-rose-600 !border-rose-500 hover:!bg-rose-500" type="submit">REMOVER PERMANENTEMENTE</x-premium-button>
                    </form>
                    <x-premium-button variant="secondary" class="w-full" @click="confirmDeleteModalOpen = false">CANCELAR</x-premium-button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    // Calorie Trend Chart
    const trendEl = document.getElementById('calorieTrendChart');
    if (trendEl) {
        const trendCtx = trendEl.getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($historyData->map(fn($d) => \Carbon\Carbon::parse($d->entry_date)->format('d/m'))) !!},
                datasets: [{
                    label: 'Real',
                    data: {!! json_encode($historyData->pluck('total_cal')) !!},
                    borderColor: '#10b981',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#0c0f16',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }, {
                    label: 'Meta',
                    data: Array({{ (int)$historyData->count() ?: 1 }}).fill({{ (int)$targetKcal }}),
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 2,
                    borderDash: [10, 5],
                    pointRadius: 0,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.02)', drawBorder: false },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' }, padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' }, padding: 10 }
                    }
                }
            }
        });
    }

    // Macro Donut Chart
    const donutEl = document.getElementById('macroDonutChart');
    if (donutEl) {
        const donutCtx = donutEl.getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Proteínas', 'Carbos', 'Gorduras'],
                datasets: [{
                    data: [{{ (int)($macroTargets['p'] ?? 0) }}, {{ (int)($macroTargets['c'] ?? 0) }}, {{ (int)($macroTargets['f'] ?? 0) }}],
                    backgroundColor: ['#f43f5e', '#10b981', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 15,
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '82%',
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0c0f16',
                        titleFont: { size: 12, weight: 'black' },
                        bodyFont: { size: 12, weight: 'bold' },
                        padding: 15,
                        displayColors: false,
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                }
            }
        });
    }
</script>
@endpush

<style>
    body { background-color: #0c0f16; }
    [x-cloak] { display: none !important; }
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
