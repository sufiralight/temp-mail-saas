<x-shell title="Workspace setup" :setup="true">
    <x-workspace-steps :step="1" />

    <div class="grid gap-1.5">
        <h1 class="setup-title">Set up your Temp Mail workspace</h1>
        @if ($errors->has('connection'))
            <x-form-feedback />
        @else
            <p class="body-copy sm:text-base">Connect a MySQL database to store your workspace settings and accounts.</p>
        @endif
    </div>

    <section class="surface" aria-labelledby="database-heading">
        <div class="mb-4 flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                <x-icon name="database" class="size-[17px]" />
            </span>
            <h2 id="database-heading" class="text-lg font-semibold tracking-tight">Database connection</h2>
        </div>

        @unless ($errors->has('connection'))
            <x-form-feedback />
        @endunless

        @if ($verified)
            <div class="flex items-start gap-3 rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-indigo-950 dark:border-indigo-900 dark:bg-indigo-950/50 dark:text-indigo-100">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200">
                    <x-icon name="check" class="size-4" />
                </span>
                <div role="status">
                    <h3 class="text-sm font-semibold">Database connected</h3>
                    <p class="mt-1 text-sm/5 text-indigo-900 dark:text-indigo-200">Your workspace can connect to MySQL. Continue to create your admin account.</p>
                </div>
            </div>
            <dl class="my-5 grid gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 sm:grid-cols-[1fr_auto_1fr] dark:border-zinc-800 dark:bg-zinc-950/50">
                @foreach (['host' => 'Host', 'port' => 'Port', 'database' => 'Database name'] as $key => $label)
                    <div class="min-w-0">
                        <dt class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-sm font-semibold wrap-anywhere">{{ $verified[$key] }}</dd>
                    </div>
                @endforeach
            </dl>
            <div class="grid gap-2.5 sm:grid-cols-[1fr_auto]">
                <a class="primary" href="{{ route('install.admin', absolute: false) }}">
                    Continue to admin setup <x-icon name="arrow" class="size-4" />
                </a>
                <form method="post" action="{{ route('install.edit', absolute: false) }}">
                    @csrf
                    <button class="secondary" type="submit">Edit connection</button>
                </form>
            </div>
        @else
            @if ($interrupted)
                <p class="body-copy mb-5 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    Workspace setup was interrupted. Run <code class="wrap-anywhere">php artisan workspace:recover</code> in the application terminal, then reload this page.
                </p>
            @elseif (! $canWrite)
                <p role="status" class="body-copy mb-5 flex items-start gap-2.5 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <x-icon name="lock" class="mt-0.5 size-4" />
                    <span>Workspace setup is locked. {{ app()->isProduction() ? 'Open this page over HTTPS and sign in through your hosting provider’s access control.' : 'Open this page from an authorized address to continue.' }}</span>
                </p>
            @endif

            @if (app()->isProduction() && $canWrite)
                <p class="body-copy mb-5">In your hosting panel, create an empty MySQL database and assign a database user with permission to create tables and write records.</p>
            @endif

            <form method="post" action="{{ route('install.verify', absolute: false) }}" data-pending-form="connection-status">
                @csrf
                <fieldset @disabled(! $canWrite) class="grid min-w-0 gap-4 short-desktop:grid-cols-[minmax(0,1fr)_8rem_minmax(0,1fr)] short-desktop:gap-y-3">
                    <div class="grid gap-4 min-[390px]:grid-cols-[minmax(0,1fr)_6rem] sm:grid-cols-[minmax(0,1fr)_9rem] short-desktop:contents">
                        <x-field name="host" label="Host" :value="app()->isLocal() ? '127.0.0.1' : null" placeholder="mysql.example.com" autocomplete="off" spellcheck="false" required />
                        <x-field name="port" label="Port" value="3306" type="number" inputmode="numeric" min="1" max="65535" required />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 short-desktop:contents">
                        <x-field name="database" label="Database name" autocomplete="off" spellcheck="false" required />
                        <x-field name="username" label="Database username" autocomplete="off" autocapitalize="none" spellcheck="false" required />
                    </div>
                    <div class="min-w-0 short-desktop:col-span-2">
                        <x-field name="password" label="Database password" type="password" autocomplete="off"
                            :required="! app()->isLocal()"
                            :hint="app()->isLocal() ? 'You can leave this blank if your local MySQL user has no password.' : 'Enter the password for the MySQL user assigned to this database.'" />
                    </div>
                    <button class="primary short-desktop:col-span-full" type="submit" data-pending-label="Checking connection…">
                        <span hidden data-spinner aria-hidden="true" class="size-4 rounded-full border-2 border-white/40 border-t-white motion-safe:animate-spin"></span>
                        <span data-submit-label>Verify connection</span>
                    </button>
                </fieldset>
            </form>
            <p id="connection-status" class="sr-only" role="status"></p>
        @endif
    </section>
</x-shell>
