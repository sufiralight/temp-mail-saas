<x-account-shell title="Choose a new password" intro="Set a new password for your Temp Mail account.">
    <form method="post" action="{{ route('password.update', absolute: false) }}" class="grid gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" label="Email address" type="email" :value="$email" autocomplete="username" required />
        <x-field name="password" label="New password" type="password" autocomplete="new-password" hint="Use at least 12 characters." required />
        <x-field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required />
        <x-account-submit>Reset password</x-account-submit>
    </form>
    <x-slot:footer>
        <a class="link" href="{{ route('password.request', absolute: false) }}">Request a new link</a>
    </x-slot:footer>
</x-account-shell>
