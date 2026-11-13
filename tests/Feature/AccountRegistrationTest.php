<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\WorkspaceInstallation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountRegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const MEMBER = [
        'name' => 'Casey Member',
        'email' => 'member@example.test',
        'password' => 'Temp-mail-member-2026!',
        'password_confirmation' => 'Temp-mail-member-2026!',
    ];

    public function test_registration_requires_completed_workspace_setup(): void
    {
        $this->get(route('register'))->assertRedirect(route('install'));
        $this->withHeader('Origin', config('app.url'))
            ->post(route('register.store'), self::MEMBER)
            ->assertRedirect(route('install'));

        $this->assertGuest();
        $this->assertDatabaseEmpty('users');
    }

    public function test_a_guest_can_register_and_open_the_dashboard_as_a_member(): void
    {
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('ready')->andReturnTrue();
        $administrator = User::factory()->create();
        DB::table('workspace_settings')->insert([
            'id' => 1,
            'installation_token' => (string) Str::uuid(),
            'admin_id' => $administrator->id,
        ]);

        $this->get(route('login'))->assertSee('Sign up');
        $this->get(route('register'))->assertOk()->assertSee('Create account');
        $this->withHeader('Origin', config('app.url'))
            ->post(route('register.store'), [...self::MEMBER, 'role' => 'admin', 'is_admin' => true])
            ->assertRedirect(route('dashboard'));

        $member = User::query()->where('email', self::MEMBER['email'])->sole();
        $this->assertAuthenticatedAs($member);
        $this->assertTrue(Hash::check(self::MEMBER['password'], $member->password));
        $this->assertDatabaseHas('workspace_settings', ['id' => 1, 'admin_id' => $administrator->id]);
        $this->get(route('dashboard'))->assertOk()
            ->assertSee('aria-label="Account menu"', false)
            ->assertSee('Casey Member');
        $this->get(route('register'))->assertRedirect(route('dashboard'));
    }

    public function test_registration_rejects_a_duplicate_email_and_a_mismatched_password(): void
    {
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('ready')->andReturnTrue();
        $existingAccount = User::factory()->create(['email' => self::MEMBER['email']]);

        $this->withHeader('Origin', config('app.url'))
            ->from(route('register'))
            ->post(route('register.store'), [...self::MEMBER, 'password_confirmation' => 'a different password'])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password'])
            ->assertSessionMissing('_old_input.password')
            ->assertSessionMissing('_old_input.password_confirmation');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($existingAccount->password, $existingAccount->fresh()->password);
    }
}
