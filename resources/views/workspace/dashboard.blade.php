@php($isAdministrator = auth()->user()->can('administer-workspace'))

<x-shell title="Dashboard" :wide="true" :sidebar="$isAdministrator">
    <x-slot:headerActions>
        @unless ($isAdministrator)
            <form method="post" action="{{ route('logout', absolute: false) }}">
                @csrf
                <button type="submit" class="secondary min-h-10 px-3">Sign out</button>
            </form>
        @endunless
    </x-slot>

    <div @class(['grid gap-5 lg:min-h-[calc(100dvh-4rem-1px)] lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-0' => $isAdministrator])>
        @if ($isAdministrator)
            <aside class="hidden border-r border-zinc-200 bg-white px-3 py-6 lg:block dark:border-zinc-800 dark:bg-zinc-900">
                <p class="px-3 pt-2 pb-3 text-[11px] font-semibold tracking-[0.14em] text-zinc-600 uppercase dark:text-zinc-400">Admin menu</p>
                <x-admin-menu />
            </aside>
            <details class="group rounded-xl border border-zinc-200 bg-white lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
                <summary class="flex min-h-11 cursor-pointer list-none items-center gap-3 rounded-xl px-3 text-sm font-semibold text-zinc-800 dark:text-zinc-200 [&::-webkit-details-marker]:hidden">
                    <x-icon name="menu" class="size-[18px]" />
                    Admin menu
                    <x-icon name="chevron-down" class="ml-auto size-[18px] group-open:rotate-180" />
                </summary>
                <div class="border-t border-zinc-200 p-2 dark:border-zinc-800">
                    <x-admin-menu />
                </div>
            </details>
        @endif

        <div @class(['min-w-0', 'lg:px-8 lg:pt-8 lg:pb-12' => $isAdministrator])>
            <div class="mb-7">
                <p class="mb-2 text-[11px] font-semibold tracking-[0.14em] text-zinc-600 uppercase dark:text-zinc-400">Workspace</p>
                <h1 class="text-3xl font-semibold tracking-[-0.045em] sm:text-4xl">Dashboard</h1>
                <p class="body-copy mt-2 wrap-anywhere">Signed in as {{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
</x-shell>
