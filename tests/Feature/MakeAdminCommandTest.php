<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_new_admin_user(): void
    {
        $this->artisan('station:make-admin', ['email' => 'admin@fissible.dev'])
            ->expectsQuestion('Password for new user', 'secret-password')
            ->expectsQuestion('Name', 'Admin')
            ->assertExitCode(0);

        $user = User::where('email', 'admin@fissible.dev')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_platform_admin);
        $this->assertEquals('Admin', $user->name);
    }

    public function test_promotes_existing_user(): void
    {
        User::factory()->create([
            'email' => 'existing@fissible.dev',
            'is_platform_admin' => false,
        ]);

        $this->artisan('station:make-admin', ['email' => 'existing@fissible.dev'])
            ->assertExitCode(0);

        $user = User::where('email', 'existing@fissible.dev')->first();
        $this->assertTrue($user->is_platform_admin);
    }
}
