@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null])

@php
    $invalid = $errors->has($name);
    $description = trim(($hint ? $name.'-hint ' : '').($invalid ? $name.'-error' : ''));
@endphp

<div class="grid min-w-0 gap-1.5">
    <label for="{{ $name }}" class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $label }}</label>
    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            @if($type !== 'password') value="{{ is_scalar(old($name, $value)) ? old($name, $value) : '' }}" @endif
            @if($description) aria-describedby="{{ $description }}" @endif
            @if($invalid) aria-invalid="true" @endif
            {{ $attributes->class(['field', 'pr-12' => $type === 'password', 'border-indigo-600! dark:border-indigo-400!' => $invalid]) }}
        >
        @if($type === 'password')
            <button type="button" data-password-toggle hidden aria-controls="{{ $name }}" aria-label="Show {{ strtolower($label) }}" aria-pressed="false" class="group absolute inset-y-0 right-0 grid w-11 place-items-center rounded-lg text-zinc-600 hover:text-indigo-700 focus-visible:outline-offset-[-4px] dark:text-zinc-300 dark:hover:text-indigo-300">
                <x-icon name="eye" class="group-aria-pressed:hidden" />
                <x-icon name="eye-off" class="hidden group-aria-pressed:block" />
            </button>
        @endif
    </div>
    @if($hint)
        <p id="{{ $name }}-hint" class="text-xs/4 text-zinc-600 dark:text-zinc-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="text-xs/4 font-medium text-zinc-800 dark:text-zinc-200">{{ $message }}</p>
    @enderror
</div>
