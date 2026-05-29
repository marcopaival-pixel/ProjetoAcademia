@props([
    'label' => null,
    'icon' => null,
    'error' => null,
    'type' => 'text',
    'id' => null,
    'placeholder' => null,
    'value' => null
])

<div class="space-y-2 group w-full">
    @if($label)
        <label for="{{ $id }}" class="text-sm font-semibold text-zinc-400 ml-1 transition-colors group-focus-within:text-emerald-500">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 transition-colors group-focus-within:text-emerald-500">
                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => "w-full bg-zinc-900 border " . ($error ? 'border-red-500/50' : 'border-zinc-800') . " rounded-2xl " . ($icon ? 'pl-12' : 'pl-5') . " pr-5 py-4 text-white placeholder:text-zinc-700 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-inner"
            ]) }}
        >
    </div>

    @if($error)
        <p class="text-xs text-red-500 ml-1 mt-1 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i>
            {{ $error }}
        </p>
    @endif
</div>
