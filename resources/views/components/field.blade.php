@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null])

@php
    $invalid = $errors->has($name);
    $description = trim(($hint ? $name.'-hint ' : '').($invalid ? $name.'-error' : ''));
@endphp

<div class="field-group grid min-w-0 gap-1">
    <label for="{{ $name }}" class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $label }}</label>
    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            @if($type !== 'password') value="{{ is_scalar(old($name, $value)) ? old($name, $value) : '' }}" @endif
            @if($description) aria-describedby="{{ $description }}" @endif
            @if($invalid) aria-invalid="true" @endif
            {{ $attributes->class(['field', 'pr-17' => $type === 'password', 'border-indigo-600! dark:border-indigo-400!' => $invalid]) }}
        >
        @if($type === 'password')
            <button type="button" data-password-toggle hidden aria-controls="{{ $name }}" aria-label="Show {{ strtolower($label) }}" aria-pressed="false" class="absolute inset-y-0.5 right-1.5 min-w-12 rounded-md px-2 text-xs font-semibold text-zinc-600 hover:text-indigo-700 dark:text-zinc-300 dark:hover:text-indigo-300">Show</button>
        @endif
    </div>
    @if($hint)
        <p id="{{ $name }}-hint" class="text-xs/4 text-zinc-600 dark:text-zinc-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="text-xs/4 font-medium text-zinc-800 dark:text-zinc-200">{{ $message }}</p>
    @enderror
</div>
