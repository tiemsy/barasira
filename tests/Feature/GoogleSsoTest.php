<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleSsoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_google_user_is_redirected_to_registration_without_account_creation(): void
    {
        $this->mockGoogleUser('new-user@example.com');

        $this->get('/api/auth/google/callback')
            ->assertRedirect(route('register'))
            ->assertSessionHas('error')
            ->assertSessionHas('google_registration.email', 'new-user@example.com');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_existing_google_user_is_authenticated_without_creating_another_account(): void
    {
        $user = User::factory()->client()->create(['email' => 'existing@example.com']);
        $this->mockGoogleUser($user->email);

        $this->get('/api/auth/google/callback')->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'google_id' => 'google-123']);
    }

    public function test_google_registration_is_completed_explicitly_without_password(): void
    {
        $this->mockGoogleUser('new-user@example.com');
        $this->get('/api/auth/google/callback')->assertRedirect(route('register'));

        $this->postJson('/api/register', [
            'first_name' => 'Aminata',
            'last_name' => 'Traoré',
            'email' => 'new-user@example.com',
            'phone' => '+22370000001',
            'role' => 'client',
        ])->assertCreated()->assertJsonPath('redirect', '/dashboard');

        $user = User::query()->where('email', 'new-user@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-123', $user->google_id);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertTrue($user->verified);
        $this->assertNull(session('google_registration'));
    }

    private function mockGoogleUser(string $email): void
    {
        $googleUser = (new GoogleUser)->map([
            'id' => 'google-123',
            'name' => 'Aminata Traoré',
            'email' => $email,
            'avatar' => 'https://example.com/avatar.jpg',
        ])->setRaw([
            'given_name' => 'Aminata',
            'family_name' => 'Traoré',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }
}
