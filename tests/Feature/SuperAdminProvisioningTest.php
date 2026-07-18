<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_and_updates_a_superadmin_idempotently(): void
    {
        config()->set('superadmin', [
            'email' => 'root@barasira.test',
            'password' => 'a-secure-password',
            'phone' => '+22370000000',
            'first_name' => 'Root',
            'last_name' => 'Barasira',
        ]);

        $this->artisan('superadmin:ensure')->assertSuccessful();
        $this->artisan('superadmin:ensure')->assertSuccessful();

        $this->assertSame(1, User::withTrashed()->where('email', 'root@barasira.test')->count());
        $superAdmin = User::where('email', 'root@barasira.test')->firstOrFail();
        $this->assertSame('superadmin', $superAdmin->role);
        $this->assertTrue($superAdmin->verified);
        $this->assertNotNull($superAdmin->email_verified_at);
        $this->assertTrue(Hash::check('a-secure-password', $superAdmin->password));
    }

    public function test_command_fails_when_production_credentials_are_missing(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config()->set('superadmin.email', 'root@barasira.test');
        config()->set('superadmin.password', null);

        $this->artisan('superadmin:ensure')
            ->expectsOutput('SUPERADMIN_PASSWORD est obligatoire et doit contenir au moins 12 caractères.')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'root@barasira.test']);
    }

    public function test_command_reports_an_empty_email_separately(): void
    {
        config()->set('superadmin.email', '');
        config()->set('superadmin.password', 'a-secure-password');

        $this->artisan('superadmin:ensure')
            ->expectsOutput('SUPERADMIN_EMAIL est absent. Définissez cette variable puis régénérez le cache de configuration.')
            ->assertFailed();
    }

    public function test_command_restores_a_deleted_superadmin(): void
    {
        config()->set('superadmin', [
            'email' => 'root@barasira.test',
            'password' => 'a-secure-password',
            'phone' => '+22370000000',
            'first_name' => 'Root',
            'last_name' => 'Barasira',
        ]);
        $user = User::factory()->create(['email' => 'root@barasira.test']);
        $user->delete();

        $this->artisan('superadmin:ensure')->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'root@barasira.test',
            'role' => 'superadmin',
            'deleted_at' => null,
        ]);
    }
}
