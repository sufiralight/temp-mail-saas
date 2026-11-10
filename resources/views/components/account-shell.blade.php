@props(['title', 'intro'])

<x-shell :title="$title">
    <div class="mx-auto max-w-md sm:pt-7">
        <div class="mb-6">
            <h1 class="text-[1.875rem]/[1.12] font-semibold tracking-[-0.045em] sm:text-[2.25rem]/[1.12]">{{ $title }}</h1>
            <p class="body-copy mt-3">{{ $intro }}</p>
        </div>
        <div class="surface">
            <x-form-feedback />
            {{ $slot }}
        </div>
    </div>
</x-shell>
