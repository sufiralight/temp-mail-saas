<x-account-shell title="Reset your password" intro="Enter your workspace email address to request a password reset link.">
    <form method="post" action="{{ route('password.email', absolute: false) }}" class="grid gap-4">
        @csrf
        <x-field name="email" label="Email address" type="email" autocomplete="username" required />
        <button type="submit" class="primary mt-1">Send reset link</button>
    </form>
    <a class="link mt-4 inline-flex text-sm" href="{{ route('login', absolute: false) }}">Back to sign in</a>
</x-account-shell>
