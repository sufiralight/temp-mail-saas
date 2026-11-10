<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\WorkspaceInstallation;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use PDOException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class WorkspaceSetupTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const CONNECTION = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'temp_mail_test',
        'username' => 'test_user',
        'password' => 'connection-secret',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeader('Sec-Fetch-Site', 'same-origin');
    }

    #[TestWith(['http://localhost:8000'])]
    #[TestWith(['http://127.0.0.1:8000'])]
    public function test_local_setup_starts_with_connection_fields_and_keeps_its_origin(string $origin): void
    {
        $this->app->instance('env', 'local');
        $this->partialMock(WorkspaceInstallation::class)
            ->shouldReceive('verify')->once()->with(self::CONNECTION);

        $this->get($origin.'/install')
            ->assertOk()
            ->assertSee('Verify connection')
            ->assertSee('You can leave this blank if your local MySQL user has no password.')
            ->assertDontSee('Owner credential')
            ->assertDontSee('Continue to admin setup')
            ->assertDontSee('hosting panel');

        $this->get($origin.'/install/admin')->assertRedirect($origin.'/install');
        $this->withHeader('Origin', $origin)->post($origin.'/install/verify', self::CONNECTION)
            ->assertRedirect($origin.'/install');

        $this->get($origin.'/install')
            ->assertSee('Database connected')
            ->assertSee('temp_mail_test')
            ->assertSee('Continue to admin setup')
            ->assertDontSee('connection-secret')
            ->assertDontSee('test_user');

        $this->get($origin.'/install/admin')->assertSee('Create your workspace admin account');
        $this->post($origin.'/install/edit')->assertRedirect($origin.'/install')
            ->assertSessionMissing('workspace.verification')
            ->assertSessionHasInput('database', 'temp_mail_test')
            ->assertSessionMissing('_old_input.password');
        $this->get($origin.'/install/admin')->assertRedirect($origin.'/install');
    }

    public function test_connection_failure_preserves_only_safe_inputs_and_clears_verification(): void
    {
        $this->app->instance('env', 'local');
        $origin = 'http://localhost:8000';
        $installation = $this->partialMock(WorkspaceInstallation::class);
        $installation->shouldReceive('verify')->once()->with(self::CONNECTION);
        $installation->shouldReceive('verify')->once()->with([...self::CONNECTION, 'host' => 'broken.test'])
            ->andThrow(new PDOException('Connection details must stay private.'));

        $this->withHeader('Origin', $origin)->post($origin.'/install/verify', self::CONNECTION);
        $this->post($origin.'/install/verify', [...self::CONNECTION, 'host' => 'broken.test'])
            ->assertRedirect($origin.'/install')
            ->assertSessionHasErrors(['connection' => 'Temp Mail couldn’t connect to MySQL. Check the host, port, and database credentials, then try again.'])
            ->assertSessionHasInput('host', 'broken.test')
            ->assertSessionHasInput('username', 'test_user')
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('workspace.verification');
    }

    public function test_local_posts_reject_cross_origin_requests_and_verification_cannot_move_to_the_other_alias(): void
    {
        $this->app->instance('env', 'local');
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('verify')->once();
        $localhost = 'http://localhost:8000';
        $loopback = 'http://127.0.0.1:8000';

        $this->withHeader('Origin', $localhost)->post($loopback.'/install/verify', self::CONNECTION)
            ->assertForbidden();
        $this->post($localhost.'/install/verify', self::CONNECTION)->assertRedirect($localhost.'/install');
        $this->get($loopback.'/install/admin')->assertRedirect($loopback.'/install');
    }

    public function test_expired_verification_cannot_create_an_admin(): void
    {
        $this->app->instance('env', 'local');
        $origin = 'http://localhost:8000';
        $installation = $this->partialMock(WorkspaceInstallation::class);
        $installation->shouldReceive('verify')->once();
        $installation->shouldReceive('finish')->never();

        $this->freezeTime();
        $this->withHeader('Origin', $origin)->post($origin.'/install/verify', self::CONNECTION);
        $this->travel(15)->minutes();

        $this->post($origin.'/install/admin')
            ->assertRedirect($origin.'/install')
            ->assertSessionHasErrors('connection');
    }

    public function test_production_writes_require_https_and_a_server_authenticated_identity(): void
    {
        $this->app->instance('env', 'production');
        config(['workspace.installer_user' => 'hosting-owner']);
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('verify')->once()->with(self::CONNECTION);
        $origin = 'https://workspace.example.test';

        $this->get($origin.'/install')->assertOk()->assertSee('Workspace setup is locked.');
        $this->withHeaders(['Origin' => $origin, 'Remote-User' => 'hosting-owner'])
            ->post($origin.'/install/verify', self::CONNECTION)->assertForbidden();
        $this->post($origin.'/install/edit')->assertForbidden();
        $this->post($origin.'/install/admin')->assertForbidden();
        $this->withServerVariables(['REMOTE_USER' => 'hosting-owner'])
            ->withHeader('Origin', 'http://workspace.example.test')
            ->post('http://workspace.example.test/install/verify', self::CONNECTION)->assertForbidden();

        $this->get($origin.'/install')
            ->assertSee('In your hosting panel')
            ->assertSee('Enter the password for the MySQL user assigned to this database.');
        $this->withHeader('Origin', $origin)
            ->post($origin.'/install/verify', self::CONNECTION)->assertRedirect($origin.'/install');
    }

    public function test_password_recovery_uses_a_one_time_link_and_the_local_origin(): void
    {
        $this->app->instance('env', 'local');
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('ready')->andReturnTrue();
        Notification::fake();
        $user = User::factory()->create();
        $origin = 'http://127.0.0.1:8000';

        $this->withHeader('Origin', $origin)->post($origin.'/forgot-password', ['email' => $user->email])
            ->assertRedirect($origin.'/forgot-password')
            ->assertSessionHas('status', 'If an account matches that email address, you’ll receive a password reset link.');
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user, $origin): bool {
            return $notification->toMail($user)->actionUrl === $origin.'/reset-password/'.$notification->token.'?email='.rawurlencode($user->email);
        });

        $token = Notification::sent($user, ResetPassword::class)->sole()->token;
        $credentials = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'A-new-workspace-password!',
            'password_confirmation' => 'A-new-workspace-password!',
        ];

        $this->post($origin.'/reset-password', $credentials)->assertRedirect($origin.'/login');
        $this->assertFalse(Password::tokenExists($user, $token));
        $this->post($origin.'/reset-password', $credentials)->assertSessionHasErrors('email');
        $this->post($origin.'/login', ['email' => $user->email, 'password' => $credentials['password']])
            ->assertRedirect($origin.'/dashboard');
        $this->get($origin.'/dashboard')->assertSee('Your inbox isn’t available yet.');
    }
}
