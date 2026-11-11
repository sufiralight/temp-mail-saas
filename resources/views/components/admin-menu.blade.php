<nav aria-label="Admin menu" class="grid gap-1">
    <a href="{{ route('dashboard', absolute: false) }}" aria-current="page" class="flex min-h-11 items-center gap-3 rounded-lg bg-indigo-50 px-3 text-sm font-semibold text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-200">
        <x-icon name="dashboard" class="size-[18px]" />
        Dashboard
    </a>
    <form method="post" action="{{ route('logout', absolute: false) }}" class="mt-2 border-t border-zinc-200 pt-2 dark:border-zinc-800">
        @csrf
        <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
            <x-icon name="sign-out" class="size-[18px]" />
            Sign out
        </button>
    </form>
</nav>
