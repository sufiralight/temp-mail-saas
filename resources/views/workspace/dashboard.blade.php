@php($isAdministrator = auth()->user()->can('administer-workspace'))
@php($account = auth()->user())

<x-shell title="Dashboard" :wide="true" :sidebar="$isAdministrator">
    <x-slot:headerActions>
        <details class="group relative">
            <summary aria-label="Account menu" class="grid size-10 cursor-pointer list-none place-items-center rounded-lg border border-zinc-200 text-zinc-700 hover:bg-zinc-100 group-open:bg-zinc-100 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:group-open:bg-zinc-900 [&::-webkit-details-marker]:hidden">
                <span aria-hidden="true" class="grid size-7 place-items-center rounded-full bg-zinc-50 text-xs font-semibold text-zinc-800 dark:bg-zinc-950 dark:text-zinc-200">{{ mb_substr(\Illuminate\Support\Str::initials($account->name, true), 0, 2) }}</span>
            </summary>
            <div class="absolute top-full right-0 z-20 mt-2 w-64 max-w-[calc(100vw-2rem)] rounded-xl border border-(--line) bg-(--surface) p-1.5 shadow-(--elevation)">
                <div class="grid gap-0.5 border-b border-zinc-200 px-2.5 pt-2 pb-3 dark:border-zinc-800">
                    <p class="text-sm font-semibold wrap-anywhere">{{ $account->name }}</p>
                    <p class="text-xs wrap-anywhere text-zinc-600 dark:text-zinc-400">{{ $account->email }}</p>
                </div>
                <form method="post" action="{{ route('logout', absolute: false) }}" class="pt-1">
                    @csrf
                    <button type="submit" class="flex min-h-11 w-full items-center gap-2.5 rounded-lg px-2.5 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <x-icon name="sign-out" class="size-4" />
                        Sign out
                    </button>
                </form>
            </div>
        </details>
    </x-slot>

    <div @class(['grid gap-5 lg:min-h-[calc(100dvh-4rem-1px)] lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-0' => $isAdministrator])>
        @if ($isAdministrator)
            <aside class="hidden border-r border-(--line) bg-(--surface) px-3 py-5 lg:block">
                <x-admin-menu />
            </aside>
            <details class="group rounded-xl border border-(--line) bg-(--surface) lg:hidden">
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
            <h1 class="text-3xl font-semibold tracking-[-0.045em] sm:text-4xl">Dashboard</h1>
        </div>
    </div>
</x-shell>
