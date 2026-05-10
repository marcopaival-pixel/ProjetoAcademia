@props(['text', 'position' => 'top'])

<div x-data="{ show: false }" class="relative inline-block ml-2">
    <button @mouseenter="show = true" @mouseleave="show = false" type="button" class="text-zinc-600 hover:text-blue-500 transition-colors">
        <i class="fas fa-question-circle text-xs"></i>
    </button>
    
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-[100] w-64 p-4 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl text-[11px] text-zinc-400 leading-relaxed pointer-events-none"
         :class="{
            'bottom-full left-1/2 -translate-x-1/2 mb-3': '{{ $position }}' === 'top',
            'top-full left-1/2 -translate-x-1/2 mt-3': '{{ $position }}' === 'bottom',
            'left-full top-1/2 -translate-y-1/2 ml-3': '{{ $position }}' === 'right',
            'right-full top-1/2 -translate-y-1/2 mr-3': '{{ $position }}' === 'left',
         }">
        <div class="relative">
            {{ $text }}
            <!-- Arrow -->
            <div class="absolute w-2 h-2 bg-zinc-900 border-r border-b border-white/10 rotate-45"
                 :class="{
                    'bottom-[-20px] left-1/2 -translate-x-1/2 border-t-0 border-l-0': '{{ $position }}' === 'top',
                    'top-[-20px] left-1/2 -translate-x-1/2 border-b-0 border-r-0': '{{ $position }}' === 'bottom',
                 }"></div>
        </div>
    </div>
</div>
