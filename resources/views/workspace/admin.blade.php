<x-shell title="Create admin account" :setup="true">
    <x-workspace-steps :step="2" />

    <div class="grid gap-1.5">
        <h1 class="setup-title">Create your workspace admin account</h1>
        <p class="body-copy sm:text-base">Use this account to manage your Temp Mail workspace.</p>
    </div>

    <section class="surface" aria-labelledby="admin-heading">
        <div class="mb-4 flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                <x-icon name="user" class="size-[17px]" />
            </span>
            <h2 id="admin-heading" class="text-lg font-semibold tracking-tight">Admin account</h2>
        </div>
        <x-form-feedback />
        <form method="post" action="{{ route('install.finish', absolute: false) }}" class="grid gap-4 sm:grid-cols-2" data-pending-form="setup-status">
            @csrf
            <x-field name="name" label="Full name" autocomplete="name" required />
            <x-field name="email" label="Email address" type="email" autocomplete="username" required />
            <x-field name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 12 characters." required />
            <x-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required />
            <button class="primary sm:col-span-2" type="submit" data-pending-label="Finishing workspace setup…">
                <span hidden data-spinner aria-hidden="true" class="size-4 rounded-full border-2 border-white/40 border-t-white motion-safe:animate-spin"></span>
                <span data-submit-label>Finish workspace setup</span>
            </button>
        </form>
        <p id="setup-status" class="sr-only" role="status"></p>
        <a class="link mt-4 inline-flex text-sm" href="{{ route('install', absolute: false) }}">Back to database connection</a>
    </section>
</x-shell>
