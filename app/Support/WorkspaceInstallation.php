<?php

namespace App\Support;

use App\Models\User;
use Closure;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class WorkspaceInstallation
{
    public const CONNECTION = 'workspace_mysql';

    public function ready(): bool
    {
        return $this->state()['ready'] ?? false;
    }

    public function interrupted(): bool
    {
        return ! $this->ready() && is_file($this->path());
    }

    public function connectionConfig(): array
    {
        $state = $this->state();

        if (! ($state['ready'] ?? false)) {
            throw new RuntimeException('Workspace setup has not finished.');
        }

        return $this->mysqlConfig($state['database']);
    }

    public function verify(#[\SensitiveParameter] array $database): void
    {
        DB::connectUsing('workspace_probe', $this->mysqlConfig($database), true)->getPdo()->query('select 1');
    }

    public function finish(#[\SensitiveParameter] array $database, #[\SensitiveParameter] array $admin): void
    {
        $this->locked(function () use ($database, $admin): void {
            abort_if(is_file($this->path()), 409);

            $connection = $this->candidate($database);
            if ($connection->getSchemaBuilder()->hasTable('users') && $connection->table('users')->exists()) {
                throw new RuntimeException('The database already contains accounts.');
            }

            $state = ['ready' => false, 'token' => (string) Str::uuid(), 'database' => $database];
            $this->write($state);

            try {
                if (Artisan::call('migrate', ['--database' => 'workspace_candidate', '--force' => true]) !== 0) {
                    throw new RuntimeException('Workspace migrations did not finish.');
                }
            } catch (Throwable $exception) {
                $this->clearInterrupted();

                throw $exception;
            }

            $connection->transaction(function () use ($connection, $state, $admin): void {
                $user = User::on('workspace_candidate')->create($admin);
                $connection->table('workspace_settings')->insert([
                    'id' => 1,
                    'installation_token' => $state['token'],
                    'admin_id' => $user->getKey(),
                ]);
            });

            $this->write([...$state, 'ready' => true]);
        });
    }

    public function recover(): string
    {
        return $this->locked(function (): string {
            $state = $this->state();

            if (! $state || $state['ready']) {
                return 'There is no interrupted workspace setup.';
            }

            $connection = $this->candidate($state['database']);
            $schema = $connection->getSchemaBuilder();
            $settings = $schema->hasTable('workspace_settings')
                ? $connection->table('workspace_settings')->where('id', 1)->first()
                : null;

            if ($settings?->installation_token === $state['token']
                && $schema->hasTable('users')
                && $connection->table('users')->where('id', $settings->admin_id)->exists()) {
                $this->write([...$state, 'ready' => true]);

                return 'Workspace setup recovered. Sign in with the workspace admin account.';
            }

            if ($settings || ($schema->hasTable('users') && $connection->table('users')->exists())) {
                throw new RuntimeException('The database contains an existing workspace or accounts.');
            }

            $this->clearInterrupted();

            return 'The interrupted setup was cleared. Open /install and use an empty MySQL database to try again.';
        });
    }

    private function clearInterrupted(): void
    {
        if (! unlink($this->path())) {
            throw new RuntimeException('The interrupted setup could not be cleared.');
        }
    }

    private function candidate(array $database): Connection
    {
        config(['database.connections.workspace_candidate' => $this->mysqlConfig($database)]);
        DB::purge('workspace_candidate');

        return DB::connection('workspace_candidate');
    }

    private function mysqlConfig(#[\SensitiveParameter] array $database): array
    {
        return [
            'driver' => 'mysql',
            'host' => $database['host'],
            'port' => $database['port'],
            'database' => $database['database'],
            'username' => $database['username'],
            'password' => $database['password'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
            'use_db_after_connecting' => false,
            'options' => [\PDO::ATTR_TIMEOUT => 5],
        ];
    }

    private function path(): string
    {
        return config('workspace.installation_file');
    }

    private function state(): ?array
    {
        $path = $this->path();
        clearstatcache(true, $path);

        if (! is_file($path)) {
            return null;
        }

        return Crypt::decrypt(File::get($path));
    }

    private function write(#[\SensitiveParameter] array $state): void
    {
        $path = $this->path();
        $contents = Crypt::encrypt($state);
        $temporary = tempnam(dirname($path), 'workspace-');
        if ($temporary === false) {
            throw new RuntimeException('Workspace setup storage is unavailable.');
        }

        try {
            if (File::put($temporary, $contents) !== strlen($contents) || ! rename($temporary, $path)) {
                throw new RuntimeException('Workspace setup state could not be saved.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    private function locked(Closure $operation): mixed
    {
        File::ensureDirectoryExists(dirname($this->path()), 0700, true);
        $lock = fopen($this->path().'.lock', 'c');

        if ($lock === false) {
            throw new RuntimeException('Workspace setup storage is unavailable.');
        }

        try {
            abort_unless(flock($lock, LOCK_EX | LOCK_NB), 409);

            return $operation();
        } finally {
            fclose($lock);
        }
    }
}
