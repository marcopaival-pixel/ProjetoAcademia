@php
    $bannerService = app(\App\Services\MarketingBannerService::class);
    $banner = auth()->check() ? $bannerService->getActiveBannerForUser(auth()->user()) : null;
@endphp

@if($banner)
<div x-data="{ 
        show: true,
        loading: false,
        async track(event, metadata = {}) {
            try {
                const routes = {
                    view: '{{ route('api.marketing.banners.view', $banner) }}',
                    click: '{{ route('api.marketing.banners.click', $banner) }}',
                    dismiss: '{{ route('api.marketing.banners.dismiss', $banner) }}'
                };
                
                await fetch(routes[event], {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(metadata)
                });
            } catch (e) {
                console.error('Failed to track banner event:', e);
            }
        },
        dismiss(dontShowAgain = false) {
            this.show = false;
            this.track('dismiss', { dont_show_again: dontShowAgain });
        },
        click(type) {
            this.track('click', { button_type: type });
        }
    }" 
    x-init="track('view')"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="relative w-full mb-8"
>
    <!-- Background Glass Card -->
    <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 shadow-2xl transition-all duration-500 hover:shadow-blue-600/10"
         style="background-color: {{ $banner->background_color }};">
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[80px] -mr-32 -mt-32 rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-600/5 blur-[60px] -ml-24 -mb-24 rounded-full"></div>

        <div class="relative px-8 py-10 md:px-12 md:py-12 flex flex-col md:flex-row items-center gap-10">
            
            <!-- Image Section -->
            @if($banner->image_desktop)
            <div class="w-full md:w-1/3 flex justify-center order-2 md:order-1">
                <img src="{{ $banner->image_desktop }}" 
                     class="w-full max-w-[280px] h-auto object-contain transform hover:scale-105 transition-transform duration-700 drop-shadow-2xl" 
                     alt="{{ $banner->title }}">
            </div>
            @endif

            <!-- Content Section -->
            <div class="flex-1 space-y-6 text-center md:text-left order-1 md:order-2">
                <div class="space-y-2">
                    @if($banner->subtitle)
                        <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 text-white text-[10px] font-black uppercase tracking-[0.2em] backdrop-blur-md border border-white/10">
                            {{ $banner->subtitle }}
                        </span>
                    @endif
                    <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                        @if($banner->icon) <i class="{{ $banner->icon }} mr-2 text-blue-400"></i> @endif
                        {{ $banner->title }}
                    </h2>
                    @if($banner->description)
                        <p class="text-zinc-400 text-base md:text-lg font-medium leading-relaxed max-w-2xl">
                            {{ $banner->description }}
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-2">
                    @if($banner->primary_button_text)
                    <a href="{{ $banner->primary_button_link }}" 
                       @click="click('primary')"
                       class="px-8 py-4 bg-white text-black font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-blue-500 hover:text-white transition-all shadow-xl shadow-white/5 active:scale-95">
                        {{ $banner->primary_button_text }}
                    </a>
                    @endif

                    @if($banner->secondary_button_text)
                    <a href="{{ $banner->secondary_button_link }}" 
                       @click="click('secondary')"
                       class="px-8 py-4 bg-white/5 text-white border border-white/10 font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-white/10 transition-all active:scale-95 backdrop-blur-md">
                        {{ $banner->secondary_button_text }}
                    </a>
                    @endif
                </div>
            </div>

            <!-- Dismiss Button -->
            @if($banner->allow_dismiss)
            <div class="absolute top-6 right-6 flex flex-col items-end gap-2">
                <button @click="dismiss(false)" class="w-10 h-10 rounded-full bg-black/20 text-white/40 hover:text-white hover:bg-black/40 transition-all flex items-center justify-center border border-white/5 backdrop-blur-md">
                    <i class="fas fa-times"></i>
                </button>
                
                @if($banner->dont_show_again_option)
                <button @click="dismiss(true)" class="text-[9px] font-black text-white/20 hover:text-white/60 uppercase tracking-widest transition-all">
                    Não mostrar novamente
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endif
