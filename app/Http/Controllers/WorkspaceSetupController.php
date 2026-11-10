<?php

namespace App\Http\Controllers;

use App\Support\WorkspaceInstallation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Password;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class WorkspaceSetupController extends Controller
{
    private const CONNECTION_ERROR = 'Temp Mail couldn’t connect to MySQL. Check the host, port, and database credentials, then try again.';

    public function __construct(private WorkspaceInstallation $installation) {}

    public function show(Request $request): View|RedirectResponse
    {
        if ($this->installation->ready()) {
            return to_route('dashboard');
        }

        $interrupted = $this->installation->interrupted();
        $canWrite = $this->canWrite($request) && ! $interrupted;
        $verified = $canWrite ? $this->verified($request) : null;

        return view('workspace.install', [
            'interrupted' => $interrupted,
            'canWrite' => $canWrite,
            'verified' => $verified ? Arr::except($verified, 'password') : null,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $this->authorizeWrite($request);
        $request->session()->forget('workspace.verification');

        $database = $request->validate([
            'host' => ['required', 'string', 'max:253', 'regex:/\A[a-zA-Z0-9_.:\[\]-]+\z/'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'database' => ['required', 'string', 'max:64', 'regex:/\A[^;\x00-\x1f\x7f]+\z/u'],
            'username' => ['required', 'string', 'max:128'],
            'password' => [app()->isLocal() ? 'nullable' : 'required', 'string', 'max:4096'],
        ]);

        try {
            $this->installation->verify($database);
        } catch (PDOException) {
            return to_route('install')
                ->withInput(Arr::except($database, 'password'))
                ->withErrors(['connection' => self::CONNECTION_ERROR]);
        }

        $request->session()->put('workspace.verification', [
            'origin' => $request->getSchemeAndHttpHost(),
            'expires' => now()->addMinutes(15)->timestamp,
            'database' => encrypt($database),
        ]);

        return to_route('install');
    }

    public function edit(Request $request): RedirectResponse
    {
        $this->authorizeWrite($request);
        $database = $this->verified($request) ?? [];
        $request->session()->forget('workspace.verification');

        return to_route('install')->withInput(Arr::except($database, 'password'));
    }

    public function admin(Request $request): View|RedirectResponse
    {
        if ($this->installation->ready()) {
            return to_route('dashboard');
        }

        if (! $this->canWrite($request) || $this->installation->interrupted() || ! $this->verified($request)) {
            return to_route('install');
        }

        return view('workspace.admin');
    }

    public function finish(Request $request): RedirectResponse
    {
        $this->authorizeWrite($request);
        $database = $this->verified($request);
        if (! $database) {
            return to_route('install')->withErrors(['connection' => 'Verify your database connection to continue.']);
        }

        $admin = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'regex:/\A[^\x00]{1,72}\z/', 'confirmed', Password::min(12)],
        ]);

        try {
            $this->installation->finish($database, $admin);
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (Throwable) {
            $request->session()->forget('workspace.verification');

            return to_route('install')
                ->withInput(Arr::except($database, 'password'))
                ->withErrors(['connection' => 'Temp Mail couldn’t finish workspace setup. Use an empty MySQL database and check that its user can create tables and write records.']);
        }

        $request->session()->forget('workspace');
        $request->session()->regenerate(true);

        return to_route('login')->with('status', 'Your workspace is ready. Sign in to open your dashboard.');
    }

    private function verified(Request $request): ?array
    {
        $verification = $request->session()->get('workspace.verification');
        if (! $verification
            || $verification['expires'] <= now()->timestamp
            || $verification['origin'] !== $request->getSchemeAndHttpHost()) {
            return null;
        }

        return decrypt($verification['database']);
    }

    private function authorizeWrite(Request $request): void
    {
        abort_unless($this->canWrite($request), 403);
        abort_if($this->installation->ready() || $this->installation->interrupted(), 409);
    }

    private function canWrite(Request $request): bool
    {
        if (app()->isLocal()) {
            return in_array($request->getHost(), ['localhost', '127.0.0.1'], true)
                && in_array($request->server('REMOTE_ADDR'), ['127.0.0.1', '::1'], true);
        }

        $owner = config('workspace.installer_user');

        return $request->isSecure()
            && is_string($owner)
            && $owner !== ''
            && $owner === $request->server('REMOTE_USER');
    }
}
