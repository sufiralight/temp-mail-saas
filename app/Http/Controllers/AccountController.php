<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'regex:/\A[^\x00]{1,72}\z/', 'confirmed', PasswordRule::min(12)],
        ]);

        try {
            $user = User::create($attributes);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => __('validation.unique', ['attribute' => 'email'])]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('dashboard');
    }

    public function signIn(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'regex:/\A[^\x00]{1,72}\z/'],
        ]);

        if (! Auth::attempt($credentials)) {
            return to_route('login')
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'The email address or password is incorrect.']);
        }

        $request->session()->regenerate();

        return to_route('dashboard');
    }

    public function signOut(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $email = $request->validate(['email' => ['required', 'email', 'max:255']]);
        Password::sendResetLink($email);

        return to_route('password.request')
            ->with('status', 'If an account matches that email address, you’ll receive a password reset link.');
    }

    public function resetForm(Request $request, string $token): View
    {
        return view('account.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'regex:/\A[^\x00]{1,72}\z/', 'confirmed', PasswordRule::min(12)],
        ]);

        $status = DB::transaction(function () use ($credentials): string {
            DB::table(config('auth.passwords.users.table'))
                ->where('email', $credentials['email'])
                ->lockForUpdate()
                ->first();

            return Password::reset($credentials, function (User $user, string $password): void {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            });
        });

        if ($status !== Password::PasswordReset) {
            return to_route('password.reset', ['token' => $credentials['token'], 'email' => $credentials['email']])
                ->withErrors(['email' => 'This password reset link is invalid or has expired. Request a new link.']);
        }

        return to_route('login')->with('status', 'Your password has been reset. Sign in with your new password.');
    }
}
