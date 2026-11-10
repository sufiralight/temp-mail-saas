@props(['title', 'wide' => false, 'setup' => false])

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
<body class="min-h-dvh bg-zinc-50 font-sans text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-50">
    <a class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-10 focus:rounded-lg focus:bg-white focus:px-3 focus:py-2 focus:text-zinc-950" href="#main">Skip to content</a>
    <header class="border-b border-zinc-200/80 bg-white dark:border-zinc-800 dark:bg-zinc-950">
        <div @class(['mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 sm:px-8', 'min-h-12 sm:min-h-14' => $setup, 'min-h-16' => ! $setup])>
            <a href="/" class="flex shrink-0 items-center gap-2.5 rounded-lg text-sm font-bold tracking-[-0.025em]">
                <span class="grid size-8 place-items-center rounded-[9px] bg-indigo-600 text-white dark:bg-indigo-500">
                    <x-icon name="mail" class="size-[18px]" />
                </span>
                Temp Mail
            </a>
            <div class="flex items-center gap-2 sm:gap-4">
                {{ $headerActions ?? '' }}
                <button type="button" data-theme-toggle hidden aria-label="Dark mode" aria-pressed="false" class="grid size-10 place-items-center rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-900">
                    <x-icon name="moon" class="size-[17px] dark:hidden" />
                    <x-icon name="sun" class="hidden size-[17px] dark:block" />
                </button>
            </div>
        </div>
    </header>
    <main id="main" tabindex="-1" @class([
        'mx-auto w-full px-4 outline-none sm:px-6',
        'max-w-5xl' => $wide,
        'max-w-[38rem]' => ! $wide && ! $setup,
        'setup-page max-w-[52rem]' => $setup,
        'pt-8 pb-12 sm:pt-11' => ! $setup,
    ])>
        {{ $slot }}
    </main>
</body>
</html>
