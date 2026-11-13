<x-account-shell title="Sign up" intro="Create your Temp Mail account to access the workspace.">
    <form method="post" action="{{ route('register.store', absolute: false) }}" class="grid gap-5">
        @csrf
        <x-field name="name" label="Full name" autocomplete="name" required />
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <x-field name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 12 characters." required />
        <x-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required />
        <x-account-submit>Create account</x-account-submit>
    </form>
    <x-slot:footer>
        Already have an account? <a class="link" href="{{ route('login', absolute: false) }}">Sign in</a>
    </x-slot:footer>
</x-account-shell>
