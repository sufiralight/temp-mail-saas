<x-account-shell title="Reset your password" intro="Enter your workspace email address to request a password reset link.">
    <form method="post" action="{{ route('password.email', absolute: false) }}" class="grid gap-5">
        @csrf
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <x-account-submit>Send reset link</x-account-submit>
    </form>
    <x-slot:footer>
        <a class="link" href="{{ route('login', absolute: false) }}">Back to sign in</a>
    </x-slot:footer>
</x-account-shell>
