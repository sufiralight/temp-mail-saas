@props(['title', 'intro'])

<x-shell :title="$title" account>
    <section class="account-content" aria-labelledby="account-title">
        <div class="mb-8 grid gap-2">
            <h1 id="account-title" class="account-title">{{ $title }}</h1>
            <p class="body-copy">{{ $intro }}</p>
        </div>

        <x-form-feedback />
        {{ $slot }}

        @isset($footer)
            <div class="account-footer">{{ $footer }}</div>
        @endisset
    </section>
</x-shell>
