<x-account-shell title="Sign in" intro="Open your Temp Mail workspace dashboard.">
    <form method="post" action="{{ route('login.store', absolute: false) }}" class="grid gap-4">
        @csrf
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <x-field name="password" label="Password" type="password" autocomplete="current-password" required />
        <button type="submit" class="primary mt-1">Sign in <x-icon name="arrow" class="size-4" /></button>
    </form>
    <a class="link mt-4 inline-flex text-sm" href="{{ route('password.request', absolute: false) }}">Forgot your password?</a>
    <p class="body-copy mt-5 border-t border-zinc-200 pt-4 dark:border-zinc-800">
        New to Temp Mail? <a class="link" href="{{ route('register', absolute: false) }}">Sign up</a>
    </p>
</x-account-shell>
