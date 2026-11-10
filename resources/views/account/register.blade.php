<x-account-shell title="Sign up" intro="Create your Temp Mail account to access the workspace.">
    <form method="post" action="{{ route('register.store', absolute: false) }}" class="grid gap-4">
        @csrf
        <x-field name="name" label="Full name" autocomplete="name" required />
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <x-field name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 12 characters." required />
        <x-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required />
        <button type="submit" class="primary mt-1">Create account <x-icon name="arrow" class="size-4" /></button>
    </form>
    <p class="body-copy mt-5 border-t border-zinc-200 pt-4 dark:border-zinc-800">
        Already have an account? <a class="link" href="{{ route('login', absolute: false) }}">Sign in</a>
    </p>
</x-account-shell>
