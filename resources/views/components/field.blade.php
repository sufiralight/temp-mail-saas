@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null])

@php
    $invalid = $errors->has($name);
    $description = trim(($hint ? $name.'-hint ' : '').($invalid ? $name.'-error' : ''));
@endphp

<div class="grid min-w-0 gap-1.5">
    <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
        <label for="{{ $name }}" class="text-sm font-semibold">{{ $label }}</label>
        {{ $action ?? '' }}
    </div>
    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            @if($type !== 'password') value="{{ is_scalar(old($name, $value)) ? old($name, $value) : '' }}" @endif
            @if($description) aria-describedby="{{ $description }}" @endif
            @if($invalid) aria-invalid="true" @endif
            {{ $attributes->class(['field', 'pr-12' => $type === 'password']) }}
        >
        @if($type === 'password')
            <button type="button" data-password-toggle hidden aria-controls="{{ $name }}" aria-label="{{ $label }} visibility" aria-pressed="false" class="group absolute inset-y-0 right-0 grid w-11 place-items-center rounded-lg text-zinc-600 hover:text-zinc-700 focus-visible:outline-offset-[-4px] dark:text-zinc-300 dark:hover:text-zinc-300">
                <x-icon name="eye" class="group-aria-pressed:hidden" />
                <x-icon name="eye-off" class="hidden group-aria-pressed:block" />
            </button>
        @endif
    </div>
    @if($hint)
        <p id="{{ $name }}-hint" class="text-xs/4 text-zinc-600 dark:text-zinc-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="field-error text-sm">{{ $message }}</p>
    @enderror
</div>
