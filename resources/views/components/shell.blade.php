@props(['title', 'wide' => false, 'setup' => false, 'sidebar' => false, 'account' => false])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <title>{{ $title }} · Temp Mail</title>
    <link rel="icon" href="{{ asset('temp-mail.svg') }}" type="image/svg+xml">
    <script>
        (() => {
            let theme;
            try { theme = localStorage.getItem('temp-mail-theme'); } catch {}
            document.documentElement.dataset.theme = theme === 'dark' || theme === 'light'
                ? theme
                : (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(["app-body min-h-dvh font-sans antialiased", "account-page" => $account])>
    <a class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-10 focus:rounded-lg focus:bg-zinc-50 focus:px-3 focus:py-2 focus:text-zinc-950" href="#main">Skip to content</a>
    <header class="app-header border-b">
        <div @class([
            'mx-auto flex items-center justify-between gap-3 px-4',
            'max-w-6xl sm:px-8' => ! $sidebar,
            'sm:px-6 lg:pl-0 lg:pr-8' => $sidebar,
            'min-h-12 sm:min-h-14' => $setup,
            'min-h-16' => ! $setup,
        ])>
            <x-brand @class([
                'rounded-lg' => ! $sidebar,
                'brand-sidebar lg:min-h-16 lg:w-56 lg:border-r lg:px-5' => $sidebar,
            ]) />
            <div class="flex items-center gap-2">
                <x-theme-control />
                {{ $headerActions ?? '' }}
            </div>
        </div>
    </header>
    <main id="main" tabindex="-1" @class([
        'mx-auto outline-none',
        'w-full px-4 sm:px-6' => ! $account,
        'max-w-5xl' => $wide,
        'max-w-[38rem]' => ! $wide && ! $setup && ! $account,
        'setup-page max-w-[52rem]' => $setup,
        'pt-8 pb-12 sm:pt-11' => ! $setup && ! $account,
        'account-main' => $account,
        'lg:max-w-none lg:p-0' => $sidebar,
    ])>
        {{ $slot }}
    </main>
</body>
</html>
