<?php

namespace App\Providers;

use App\Models\User;
use App\Support\WorkspaceInstallation;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(WorkspaceInstallation $installation, ConnectionFactory $factory): void
    {
        DB::extend(WorkspaceInstallation::CONNECTION, fn () => $factory->make(
            $installation->connectionConfig(),
            WorkspaceInstallation::CONNECTION,
        ));

        if ($installation->ready()) {
            DB::setDefaultConnection(WorkspaceInstallation::CONNECTION);
        }

        Gate::define('administer-workspace', fn (User $user): bool => DB::table('workspace_settings')
            ->where('id', 1)
            ->where('admin_id', $user->getKey())
            ->exists());

        ResetPassword::createUrlUsing(function ($user, string $token): string {
            $origin = app()->isLocal() ? request()->getSchemeAndHttpHost() : rtrim(config('app.url'), '/');

            return $origin.route('password.reset', ['token' => $token, 'email' => $user->email], absolute: false);
        });
    }
}
