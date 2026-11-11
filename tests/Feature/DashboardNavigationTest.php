<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\WorkspaceInstallation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DashboardNavigationTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_only_the_workspace_administrator_sees_the_admin_menu(bool $isAdministrator): void
    {
        $this->partialMock(WorkspaceInstallation::class)->shouldReceive('ready')->andReturnTrue();
        $administrator = User::factory()->create();
        DB::table('workspace_settings')->insert([
            'id' => 1,
            'installation_token' => (string) Str::uuid(),
            'admin_id' => $administrator->id,
        ]);

        $user = $isAdministrator ? $administrator : User::factory()->create();
        $response = $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Sign out');

        if ($isAdministrator) {
            $response->assertSee('aria-label="Admin menu"', false)
                ->assertSee('aria-current="page"', false)
                ->assertDontSee('href="#inbox"', false);
        } else {
            $response->assertDontSee('aria-label="Admin menu"', false);
        }
    }
}
