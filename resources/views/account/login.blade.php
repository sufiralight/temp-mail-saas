<x-account-shell title="Sign in" intro="Access your Temp Mail workspace.">
    <form method="post" action="{{ route('login.store', absolute: false) }}" class="grid gap-5" data-pending-form="login-status">
        @csrf
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <x-field name="password" label="Password" type="password" autocomplete="current-password" required>
            <x-slot:action>
                <a class="link text-sm" href="{{ route('password.request', absolute: false) }}">Forgot password?</a>
            </x-slot:action>
        </x-field>
        <x-account-submit pending="Signing in…">Sign in</x-account-submit>
        <span id="login-status" class="sr-only" role="status"></span>
    </form>

    <x-slot:footer>
        New to Temp Mail? <a class="link" href="{{ route('register', absolute: false) }}">Sign up</a>
    </x-slot:footer>
</x-account-shell>
