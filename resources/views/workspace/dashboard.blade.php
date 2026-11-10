<x-shell title="Dashboard" :wide="true">
    <x-slot:headerActions>
        <form method="post" action="{{ route('logout', absolute: false) }}">
            @csrf
            <button type="submit" class="secondary min-h-10 px-3">Sign out</button>
        </form>
    </x-slot:headerActions>

    <div class="mb-7">
        <p class="mb-2 text-[11px] font-semibold tracking-[0.14em] text-zinc-600 uppercase dark:text-zinc-400">Workspace</p>
        <h1 class="text-3xl font-semibold tracking-[-0.045em] sm:text-4xl">Dashboard</h1>
        <p class="body-copy mt-2 wrap-anywhere">Signed in as {{ auth()->user()->name }}</p>
    </div>
    <section class="surface flex min-h-56 flex-col items-start justify-center gap-3 sm:items-center sm:text-center" aria-labelledby="inbox-heading">
        <span class="grid size-11 place-items-center rounded-xl border border-indigo-100 bg-indigo-50 text-indigo-700 dark:border-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-300">
            <x-icon name="inbox" />
        </span>
        <h2 id="inbox-heading" class="text-lg font-semibold tracking-tight">Inbox</h2>
        <p class="body-copy max-w-sm">Your inbox isn’t available yet. Temporary addresses and incoming messages will appear here once inbox features are available.</p>
    </section>
</x-shell>
