@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'focusable' => false,
])

@php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
    ][$maxWidth] ?? 'sm:max-w-2xl';
@endphp

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close.window="show = false"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;"
>
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-black/75 backdrop-blur-sm"
        x-on:click="show = false"
    ></div>

    <div
        x-show="show"
        x-transition
        class="mb-6 transform overflow-hidden rounded-[2rem] bg-zinc-900 border border-white/10 shadow-2xl transition-all sm:mx-auto sm:w-full {{ $maxWidthClass }}"
        {{ $focusable ? 'tabindex=-1' : '' }}
    >
        {{ $slot }}
    </div>
</div>
