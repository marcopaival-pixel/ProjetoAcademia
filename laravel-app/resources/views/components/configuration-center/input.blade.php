@props([
    'field',
    'value' => null,
])

<div class="mb-4">
    <label for="{{ $field->name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $field->label }}
        @if($field->is_required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div class="mt-1">
        @switch($field->type)
            @case('textarea')
                <textarea
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm"
                    placeholder="{{ $field->placeholder }}"
                    {{ $field->is_required ? 'required' : '' }}
                    {{ $field->is_readonly ? 'readonly' : '' }}
                >{{ old($field->name, $value) }}</textarea>
                @break

            @case('select')
                <select
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm"
                    {{ $field->is_required ? 'required' : '' }}
                >
                    <option value="">Selecione...</option>
                    @if($field->options && isset($field->options['choices']))
                        @foreach($field->options['choices'] as $val => $label)
                            <option value="{{ $val }}" {{ old($field->name, $value) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    @endif
                </select>
                @break

            @case('boolean')
            @case('toggle')
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="{{ $field->name }}"
                        name="{{ $field->name }}"
                        value="1"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700"
                        {{ old($field->name, $value) ? 'checked' : '' }}
                    >
                    <label for="{{ $field->name }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Ativo / Sim
                    </label>
                </div>
                @break

            @case('number')
            @case('decimal')
                <input
                    type="number"
                    step="{{ $field->type === 'decimal' ? '0.01' : '1' }}"
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    value="{{ old($field->name, $value) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm"
                    placeholder="{{ $field->placeholder }}"
                    {{ $field->is_required ? 'required' : '' }}
                    {{ $field->is_readonly ? 'readonly' : '' }}
                >
                @break

            @default
                <input
                    type="{{ $field->type }}"
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    value="{{ old($field->name, $value) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm"
                    placeholder="{{ $field->placeholder }}"
                    {{ $field->is_required ? 'required' : '' }}
                    {{ $field->is_readonly ? 'readonly' : '' }}
                >
        @endswitch
    </div>

    @if($field->help_text)
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $field->help_text }}</p>
    @endif

    @error($field->name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
